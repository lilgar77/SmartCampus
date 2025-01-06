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
use App\Entity\Room;
use App\Form\AcquisitionSystemeType;
use App\Form\SearchAquisitionSystemeType;
use App\Form\SearchRoomFormType;
use App\Model\EtatAS;
use App\Repository\AcquisitionSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AcquisitionSytemeController extends AbstractController
{

    #[Route('/acquisitionsysteme', name: 'app_acquisition_syteme_liste')]
    public function listeAS(Request $request, AcquisitionSystemRepository $acquisitionSystemRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }

        // Créer le formulaire
        $form = $this->createForm(SearchAquisitionSystemeType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $ASSearch=$acquisitionSystemRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData() ?? [];
            $ASSearch = $acquisitionSystemRepository->findByFilters($data);
        }

        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $ASSearch,
            'AS' => $form->createView(),
        ]);
    }



    #[Route('/acquisitionsyteme/add', name: 'app_acquisition_syteme_add')]
    public function addAS(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $acquisitionSystem = new AcquisitionSystem();

        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $acquisitionSystem->setEtat(EtatAS::Disponible);

            if(($acquisitionSystem->getEtat()== EtatAS::En_Installation
                || $acquisitionSystem->getEtat()== EtatAS::A_Reparer
                || $acquisitionSystem->getEtat()== EtatAS::A_Desinstaller)
                && $acquisitionSystem->getRoom()!=null)
            {
                $installation = new Installation();
                $installation->setSA($acquisitionSystem);
                $installation->setRoom($acquisitionSystem->getRoom());
                $installation->setComment($acquisitionSystem->getWording());
                $entityManager->persist($installation);
                $entityManager->flush();
            }

            // Set default values for temp and humidity
            $acquisitionSystem->setTemperature(0);
            $acquisitionSystem->setHumidity(0);
            $acquisitionSystem->setCO2(0);

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

    
    #[Route('/acquisitionsyteme/{id}', name: 'app_acquisition_syteme_delete', methods: ['POST'])]
    public function delete(AcquisitionSystem $acquisitionSystem, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        $this->addFlash('success', 'Système d\'acquisition "'. $acquisitionSystem->getName() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_acquisition_syteme_liste');
    }

    
    #[Route('/acquisitionsyteme/{id}/edit', name: 'app_acquisition_syteme_edit')]
    public function edit(AcquisitionSystem $acquisitionSystem, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(($acquisitionSystem->getEtat()== EtatAS::En_Installation
                    || $acquisitionSystem->getEtat()== EtatAS::A_Reparer
                    || $acquisitionSystem->getEtat()== EtatAS::A_Desinstaller)
                    && $acquisitionSystem->getRoom()!=null){

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
