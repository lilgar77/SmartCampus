<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function index(RoomRepository $roomRepository): Response
    {
        // Récupération de toutes les salles
        $rooms = $roomRepository->findRoomWithAs();

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'rooms' => $rooms,
        ]);
    }

    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id): Response
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        return $this->render('welcome/detail.html.twig', [
            'room' => $room,
        ]);
    }
}