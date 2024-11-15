<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Form\AcquisitionSystemeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class AcquisitionSytemeController extends AbstractController
{

    #[Route('/acquisitionsysteme', name: 'liste_app_acquisition_syteme')]
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
    }


    #[Route('/acquisitionsyteme/{id}', name: 'app_acquisition_syteme_delete', methods: ['POST'])]
    public function delete(AcquisitionSystem $acquisitionSystem, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        return $this->redirectToRoute('liste_app_acquisition_syteme');
    }


    #[Route('/acquisitionsyteme/{id}/edit', name: 'app_acquisition_syteme_edit')]
    public function edit(AcquisitionSystem $acquisitionSystem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('liste_app_acquisition_syteme');
        }

        return $this->render('acquisition_syteme/edit.html.twig', [
            'acquisition_systems' => $acquisitionSystem,
            'ASForm' => $form->createView(),
        ]);
    }

}
