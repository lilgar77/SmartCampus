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

// use services api
use App\Service\ApiService;

class WelcomeController extends AbstractController
{
    private ApiService $apiService;
    private AlertManager $alertManager;


    public function __construct(ApiService $apiService, AlertManager $alertManager)
    {
        $this->apiService = $apiService;
        $this->alertManager = $alertManager;

    }

    #[Route('/', name: 'app_welcome')]
    public function index(Request $request, RoomRepository $roomRepository, ApiService $apiService): Response
    {
        $this->alertManager->checkAndCreateAlerts();
        // Récupération de toutes les salles
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
            'include_name' => false,
        ]);

        $form->handleRequest($request);

        $rooms = $roomRepository->findRoomWithAsDefault();
        if ($form->isSubmitted() && $form->isValid()) {
            $rooms = $roomRepository->findRoomWithAs($room);
        }
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
                    'temp' => round($apiService->getLastCapture('temp', $dbname)[0]['valeur'],1) ?? null,
                    'hum' =>  round($apiService->getLastCapture('hum', $dbname)[0]['valeur'],1) ?? null,
                    'co2' => $apiService->getLastCapture('co2', $dbname)[0]['valeur'] ?? null,
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
            $capture=$apiService->getLastCapture($type, $dbname)[0] ?? null;
            if (!is_string($dbname)) {
                return null;
            }
            if ($capture) {
                $capture['valeur'] = round($capture['valeur'], 1); // Arrondir au dixième près
            }
            return $capture;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        // Define the date range for the data retrieval
        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Function to retrieve captures by interval
        $getCapturesByInterval = function(string $type) use ($apiService, $date1, $date2, $dbname) {
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
     * @param array $data Array of data points with 'dateCapture' and 'valeur' keys.
     * @return array Array of hourly averaged data with rounded values.
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
                $groupedData[$hour] = ['sum' => 0, 'count' => 0];
            }

            // Sum up the values and count the occurrences
            $groupedData[$hour]['sum'] += $item['valeur'];
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