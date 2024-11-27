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
        $rooms = $roomRepository->findAll();

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'rooms' => $rooms,
        ]);
    }

    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id): Response
    {
        // Recherche d'une salle par son ID
        $room = $roomRepository->find($id);

        // Gérer le cas où la salle n'est pas trouvée
        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        return $this->render('welcome/detail.html.twig', [
            'room' => $room,
        ]);
    }
}