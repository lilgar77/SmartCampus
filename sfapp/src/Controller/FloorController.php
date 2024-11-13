<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FloorController extends AbstractController
{
    #[Route('/floor', name: 'app_floor')]
    public function index(): Response
    {
        return $this->render('floor/index.html.twig', [
            'controller_name' => 'FloorController',
        ]);
    }
}
