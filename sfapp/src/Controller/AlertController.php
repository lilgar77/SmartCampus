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
use Symfony\Component\Security\Http\Attribute\IsGranted;


class AlertController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository, RoomRepository $roomRepository, ApiService $apiService, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();


        return $this->render('alert/index.html.twig', [
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}
