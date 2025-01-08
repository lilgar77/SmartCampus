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
use App\Form\DiagnosticFilterFormType;

class DiagnosticController extends AbstractController
{

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

    #[Route('/diagnostic/{id}', name: 'app_diagnostic_details')]
    public function details(AcquisitionSystemRepository $acquisitionSystemRepository, int $id, RoomRepository $roomRepository,ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager, AlertRepository $alertRepository, Request $request): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        $AS = $acquisitionSystemRepository->find($id);
        $room = $roomRepository->find($AS->getRoom());
        $dbname = $roomRepository->getRoomDb($room->getName())['dbname'];
        if (!$AS) {
            throw $this->createNotFoundException('SA non trouvée');
        }

        $getLastCapture = function(string $type) use ($apiService, $dbname) {
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        $form = $this->createForm(DiagnosticFilterFormType::class);
        $form->handleRequest($request);

        // Définition de l'intervalle par défaut
        $interval = '1d'; // Valeur par défaut
        if ($form->isSubmitted() && $form->isValid()) {
            $interval = $form->get('interval')->getData();
        }

        // Calcul des dates en fonction de l'intervalle
        $date2 = (new \DateTime('now'))->format('Y-m-d');
        switch ($interval) {
            case '1d':
                $date1 = (new \DateTime('yesterday'))->format('Y-m-d');
                break;
            case '1w':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1W'))->format('Y-m-d');
                break;
            case '1m':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1M'))->format('Y-m-d');
                break;
            case '1y':
                $date1 = (new \DateTime('now'))->sub(new \DateInterval('P1Y'))->format('Y-m-d');
                break;
            default:
                $date1 = (new \DateTime('2024-12-01'))->format('Y-m-d');
                break;
        }

        // Fonction pour récupérer les données d'intervalle pour chaque type
        $getCapturesByInterval = function(string $type) use ($apiService, $date1, $date2, $dbname) {
            try {
                return $apiService->getCapturesByInterval(
                    $date1,
                    $date2,
                    $type,
                    1,
                    $dbname
                );            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        };

        $dataTemp = $getCapturesByInterval('temp');
        $dataHum = $getCapturesByInterval('hum');
        $dataCo2 = $getCapturesByInterval('co2');

        return $this->render('diagnostic/diagnostic.html.twig', [
            'as' => $AS,
            'form' => $form->createView(),
            'dataTemp' => $dataTemp,
            'dataHum' => $dataHum,
            'dataCo2' => $dataCo2,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
            'Alerts' => $alertRepository->findLastFiveAlertsByRoom($room),
        ]);
    }
}
