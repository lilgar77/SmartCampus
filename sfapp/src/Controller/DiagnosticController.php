<?php

namespace App\Controller;

use App\Service\AlertManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AcquisitionSystemRepository;
use App\Repository\RoomRepository;
use App\Repository\AlertRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiService;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class DiagnosticController extends AbstractController
{

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function index(AcquisitionSystemRepository $acquisitionSystemRepository,ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager, RoomRepository $roomRepository): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();
        $AcquisitionSystems = $acquisitionSystemRepository->findInstalledSystems();

        return $this->render('diagnostic/index.html.twig', [
            'AS' => $AcquisitionSystems,
        ]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/diagnostic/{id}', name: 'app_diagnostic_details')]
    public function details(AcquisitionSystemRepository $acquisitionSystemRepository, int $id, RoomRepository $roomRepository,ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager, AlertRepository $alertRepository, Request $request): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        $AS = $acquisitionSystemRepository->find($id);
        if (!$AS) {
            throw $this->createNotFoundException('SA non trouvée');
        }

        $room = $AS->getRoom();
        if (!$room) {
            throw $this->createNotFoundException('La salle n’a pas été trouvée.');
        }

        $name = $room->getName();
        if (!$name) {
            throw $this->createNotFoundException('Le nom de la salle est introuvable.');
        }

        $roomDb = $roomRepository->getRoomDb($name);
        if (!isset($roomDb['dbname']) || !is_string($roomDb['dbname'])) {
            throw $this->createNotFoundException('La base de données de la salle est introuvable ou invalide.');
        }

        $dbname = $roomDb['dbname'];

        $getLastCapture = function (string $type) use ($apiService, $dbname) {
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        // Gestion de l'intervalle
        $interval = $request->query->get('interval', '1d');
        $date2 = (new \DateTime('now'))->format("Y-m-d");
        switch ($interval) {
            case '1d':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1D'))->format("Y-m-d");
                break;
            case '1w':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1W'))->format("Y-m-d");
                break;
            case '1m':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1M'))->format("Y-m-d");
                break;
            case '1y':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1Y'))->format("Y-m-d");
                break;
            default:
                $date1 = (new \DateTime('2024-12-01'))->format("Y-m-d");
                break;
        }

        $getCapturesByInterval = function (string $type) use ($apiService, $date1, $date2, $dbname) {
            try {
                return $apiService->getCapturesByInterval($date1, $date2, $type, 1, $dbname);
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        };

        $dataTemp = $getCapturesByInterval('temp');
        $dataHum = $getCapturesByInterval('hum');
        $dataCo2 = $getCapturesByInterval('co2');

        $alerts = $alertRepository->findLastFiveAlertsByRoom($room);

        return $this->render('diagnostic/diagnostic.html.twig', [
            'as' => $AS,
            'dataTemp' => $dataTemp,
            'dataHum' => $dataHum,
            'dataCo2' => $dataCo2,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
            'Alerts' => $alerts,
        ]);
    }
}
