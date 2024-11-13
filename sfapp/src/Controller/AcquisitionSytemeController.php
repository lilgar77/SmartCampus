<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Form\AcquisitionSystemeType;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class AcquisitionSytemeController extends AbstractController
{

    #[Route('/acquisitionsyteme', name: 'liste_app_acquisition_syteme')]
    public function listeAS(AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $acquisitionSystems,
        ]);
    }
    #[Route('/acquisitionsyteme/add', name: 'app_acquisition_syteme_add')]
    public function addAS(Request $request, EntityManagerInterface $entityManager): Response
    {
        $acquisitionSystem = new AcquisitionSystem();
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            return $this->redirectToRoute('liste_app_acquisition_syteme');
        }
        $acquisitionSystems = $entityManager->getRepository(AcquisitionSystem::class)->findAll();
        return $this->render('acquisition_syteme/add.html.twig', [
            'acquisition_systems' => $acquisitionSystems,
            'ASForm' => $form->createView(),
        ]);

        /*

        $rooms = $entityManager->getRepository(Room::class)->findAll();

        return $this->render('rooms/add.html.twig', [
            'rooms' => $rooms,
            'roomForm' => $form->createView(),
        ]);
         */
    }




/*
 * public function index(Request $request, UsagerRepository $usagerRepository): Response
    {
        $searchUsagerData = new Usager;
        $form = $this->createForm(UsagerSearchType::class, $searchUsagerData);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && $searchUsagerData->getNom() != ''){
            $usager = $usagerRepository->findBy(
                ['nom' => $searchUsagerData->getNom()],
                ['prenom'=> 'ASC'],
            );
            return $this->render('usager_liste/index.html.twig',
            [   'usager' => $usager,
                'form' => $form,
            ]);
        }


        return $this->render('usager_liste/index.html.twig', [
            'usager' => $usagerRepository->findBy(
            [],
            ['nom'=> 'ASC'],
            ),
            'form' => $form->createView(),

        ]);
 */



}
