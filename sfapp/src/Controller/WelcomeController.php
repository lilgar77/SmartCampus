<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;

// use services api
use App\Service\ApiService;

class WelcomeController extends AbstractController
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    #[Route('/', name: 'app_welcome')]
    public function index(RoomRepository $roomRepository, ApiService $apiService): Response
    {
        $rooms = $roomRepository->findRoomWithAs();

        $roomsWithLastCaptures = array_map(function ($room) use ($apiService, $roomRepository) {
            $roomDbInfo = $roomRepository->getRoomDb($room->getName());
            $dbname = $roomDbInfo['dbname'] ?? null;

            if (!$dbname) {
                return [
                    'room' => $room,
                    'dbname' => null,
                    'lastCaptures' => [
                        'temp' => null,
                        'hum' => null,
                        'co2' => null,
                    ],
                ];
            }

            return [
                'room' => $room,
                'dbname' => $dbname,
                'lastCaptures' => [
                    'temp' => $apiService->getLastCapture('temp', $dbname)[0]['valeur'] ?? null,
                    'hum' => $apiService->getLastCapture('hum', $dbname)[0]['valeur'] ?? null,
                    'co2' => $apiService->getLastCapture('co2', $dbname)[0]['valeur'] ?? null,
                ],
            ];
        }, $rooms);

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'roomsWithLastCaptures' => $roomsWithLastCaptures,
        ]);
    }


    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id, ApiService $apiService): Response
    {
        $room = $roomRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

       //récupération de la base de donnée de la salle
        $dbname = $roomRepository->getRoomDb($room->getName())['dbname'];

        $getLastCapture = function(string $type) use ($apiService, $dbname) {
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        $date1 = (new \DateTime('2024-12-01'))->format('Y-m-d');
        $date2 = (new \DateTime('2025-01-31'))->format('Y-m-d');

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

        return $this->render('welcome/detail.html.twig', [
            'room' => $room,
            'dataTemp' => $dataTemp,
            'dataHum' => $dataHum,
            'dataCo2' => $dataCo2,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
        ]);
    }
}