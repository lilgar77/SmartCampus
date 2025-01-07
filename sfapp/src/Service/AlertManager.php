<?php

namespace App\Service;

use App\Entity\Alert;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AlertRepository;
use App\Model\AlertType;
use App\Repository\RoomRepository;
use Psr\Log\LoggerInterface; // Interface standard pour le logger


class AlertManager
{
    private EntityManagerInterface $entityManager;
    private AlertRepository $alertRepository;
    private RoomRepository $roomRepository;
    private LoggerInterface $logger;



    public function __construct(EntityManagerInterface $entityManager, AlertRepository $alertRepository, RoomRepository $roomRepository,LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->alertRepository = $alertRepository;
        $this->roomRepository = $roomRepository;
        $this->logger = $logger; // Stockage du logger

    }

    public function checkAndCreateAlerts(): void
    {
        $this->logger->info('Vérification des alertes lancée.');

        // Récupère toutes les salles
        $rooms = $this->roomRepository->findRoomWithAsInstalled();

        foreach ($rooms as $room) {
            $this->checkThresholds($room, $room->getIdAS()->getTemperature(), $room->getIdAS()->getHumidity(), $room->getIdAS()->getCO2());
        }

        $this->logger->info('Vérification des alertes terminée.');
    }

    public function checkThresholds(Room $room, float $temperature, float $humidity, int $co2): void
    {
        // Récupérer les alertes actives
        $activeAlerts = $this->alertRepository->findActiveAlertsByRoom($room);

        // Vérifications
        $this->processAlertTemp($room, 'temp', $temperature, $activeAlerts);
        $this->processAlertHum($room, 'hum', $humidity,$temperature, $activeAlerts);
        $this->processAlertCo2($room, 'co2', $co2, $activeAlerts);

        $this->entityManager->flush();
    }

    private function processAlertTemp(Room $room, string $typeName, float|int $value, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::temp);

        if ($value < 17 || $value > 21) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));                $alert->setType(AlertType::temp);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte " . $typeName);
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->closeAlert();
        }
    }

    private function processAlertHum(Room $room, string $typeName, float|int $valueHum, float|int $valueTemp, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::hum);

        if ($valueHum > 70  && $valueTemp > 20) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                $alert->setType(AlertType::hum);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte " . $typeName);
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->closeAlert();
        }
    }

    private function processAlertCo2(Room $room, string $typeName, float|int $value, array $activeAlerts): void
    {
        // Vérifie si une alerte est déjà active pour ce type
        $activeAlert = $this->findActiveAlert($activeAlerts, AlertType::co2);

        if ($value > 200) {
            // Si les seuils sont dépassés et qu'il n'y a pas d'alerte active, on en crée une
            if (!$activeAlert) {
                $alert = new Alert();
                $alert->setDateBegin(new \DateTime('now', new \DateTimeZone('Europe/Paris')));                $alert->setType(AlertType::co2);
                $alert->setIdRoom($room);
                $alert->setIdSA($room->getIdAS());
                $alert->setDescription("Alerte " . $typeName);
                $this->entityManager->persist($alert);
            }
        } elseif ($activeAlert) {
            // Si les seuils sont respectés et qu'une alerte est active, on la clôture
            $activeAlert->closeAlert();
        }
    }

    private function findActiveAlert(array $activeAlerts, AlertType $type): ?Alert
    {
        foreach ($activeAlerts as $alert) {
            if ($alert->getType() === $type) {
                return $alert;
            }
        }

        return null;
    }
}