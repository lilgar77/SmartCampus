<?php
##################################################################
##  @Name of file : WelcomeController.php                       ##
##  @Brief : Controller for welcome page functionality.         ##
##          Handles the display of rooms, their captures, and    ##
##          provides detailed room information.                 ##
##  @Functions :                                               ##
##      - index  : Displays rooms with last captures.           ##
##      - details: Displays detailed room information, including##
##                 hourly averages for captures.                ##
##################################################################

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
use App\Service\ApiService;

class WelcomeController extends AbstractController
{
    private ApiService $apiService;
    private AlertManager $alertManager;

    /**
     * Constructor to initialize ApiService and AlertManager.
     *
     * @param ApiService $apiService Service to interact with the API.
     * @param AlertManager $alertManager Service to manage alerts.
     */
    public function __construct(ApiService $apiService, AlertManager $alertManager)
    {
        $this->apiService = $apiService;
        $this->alertManager = $alertManager;
    }

    /**
     * Displays the welcome page with a form to search rooms.
     *
     * @param Request $request HTTP request for handling form submission.
     * @param RoomRepository $roomRepository Repository for room data.
     * @return Response
     */
    #[Route('/', name: 'app_welcome')]
    public function index(Request $request, RoomRepository $roomRepository): Response
    {
        // Check and create necessary alerts
        $this->alertManager->checkAndCreateAlerts();

        // Initialize the room search form
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
            'include_name' => false,
        ]);
        $form->handleRequest($request);

        // Fetch rooms based on search criteria
        $rooms = $roomRepository->findRoomWithAsDefault();
        if ($form->isSubmitted() && $form->isValid()) {
            $rooms = $roomRepository->findRoomWithAs($room);
        }

        // Retrieve the last captures for each room
        $roomsWithLastCaptures = array_map(function ($room) use ($roomRepository) {
            $roomName = $room->getName() ?? '';
            $roomDbInfo = $roomRepository->getRoomDb($roomName);
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

            // Get last captures
            $lastCaptureTemp = $this->apiService->getLastCapture('temp', $dbname);
            $lastCaptureHum = $this->apiService->getLastCapture('hum', $dbname);
            $lastCaptureCo2 = $this->apiService->getLastCapture('co2', $dbname);

            // Validate and extract values
            $tempValue = isset($lastCaptureTemp[0]['valeur']) && is_numeric($lastCaptureTemp[0]['valeur'])
                ? round((float)$lastCaptureTemp[0]['valeur'], 1)
                : null;

            $humValue = isset($lastCaptureHum[0]['valeur']) && is_numeric($lastCaptureHum[0]['valeur'])
                ? round((float)$lastCaptureHum[0]['valeur'], 1)
                : null;

            $co2Value = isset($lastCaptureCo2[0]['valeur']) && is_numeric($lastCaptureCo2[0]['valeur'])
                ? $lastCaptureCo2[0]['valeur'] // Ensure it's cast to int
                : null;

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

        // Render the welcome page with room data and the form
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'room'  => $form->createView(),
            'rooms' => $rooms,
            'roomsWithLastCaptures' => $roomsWithLastCaptures,
        ]);
    }

    /**
     * Displays detailed information for a specific room, including hourly average data.
     *
     * @param RoomRepository $roomRepository Repository for room data.
     * @param int $id The ID of the room.
     * @param ApiService $apiService Service to interact with the API for capture data.
     * @param AlertManager $alertManager Service to manage alerts.
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @return Response
     */
    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id, ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager): Response
    {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        $room = $roomRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        $roomName = $room->getName();
        if (!is_string($roomName)) {
            throw new \InvalidArgumentException('Invalid room name.');
        }

        $roomDbInfo = $roomRepository->getRoomDb($roomName);
        $dbname = $roomDbInfo['dbname'] ?? null;

        if (!is_string($dbname)) {
            throw new \InvalidArgumentException('Invalid database name.');
        }

        // Helper function to retrieve the last capture for a given type
        $getLastCapture = function (string $type) use ($apiService, $dbname) {
            $capture = $apiService->getLastCapture($type, $dbname)[0] ?? null;
            if ($capture && isset($capture['valeur']) && is_numeric($capture['valeur'])) {
                $capture['valeur'] = round((float)$capture['valeur'], 1);
            }
            return $capture;
        };

        // Retrieve the last captures for temperature, humidity, and CO2
        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        $acquisitionSystem = $room->getIdAS();
        if ($acquisitionSystem !== null) {
            if (isset($lastCapturetemp['valeur']) && is_numeric($lastCapturetemp['valeur'])) {
                $acquisitionSystem->setTemperature((int)$lastCapturetemp['valeur']);
            }
            if (isset($lastCapturehum['valeur']) && is_numeric($lastCapturehum['valeur'])) {
                $acquisitionSystem->setHumidity((int)$lastCapturehum['valeur']);
            }
            if (isset($lastCaptureco2['valeur']) && is_numeric($lastCaptureco2['valeur'])) {
                $acquisitionSystem->setCO2((int)$lastCaptureco2['valeur']);
            }
        }

        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Helper function to retrieve captures within a date range
        $getCapturesByInterval = function (string $type) use ($apiService, $date1, $date2, $dbname) {
            try {
                return $apiService->getCapturesByInterval($date1, $date2, $type, 1, $dbname);
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        };

        $sanitizeData = function ($data) {
            if (!is_array($data)) {
                return [];
            }

            // Ensure each item is an array with 'dateCapture' and 'valeur'
            return array_filter($data, function ($item) {
                return is_array($item) && isset($item['dateCapture'], $item['valeur']) && is_string($item['dateCapture']) && is_numeric($item['valeur']);
            });
        };

        // Sanitize data before passing to the calculateHourlyAverage function
        $dataTemp = $this->calculateHourlyAverage($sanitizeData($getCapturesByInterval('temp')));
        $dataHum = $this->calculateHourlyAverage($sanitizeData($getCapturesByInterval('hum')));
        $dataCo2 = $this->calculateHourlyAverage($sanitizeData($getCapturesByInterval('co2')));

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
     * @param array<mixed> $data
     * @return array<mixed>
     */
    private function calculateHourlyAverage(array $data): array
    {
        // Ensure that the data is well-structured
        $groupedData = [];

        // Group data by hour
        foreach ($data as $item) {
            // Ensure that the item is an array with 'dateCapture' and 'valeur'
            if (!is_array($item) || !isset($item['dateCapture'], $item['valeur'])
                || !is_string($item['dateCapture']) || !is_numeric($item['valeur'])) {
                continue;  // Skip if the data is not valid
            }

            // Convert 'dateCapture' into the desired hourly format
            try {
                $hour = (new \DateTime($item['dateCapture']))->format('Y-m-d H:00:00');
            } catch (\Exception $e) {
                continue; // Skip invalid date formats
            }

            // Initialize array if not already set
            if (!isset($groupedData[$hour])) {
                $groupedData[$hour] = ['sum' => 0, 'count' => 0];
            }

            // Accumulate the sum and count for averaging later
            $groupedData[$hour]['sum'] += (float)$item['valeur'];
            $groupedData[$hour]['count']++;
        }

        // Calculate hourly averages
        $averagedData = [];
        foreach ($groupedData as $hour => $values) {
            $averagedData[] = [
                'dateCapture' => $hour,
                'valeur' => round($values['sum'] / $values['count'], 1),
            ];
        }

        return $averagedData;
    }

}
