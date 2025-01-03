<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\SearchRoomFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;

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
    public function index(Request $request, RoomRepository $roomRepository): Response
    {
        // Récupération de toutes les salles
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
            'include_name' => false,
        ]);
        $form->handleRequest($request);

        $rooms = $roomRepository->findRoomWithAsDefault();
        if ($form->isSubmitted() && $form->isValid()) {
            $rooms = $roomRepository->findRoomWithAs($room);
        }

        $getLastCapture = function(string $type) {
            return $this->apiService->getLastCapture($type)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');


        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
            'room'  => $form->createView(),
            'rooms' => $rooms,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
        ]);
    }


    #[Route('/{id}', name: 'app_welcome_details')]
    public function details(RoomRepository $roomRepository, int $id, ApiService $apiService): Response
    {
        $room = $roomRepository->find($id);
        if (!$room) {
            throw $this->createNotFoundException('Salle non trouvée');
        }

        $getLastCapture = function(string $type) use ($apiService) {
            return $apiService->getLastCapture($type)[0] ?? null;
        };

        $lastCapturetemp = $getLastCapture('temp');
        $lastCapturehum = $getLastCapture('hum');
        $lastCaptureco2 = $getLastCapture('co2');

        $date1 = (new \DateTime())->format('Y-m-d');
        $date2 = (new \DateTime('tomorrow'))->format('Y-m-d');

        // Fonction pour récupérer les données d'intervalle pour chaque type
        $getCapturesByInterval = function(string $type) use ($apiService, $date1, $date2) {
            try {
                return $apiService->getCapturesByInterval($date1, $date2, $type, 1);
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        };

        $dataTemp = $getCapturesByInterval('temp');
        $dataHum = $getCapturesByInterval('hum');
        $dataCo2 = $getCapturesByInterval('co2');

        return $this->render('welcome/detail.html.twig', [
            'room' => $room,
            'dataTemp' => $dataTemp,
            'dataHum' => $dataHum,
            'dataCo2' => $dataCo2,
            'lastCapturetemp' => $lastCapturetemp,
            'lastCapturehum' => $lastCapturehum,
            'lastCaptureco2' => $lastCaptureco2,
        ]);
    }
}