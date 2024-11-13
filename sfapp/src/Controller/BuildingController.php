<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Building;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;

class BuildingController extends AbstractController
{
    #[Route('/building', name: 'app_building')]
    public function index(BuildingRepository $buildingRepository): Response
    {
        return $this->render('building/index.html.twig', [
            'buildings' => $buildingRepository->findAll(),
        ]);
    }

    #[Route('/building/add', name: 'app_building_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($building);
            $entityManager->flush();

            return $this->redirectToRoute('app_building');
        }

        $building = $entityManager->getRepository(Building::class)->findAll();

        return $this->render('building/add.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }

    #[Route('/building/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(Building $building, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($building);
        $entityManager->flush();

        return $this->redirectToRoute('app_building');
    }

    #[Route('/building/{id}/edit', name: 'app_building_edit')]
    public function edit(Building $building, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_building');
        }

        return $this->render('building/edit.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }
}
