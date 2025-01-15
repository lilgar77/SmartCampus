<?php

namespace App\Service;

use App\Entity\Alert;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AlertRepository;
use App\Model\AlertType;
use App\Repository\RoomRepository;

class AlertManager
{
    private EntityManagerInterface $entityManager;
    private AlertRepository $alertRepository;
    private RoomRepository $roomRepository;

    public function __construct(EntityManagerInterface $entityManager, AlertRepository $alertRepository, RoomRepository $roomRepository)
    {
        // Initialize dependencies
        $this->entityManager = $entityManager;
        $this->alertRepository = $alertRepository;
        $this->roomRepository = $roomRepository;
    }

    public function checkAndCreateAlerts(): void
    {
        // Retrieve all rooms
        $rooms = $this->roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            $acquisitionSystem = $room->getIdAS();

            // Check if the acquisition system exists
            if ($acquisitionSystem !== null) {
                $this->checkThresholds(
                    $room,
                    $acquisitionSystem->getTemperature(),
                    $acquisitionSystem->getHumidity(),
                    $acquisitionSystem->getCO2()
                );
            }
        }
    }

    public function checkThresholds(Room $room, ?float $temperature, ?float $humidity, ?int $co2): void
    {
        // Retrieve active alerts
        $activeAlerts = $this->alertRepository->findActiveAlertsByRoom($room);

        // Check thresholds
        $this->processAlertTemp($room, $temperature, $activeAlerts);
        $this->processAlertHum($room, $humidity, $temperature, $activeAlerts);
        $this->processAlertCo2($room, $co2, $activeAlerts);

        $this->entityManager->flush();
    }

    /**
     * Handle temperature alerts.
     *
     * @param Alert[] $activeAlerts An array of Alert objects.
     */
    private function processAlertTemp(Room $room, ?float $value, array $activeAlerts): void
    {
        // Check if an active alert already exists for this type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::temp);

        if ($value < 17 || $value > 21) {
            // If thresholds are exceeded and no active alert exists, create one
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                $alert->setType(AlertType::temp);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte: La température n'est plus dans les seuils. Les fenêtres doivent être ouvertes. " );
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // If thresholds are respected and an alert is active, close it
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * Handle humidity alerts.
     *
     * @param Alert[] $activeAlerts An array of Alert objects.
     */
    private function processAlertHum(Room $room, ?float $valueHum, ?float $valueTemp, array $activeAlerts): void
    {
        // Check if an active alert already exists for this type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::hum);

        if ($valueHum > 70 && $valueTemp >= 20.0) {
            // If thresholds are exceeded and no active alert exists, create one
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                $alert->setType(AlertType::hum);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte: L'humidité est trop élévée et les risques de moisissures sont là. Les fenêtres doivent être ouverts. " );
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // If thresholds are respected and an alert is active, close it
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * Handle CO2 alerts.
     *
     * @param Alert[] $activeAlerts An array of Alert objects.
     */
    private function processAlertCo2(Room $room, ?int $value, array $activeAlerts): void
    {
        // Check if an active alert already exists for this type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::co2);

        if ($value < 400 || $value > 1000) {
            // If thresholds are exceeded and no active alert exists, create one
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                $alert->setType(AlertType::co2);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte: Le CO2 n'est plus dans les seuils prévus. Les fenêtres doivent être ouvertes" );
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // If thresholds are respected and an alert is active, close it
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * Find an active alert of a specific type.
     *
     * @param Alert[] $activeAlerts An array of Alert objects.
     * @return Alert|null Returns the active alert or null if none exists.
     */
    private function findActiveAlert(array $activeAlerts, AlertType $type): ?Alert
    {
        foreach ($activeAlerts as $alert) {
            if ($alert instanceof Alert && $alert->getType() === $type) {
                return $alert;
            }
        }

        return null;
    }

    public function deleteAlerts(Room $room): void
    {
        // Delete all alerts for the given room
        $alerts = $this->alertRepository->findAlertsByRoom($room);
        foreach ($alerts as $alert) {
            if ($alert instanceof Alert) {
                $this->entityManager->remove($alert);
            }
        }
        $this->entityManager->flush();
    }
}