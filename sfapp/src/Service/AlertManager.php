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
        $this->entityManager = $entityManager;
        $this->alertRepository = $alertRepository;
        $this->roomRepository = $roomRepository;

    }

    public function checkAndCreateAlerts(): void
    {
        // Récupère toutes les salles
        $rooms = $this->roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            $acquisitionSystem = $room->getIdAS();

            // Vérifie si le système d'acquisition existe
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
        // Récupérer les alertes actives
        $activeAlerts = $this->alertRepository->findActiveAlertsByRoom($room);

        // Vérifications
        $this->processAlertTemp($room, $temperature, $activeAlerts);
        $this->processAlertHum($room, $humidity, $temperature, $activeAlerts);
        $this->processAlertCo2($room, $co2, $activeAlerts);

        $this->entityManager->flush();
    }

    /**
     * @param Alert[] $activeAlerts Un tableau d'objets Alert.
     */
    private function processAlertTemp(Room $room, ?float $value, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::temp);


        if ($value <17 || $value>21) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));                $alert->setType(AlertType::temp);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte: La température n'est plus dans les seuils. Les fenêtres doivent être ouvertes. " );
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * @param Alert[] $activeAlerts Un tableau d'objets Alert.
     */
    private function processAlertHum(Room $room, ?float $valueHum, ?float $valueTemp, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::hum);

        if ($valueHum > 70 and $valueTemp >= 20.0) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
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
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * @param Alert[] $activeAlerts Un tableau d'objets Alert.
     */
    private function processAlertCo2(Room $room, ?int $value, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::co2);

        if ( $value < 400 || $value > 1000 ) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
            if (!$activeAlert) {
                $alert = new Alert();
                $date = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
                $alert->setDateBegin($date);
                $alert->setType(AlertType::co2);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte: Le CO2 n'est plus dans les seuils prévus. Les fenêtres doivent être ouvertes" );
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->setDateEnd(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        }
    }

    /**
     * @param Alert[] $activeAlerts
     * @return Alert|null
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
        $alerts = $this->alertRepository->findAlertsByRoom($room);
        foreach ($alerts as $alert) {
            $this->entityManager->remove($alert);
        }
        $this->entityManager->flush();
    }
}