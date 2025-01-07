<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AcquisitionSystemRepository;
use App\Repository\RoomRepository;

use App\Service\ApiService;

class DiagnosticController extends AbstractController
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function index(AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        $AcquisitionSystems = $acquisitionSystemRepository->findInstalledSystems();

        return $this->render('diagnostic/index.html.twig', [
            'AS' => $AcquisitionSystems,
        ]);
    }

    #[Route('/diagnostic/{id}', name: 'app_diagnostic_details')]
    public function details(AcquisitionSystemRepository $acquisitionSystemRepository, int $id, ApiService $apiService, RoomRepository $roomRepository): Response
    {
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

        $date1 = (new \DateTime('2024-12-01'))->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Fonction pour récupérer les données d'intervalle pour chaque type
        $getCapturesByInterval = function(string $type) use ($apiService, $date1, $date2, $dbname) {
            try {
                return $apiService->getCapturesByInterval($date1, $date2, $type, 1, $dbname);
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        };

        $dataTemp = $getCapturesByInterval('temp');
        $dataHum = $getCapturesByInterval('hum');
        $dataCo2 = $getCapturesByInterval('co2');

        return $this->render('diagnostic/diagnostic.html.twig', [
            'as' => $AS,
            'dataTemp' => $dataTemp,
            'dataHum' => $dataHum,
            'dataCo2' => $dataCo2,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
        ]);
    }
}
