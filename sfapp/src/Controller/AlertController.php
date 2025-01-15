<?php
##########################################################################
##  @Name of file : AlertController.php                                 ##
##  @brief : Controller for managing alerts.                           ##
##          Integrates functionalities to view ongoing and resolved    ##
##          alerts in the system.                                      ##
##  @Function :                                                        ##
##      - index (Page that displays active and resolved alerts)        ##
##########################################################################
namespace App\Controller;

use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AlertManager;
use App\Repository\AlertRepository;
use App\Repository\RoomRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AlertController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository, RoomRepository $roomRepository, ApiService $apiService, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        // Update the last capture data for rooms using the API service
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Check existing conditions and create new alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Render the alert management page with ongoing and resolved alerts
        return $this->render('alert/index.html.twig', [
            // Fetch alerts that are currently active (no end date)
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            // Fetch alerts that have been resolved (have an end date)
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}