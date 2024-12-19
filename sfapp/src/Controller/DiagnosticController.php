<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AcquisitionSystemRepository;

class DiagnosticController extends AbstractController
{
    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function index(AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        $AcquisitionSystems = $acquisitionSystemRepository->findInstalledSystems();

        return $this->render('diagnostic/index.html.twig', [
            'AS' => $AcquisitionSystems,
        ]);
    }
}
