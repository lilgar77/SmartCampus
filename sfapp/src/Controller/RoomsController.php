<?php
###############################################################
##  @Name of file :RoomsController.php                       ##
##  @brief :Controller for the rooms.                        ##
##          Integration of different routes for the rooms    ##
##  @Function :                                              ##
##      - index (Page that displays rooms)                   ##
##      - add  (Page that adds rooms)                        ##
##      - delete (Page that deletes rooms)                   ##
##      - edit   (Page that edits rooms)                     ##
###############################################################

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
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms', name: 'app_rooms')]
    public function index(Request $request, RoomRepository $roomRepository, ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager): Response
    {
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();


        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $roomSearch=$roomRepository->findAll();
        if ($form->isSubmitted() && $form->isValid()) {

            $roomSearch = $roomRepository->findByCriteria($room);
        }

        return $this->render('rooms/index.html.twig', [
            'rooms' => $roomSearch,
            'room'  => $form->createView(),
        ]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/add', name: 'app_room_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $alertManager->checkAndCreateAlerts();
        $room = new Room();
        $form = $this->createForm(RoomFormType::class, $room);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            if($room->getIdAS() != null)
            {
                $room->getIdAS()->setEtat(EtatAS::En_Installation);
                $installation = new Installation();
                $installation->setSA($room->getIdAS());
                $installation->setRoom($room);
                $installation->setComment("Requête pour installation");
                $entityManager->persist($installation);
            }

            $entityManager->flush();
            $this->addFlash('success', 'La salle "' . $room->getName() . '" a été ajoutée avec succès.');
            return $this->redirectToRoute('app_rooms');
        }

        $rooms = $entityManager->getRepository(Room::class)->findAll();

        return $this->render('rooms/add.html.twig', [
            'rooms' => $rooms,
            'roomForm' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {

        $alertManager->checkAndCreateAlerts();

        $alertManager->deleteAlerts($room);
        $entityManager->remove($room);
        $entityManager->flush();

        $this->addFlash('success', 'La salle "' . $room->getName() . '" a été supprimée avec succès.');


        return $this->redirectToRoute('app_rooms');
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/rooms/{id}/edit', name: 'app_room_edit')]
    public function edit(Room $room, Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {

        $alertManager->checkAndCreateAlerts();
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La salle "' . $room->getName() . '" a été modifiée avec succès.');

            return $this->redirectToRoute('app_rooms');
        }

        return $this->render('rooms/edit.html.twig', [
            'room' => $room,
            'roomForm' => $form->createView(),
        ]);
    }
}
