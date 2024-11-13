<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuildingController extends AbstractController
{
    #[Route('/building', name: 'app_building')]
    public function index(): Response
    {
        return $this->render('building/index.html.twig', [
            'controller_name' => 'BuildingController',
        ]);
    }
}
