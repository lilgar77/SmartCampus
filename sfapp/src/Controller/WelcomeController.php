<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Room;
use App\Repository\RoomRepository;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'app_welcome')]
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id): Response
    {
        // Recherche l'usager par son ID
        $room = $roomRepository->find($id);

        // Gérer le cas où l'usager n'est pas trouvé
        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvé');
        }

        return $this->render('welcome/detail.html.twig', [
            'room' => $room
        ]);
    }
}
