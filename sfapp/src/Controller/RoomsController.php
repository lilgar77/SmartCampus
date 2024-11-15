<?php

namespace App\Controller;

use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Room;
use App\Form\SearchRoomFormType;
use App\Form\RoomFormType;

class RoomsController extends AbstractController
{
    #[Route('/rooms', name: 'app_rooms')]
    public function index(Request $request, RoomRepository $roomRepository): Response
    {
        $room = new Room();
        $form = $this->createForm(SearchRoomFormType::class, $room, [
            'method' => 'GET'
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && $room->getName()!=''){
            $roomSearch = $roomRepository->findBy(
                ['name' => $room->getName()],
                ['name' => 'ASC']
            );

            return $this->render('rooms/index.html.twig',
                [
                    'rooms' => $roomSearch,
                    'room' => $form,
                ]);


        }
        return $this->render('rooms/index.html.twig',
            [
                'room'  => $form->createView(),
                'rooms' => $roomRepository->findAll(),
            ]);

    }

    #[Route('/rooms/add', name: 'app_room_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();
            return $this->redirectToRoute('app_rooms');
        }

        $rooms = $entityManager->getRepository(Room::class)->findAll();

        return $this->render('rooms/add.html.twig', [
            'rooms' => $rooms,
            'roomForm' => $form->createView(),
        ]);
    }

    #[Route('/rooms/{id}', name: 'app_room_delete', methods: ['POST'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($room);
        $entityManager->flush();

        $this->addFlash('success', 'La salle "' . $room->getName() . '" a été supprimée avec succès.');


        return $this->redirectToRoute('app_rooms');
    }

    #[Route('/rooms/{id}/edit', name: 'app_room_edit')]
    public function edit(Room $room, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rooms');
        }

        return $this->render('rooms/edit.html.twig', [
            'room' => $room,
            'roomForm' => $form->createView(),
        ]);
    }
}
