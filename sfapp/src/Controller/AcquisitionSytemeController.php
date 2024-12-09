<?php

##########################################################################
##  @Name of file :AcquisitionSystemeController.php                     ##
##  @brief :Controller for the Acquisition System.                      ##
##          Integration of different routes for the acquisition system  ##
##  @Function :                                                         ##
##      - listeAS (Page that displays SA)                               ##
##      - addAS  (Page that adds SA)                                    ##
##      - delete (Page that deletes SA)                                 ##
##      - edit   (Page that edits SA)                                   ##
##########################################################################
namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Entity\Installation;
use App\Form\AcquisitionSystemeType;
use App\Model\EtatAS;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcquisitionSytemeController extends AbstractController
{

    /**
    @Name of function : listeAS                                                             ##
    @brief :Page that displays SA with buttons add delete and edit                          ##
    @param :                                                                                ##
        $acquisitionSystemRepository (Access the System Acquisition table in the database)  ##
     **/
    #[Route('/acquisitionsysteme', name: 'app_acquisition_syteme_liste')]
    public function listeAS(AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $acquisitionSystems,
        ]);
    }

    /**
    @Name of function : addAS                                                       ##
    @brief :Page that adds a new acquisition system with the different attributes   ##
    @param :                                                                        ##
    $request (Encapsulates HTTP request data)                                       ##
    $entityManager (Used to interact with the database)                             ##
     **/
    #[Route('/acquisitionsyteme/add', name: 'app_acquisition_syteme_add')]
    public function addAS(Request $request, EntityManagerInterface $entityManager): Response
    {
        $acquisitionSystem = new AcquisitionSystem();

        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            if($acquisitionSystem->getEtat()== EtatAS::En_Installation || $acquisitionSystem->getEtat()== EtatAS::A_Reparer)
            {
                $installation = new Installation();
                $installation->setSA($acquisitionSystem);
                $installation->setRoom($acquisitionSystem->getRoom());
                $installation->setComment($acquisitionSystem->getWording());
                $entityManager->persist($installation);
                $entityManager->flush();
            }
            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            $this->addFlash('success', 'Système d\'acquisition "'. $acquisitionSystem->getName() . '" ajouté avec succès ');

            return $this->redirectToRoute('app_acquisition_syteme_liste');
        }

        // Render the add page with the form
        return $this->render('acquisition_syteme/add.html.twig', [
            'acquisition_systems' => $entityManager->getRepository(AcquisitionSystem::class)->findAll(),
            'ASForm' => $form->createView(),
        ]);
    }

    /**
    @Name of function : delete                                                  ##
    @brief :Page that deletes the selected acquisition system                   ##
    @param :                                                                    ##
        $acquisitionSystem (Recovers the acquisition system from the database)  ##
        $entityManager (Used to interact with the database)                     ##
     **/
    #[Route('/acquisitionsyteme/{id}', name: 'app_acquisition_syteme_delete', methods: ['POST'])]
    public function delete(AcquisitionSystem $acquisitionSystem, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        $this->addFlash('success', 'Système d\'acquisition "'. $acquisitionSystem->getName() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_acquisition_syteme_liste');
    }

    /**
    @Name of function : edit                                                                ##
    @brief :Page that edits an existing acquisition system                                  ##
    @param :                                                                                ##
        $acquisitionSystem (Fetches the acquisition system to be edited from the database)  ##
        $request (Encapsulates HTTP request data)                                           ##
        $entityManager (Used to interact with the database)                                 ##
     **/
    #[Route('/acquisitionsyteme/{id}/edit', name: 'app_acquisition_syteme_edit')]
    public function edit(AcquisitionSystem $acquisitionSystem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(($acquisitionSystem->getEtat()== EtatAS::En_Installation || $acquisitionSystem->getEtat()== EtatAS::A_Reparer ) && $acquisitionSystem->getRoom()!=null){
                $installation = new Installation();
                $installation->setSA($acquisitionSystem);
                $installation->setRoom($acquisitionSystem->getRoom());
                $installation->setComment($acquisitionSystem->getWording());
                $entityManager->persist($installation);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Système d\'acquisition "'. $acquisitionSystem->getName() . '" modifié avec succès ');

            return $this->redirectToRoute('app_acquisition_syteme_liste');
        }

        return $this->render('acquisition_syteme/edit.html.twig', [
            'acquisition_systems' => $acquisitionSystem,
            'ASForm' => $form->createView(),
        ]);
    }

}
