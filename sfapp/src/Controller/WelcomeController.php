<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\SearchRoomFormType;
use App\Service\AlertManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

// use services api
use App\Service\ApiService;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function index(Request $request, RoomRepository $roomRepository, ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
            'include_name' => false,
        ]);

        $form->handleRequest($request);

        $rooms = $roomRepository->findRoomWithAsDefault();
        if ($form->isSubmitted() && $form->isValid()) {
            $rooms = $roomRepository->findRoomWithAs($room);
        } else {
            $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
            $alertManager->checkAndCreateAlerts();
        }

        $roomsWithLastCaptures = array_map(function ($room) use ($apiService, $roomRepository) {
            $roomName = $room->getName();

            // Vérifier que le nom de la salle est une chaîne de caractères
            if (!is_string($roomName)) {
                throw $this->createNotFoundException('Le nom de la salle est introuvable ou invalide.');
            }

            // Récupérer les informations de la salle dans la base de données
            $roomDbInfo = $roomRepository->getRoomDb($roomName);
            $dbname = $roomDbInfo['dbname'] ?? null;

            // Si le nom de la base de données n'est pas valide, retourner les valeurs par défaut
            if (!is_string($dbname)) {
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

            // Récupérer les dernières captures de température, humidité et CO2
            $lastCaptureTemp = $apiService->getLastCapture('temp', $dbname);
            $tempValue = (isset($lastCaptureTemp[0]['valeur']) && is_numeric($lastCaptureTemp[0]['valeur']))
                ? (float) $lastCaptureTemp[0]['valeur']
                : null;

            $lastCaptureHum = $apiService->getLastCapture('hum', $dbname);
            $humValue = (isset($lastCaptureHum[0]['valeur']) && is_numeric($lastCaptureHum[0]['valeur']))
                ? (float) $lastCaptureHum[0]['valeur']
                : null;

            $lastCaptureCo2 = $apiService->getLastCapture('co2', $dbname);
            $co2Value = (isset($lastCaptureCo2[0]['valeur']) && is_numeric($lastCaptureCo2[0]['valeur']))
                ? (float) $lastCaptureCo2[0]['valeur']
                : null;

            // Retourner les informations de la salle avec les dernières captures
            return [
                'room' => $room,
                'dbname' => $dbname,
                'lastCaptures' => [
                    'temp' => $tempValue,
                    'hum' => $humValue,
                    'co2' => $co2Value,
                ],
            ];
        }, $rooms);

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'room'  => $form->createView(),
            'rooms' => $rooms,
            'roomsWithLastCaptures' => $roomsWithLastCaptures,
        ]);
    }

    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id, ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        $room = $roomRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        // Récupération de la base de données de la salle
        $roomName = $room->getName();

        if (!is_string($roomName)) {
            throw $this->createNotFoundException('Le nom de la salle est introuvable ou invalide.');
        }

        $roomDbInfo = $roomRepository->getRoomDb($roomName);
        $dbname = $roomDbInfo['dbname'] ?? null;

        // Récupérer la dernière capture pour chaque type
        $getLastCapture = function(string $type) use ($apiService, $dbname) {
            if (!is_string($dbname)) {
                return null;
            }
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Fonction pour récupérer les captures par intervalle
        $getCapturesByInterval = function(string $type) use ($apiService, $date1, $date2, $dbname) {
            if (!is_string($dbname)) {
                return ['error' => 'Database name is invalid'];
            }
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