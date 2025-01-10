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
    public function index(Request $request, RoomRepository $roomRepository): Response
    {
        $this->alertManager->checkAndCreateAlerts();

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

            // Validate and extract values (assuming getLastCapture() always returns an array)
            $tempValue = isset($lastCaptureTemp[0]['valeur']) && is_numeric($lastCaptureTemp[0]['valeur'])
                ? round((float) $lastCaptureTemp[0]['valeur'], 1)
                : null;

            $humValue = isset($lastCaptureHum[0]['valeur']) && is_numeric($lastCaptureHum[0]['valeur'])
                ? round((float) $lastCaptureHum[0]['valeur'], 1)
                : null;

            $co2Value = isset($lastCaptureCo2[0]['valeur'])
                ? $lastCaptureCo2[0]['valeur']
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
            throw new \InvalidArgumentException('Invalid room name.');
        }

        $roomDbInfo = $roomRepository->getRoomDb($roomName);
        $dbname = $roomDbInfo['dbname'] ?? null;

        if (!is_string($dbname)) {
            throw new \InvalidArgumentException('Invalid database name.');
        }

        $getLastCapture = function (string $type) use ($apiService, $dbname) {
            $capture = $apiService->getLastCapture($type, $dbname)[0] ?? null;
            if ($capture && isset($capture['valeur']) && is_numeric($capture['valeur'])) {
                $capture['valeur'] = round((float)$capture['valeur'], 1);
            }
            return $capture;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        // Define the date range for the data retrieval
        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        $getCapturesByInterval = function (string $type) use ($apiService, $date1, $date2, $dbname) {
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
     * @param array<int|string, array<string, Mixed>|string>  $data Array of data points with 'dateCapture' and 'valeur' keys.
     * @return array<int|string,array<string, Mixed>|string> Array of hourly averaged data with rounded values.
     */
    private function calculateHourlyAverage(array $data): array
    {
        $groupedData = [];

        foreach ($data as $item) {
            // Validation des clés attendues
            if (!isset($item['dateCapture'], $item['valeur']) || !is_string($item['dateCapture']) || !is_numeric($item['valeur'])) {
                continue; // Ignore les données invalides
            }

            $hour = (new \DateTime($item['dateCapture']))->format('Y-m-d H:00:00');

            if (!isset($groupedData[$hour])) {
                $groupedData[$hour] = ['sum' => 0, 'count' => 0];
            }

            $groupedData[$hour]['sum'] += (float)$item['valeur'];
            $groupedData[$hour]['count']++;
        }

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
