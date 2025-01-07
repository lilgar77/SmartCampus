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



    public function __construct(AlertManager $alertManager)
    {
        $this->alertManager = $alertManager;
    }
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository): Response
    {
        $this->alertManager->checkAndCreateAlerts();



        return $this->render('alert/index.html.twig', [
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}
