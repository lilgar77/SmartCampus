<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;

// use services api
use App\Service\ApiService;

class WelcomeController extends AbstractController
{
    private ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    #[Route('/', name: 'app_welcome')]
    public function index(RoomRepository $roomRepository): Response
    {
        // Récupération de toutes les salles
        $rooms = $roomRepository->findRoomWithAs();

        // Rendu de la vue
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'rooms' => $rooms,
        ]);
    }

    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id, ApiService $apiService): Response
    {
        $room = $roomRepository->find($id);

        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        try {
            $data = $apiService->getCapturesByInterval($date1, $date2, 1);

            $lastCapture = $apiService->getLastCapture('ESP-011');
        } catch (\Exception $e) {
            $data = ['error' => $e->getMessage()];
            $lastCapture = ['error' => $e->getMessage()];
        }

        return $this->render('welcome/detail.html.twig', [
            'room' => $room,
            'data' => $data,
            'lastCapture' => $lastCapture,
        ]);
    }
}