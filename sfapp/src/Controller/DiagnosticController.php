<?php
##################################################################
##  @Name of file : DiagnosticController.php                    ##
##  @brief : Controller for handling diagnostics.               ##
##          Provides functionality for viewing system details   ##
##          and historical capture data by intervals.           ##
##  @Functions :                                                ##
##      - index (Displays all acquisition systems)              ##
##      - details (Displays detailed diagnostics of a system)   ##
##################################################################

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
    /**
     * Displays a list of all acquisition systems with diagnostic data.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function index(
        AcquisitionSystemRepository $acquisitionSystemRepository,
        ApiService $apiService,
        AlertManager $alertManager,
        EntityManagerInterface $entityManager,
        RoomRepository $roomRepository
    ): Response {
        // Update last captures for rooms using the API service
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Retrieve all installed acquisition systems
        $AcquisitionSystems = $acquisitionSystemRepository->findInstalledSystems();

        // Render the diagnostic index page with the list of acquisition systems
        return $this->render('diagnostic/index.html.twig', [
            'AS' => $AcquisitionSystems,
        ]);
    }

    /**
     * Displays detailed diagnostics for a specific acquisition system.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/diagnostic/{id}', name: 'app_diagnostic_details')]
    public function details(
        AcquisitionSystemRepository $acquisitionSystemRepository,
        int $id,
        RoomRepository $roomRepository,
        ApiService $apiService,
        AlertManager $alertManager,
        EntityManagerInterface $entityManager,
        AlertRepository $alertRepository,
        Request $request
    ): Response {
        // Update last captures for rooms and check for alerts
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        // Retrieve the acquisition system by ID
        $AS = $acquisitionSystemRepository->find($id);
        if (!$AS) {
            throw $this->createNotFoundException('Acquisition system not found.');
        }

        // Retrieve the associated room
        $room = $AS->getRoom();
        if (!$room) {
            throw $this->createNotFoundException('Room not found.');
        }

        // Retrieve the room's name and validate its database
        $name = $room->getName();
        if (!$name) {
            throw $this->createNotFoundException('Room name is missing.');
        }

        $roomDb = $roomRepository->getRoomDb($name);
        if (!isset($roomDb['dbname']) || !is_string($roomDb['dbname'])) {
            throw $this->createNotFoundException('Database for the room is invalid or missing.');
        }

        $dbname = $roomDb['dbname'];

        // Retrieve the last capture data for specific types
        $getLastCapture = function (string $type) use ($apiService, $dbname) {
            return $apiService->getLastCapture($type, $dbname)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        // Handle the interval parameter for historical data
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

        // Retrieve capture data for the specified interval
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

        // Retrieve the last five alerts for the room
        $alerts = $alertRepository->findLastFiveAlertsByRoom($room);

        // Render the diagnostic details page with all data
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