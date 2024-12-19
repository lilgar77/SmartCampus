<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;

class DiagnosticController extends AbstractController
{
    #[Route('/diagnostic', name: 'app_diagnostic')]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findRoomWithAs();

        return $this->render('diagnostic/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }
}
