<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AlertManager;
use App\Repository\AlertRepository;
use Psr\Log\LoggerInterface;

class AlertController extends AbstractController
{
    private AlertManager $alertManager;

    private LoggerInterface $logger; // Déclaration du logger


    public function __construct(AlertManager $alertManager, LoggerInterface $logger)
    {
        $this->alertManager = $alertManager;
        $this->logger = $logger;
    }
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository): Response
    {
        $this->alertManager->checkAndCreateAlerts();

        $this->logger->info("Alerts created");

        return $this->render('alert/index.html.twig', [
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}
