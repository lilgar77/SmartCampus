<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcquisitionSytemeController extends AbstractController
{
    #[Route('/acquisition/syteme', name: 'app_acquisition_syteme')]
    public function index(): Response
    {
        return $this->render('acquisition_syteme/index.html.twig', [
            'controller_name' => 'AcquisitionSytemeController',
        ]);
    }
}
