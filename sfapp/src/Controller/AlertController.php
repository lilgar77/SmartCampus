<?php

namespace App\Controller;

use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AlertManager;
use App\Repository\AlertRepository;
use App\Repository\RoomRepository;

class AlertController extends AbstractController
{
    private AlertManager $alertManager;
    private ApiService $apiService;

    private EntityManagerInterface $entityManager;



    public function __construct(AlertManager $alertManager, ApiService $apiService, EntityManagerInterface $entityManager)
    {
        $this->alertManager = $alertManager;
        $this->apiService = $apiService;
        $this->entityManager = $entityManager;
    }
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository, RoomRepository $roomRepository): Response
    {
        $this->apiService->updateLastCapturesForRooms($roomRepository, $this->entityManager);
        $this->alertManager->checkAndCreateAlerts();



        return $this->render('alert/index.html.twig', [
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}
