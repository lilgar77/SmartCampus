<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Alert;
use App\Repository\AlertRepository;

class AlertController extends AbstractController
{
    #[Route('/alert', name: 'app_alert')]
    public function index(AlertRepository $alertRepository): Response
    {
        return $this->render('alert/index.html.twig', [
            'alertsBegin' => $alertRepository->findWithoutDateEnd(),
            'alertsEnd' => $alertRepository->findWithDateEnd(),
        ]);
    }
}
