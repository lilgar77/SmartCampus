<?php
##################################################################
##  @Name of file : RoomsController.php                        ##
##  @brief : Controller for managing rooms.                    ##
##          Handles CRUD operations and interactions for rooms ##
##  @Functions :                                               ##
##      - index : Displays a list of rooms                     ##
##      - add   : Handles the addition of a new room           ##
##      - delete: Handles the deletion of an existing room     ##
##      - edit  : Handles the editing of a room                ##
##################################################################

namespace App\Controller;

use App\Model\EtatAS;
use App\Repository\RoomRepository;
use App\Service\AlertManager;
use App\Service\ApiService;
use App\Entity\Installation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Room;
use App\Form\SearchRoomFormType;
use App\Form\RoomFormType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RoomsController extends AbstractController
{
    /**
     * Displays a list of rooms with search functionality.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms', name: 'app_rooms')]
    public function index(
        Request $request,
        RoomRepository $roomRepository,
        ApiService $apiService,
        AlertManager $alertManager,
        EntityManagerInterface $entityManager
    ): Response {
        // Update room captures and check for alerts
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        // Create and handle the search form
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $roomSearch = $form->isSubmitted() && $form->isValid()
            ? $roomRepository->findByCriteria($room)
            : $roomRepository->findAll();

        return $this->render('rooms/index.html.twig', [
            'rooms' => $roomSearch,
            'room'  => $form->createView(),
        ]);
    }

    /**
     * Handles the addition of a new room.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/add', name: 'app_room_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Create and handle the add room form
        $room = new Room();
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);

            if ($room->getIdAS() !== null) {
                $room->getIdAS()->setEtat(EtatAS::En_Installation);
                $installation = new Installation();
                $installation->setSA($room->getIdAS());
                $installation->setRoom($room);
                $installation->setComment("Request for installation");
                $entityManager->persist($installation);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Room "' . $room->getName() . '" has been successfully added.');

            return $this->redirectToRoute('app_rooms');
        }

        return $this->render('rooms/add.html.twig', [
            'roomForm' => $form->createView(),
        ]);
    }

    /**
     * Handles the deletion of a room.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Room $room,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Delete related alerts and the room itself
        $alertManager->deleteAlerts($room);
        $entityManager->remove($room);
        $entityManager->flush();

        $this->addFlash('success', 'Room "' . $room->getName() . '" has been successfully deleted.');

        return $this->redirectToRoute('app_rooms');
    }

    /**
     * Handles the editing of a room's details.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/{id}/edit', name: 'app_room_edit')]
    public function edit(
        Room $room,
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Create and handle the edit room form
        $form = $this->createForm(RoomFormType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Room "' . $room->getName() . '" has been successfully updated.');

            return $this->redirectToRoute('app_rooms');
        }

        return $this->render('rooms/edit.html.twig', [
            'room' => $room,
            'roomForm' => $form->createView(),
        ]);
    }
}