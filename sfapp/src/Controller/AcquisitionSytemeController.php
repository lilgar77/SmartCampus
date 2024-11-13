<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Repository\AcquisitionSystemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcquisitionSytemeController extends AbstractController
{
    /*
    #[Route('/acquisitionsyteme', name: 'app_acquisition_syteme')]
    public function index(): Response
    {
        return $this->render('acquisition_syteme/index.html.twig', [
            'controller_name' => 'AcquisitionSytemeController',
        ]);
    }
    */
    #[Route('/acquisitionsytemeList', name: 'liste_app_acquisition_syteme')]
    public function listeAS(AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $acquisitionSystems,
        ]);
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
