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
        // Update the last captures for all rooms
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Fetch the room by its ID
        $room = $roomRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        // Retrieve the room's database information
        $roomName = $room->getName();

        if (!is_string($roomName)) {
            throw $this->createNotFoundException('Room name is invalid or not found.');
        }

        $roomDbInfo = $roomRepository->getRoomDb($roomName);
        $dbname = $roomDbInfo['dbname'] ?? null;

        // Get the last capture for each type of data (temperature, humidity, CO2)
        $getLastCapture = function(string $type) use ($apiService, $dbname) {
            if (!is_string($dbname)) {
                return null;
            }
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        // Define the date range for the data retrieval
        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Function to retrieve captures by interval
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

        // Calculate hourly averages and round them
        $dataTemp = $this->calculateHourlyAverage($getCapturesByInterval('temp'));
        $dataHum = $this->calculateHourlyAverage($getCapturesByInterval('hum'));
        $dataCo2 = $this->calculateHourlyAverage($getCapturesByInterval('co2'));

        // Render the details template with the data
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

    /**
     * Groups data by hour and calculates rounded averages.
     *
     * @param array<int, array{dateCapture: string, valeur: float}> $data
     * @return array<int, array{dateCapture: string, valeur: float}>
     */
    private function calculateHourlyAverage(array $data): array
    {
        $groupedData = [];

        foreach ($data as $item) {
            if (!isset($item['dateCapture'], $item['valeur'])) {
                continue; // Ignore invalid data
            }

            // Extract the hour from the capture date
            $hour = (new \DateTime($item['dateCapture']))->format('Y-m-d H:00:00');

            // Initialize the group if it doesn't exist
            if (!isset($groupedData[$hour])) {
                $groupedData[$hour] = ['sum' => 0.0, 'count' => 0];
            }

            // Sum up the values and count the occurrences
            $groupedData[$hour]['sum'] += (float) $item['valeur'];
            $groupedData[$hour]['count']++;
        }

        $averagedData = [];
        foreach ($groupedData as $hour => $values) {
            // Calculate the rounded average for each hour
            $averagedData[] = [
                'dateCapture' => $hour,
                'valeur' => round($values['sum'] / $values['count']), // Rounded average
            ];
        }

        return $averagedData;
    }

}