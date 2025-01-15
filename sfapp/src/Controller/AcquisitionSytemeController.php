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
use App\Service\AlertManager;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\RoomRepository;

class AcquisitionSytemeController extends AbstractController
{
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsysteme', name: 'app_acquisition_syteme_liste')]
    public function listeAS(Request $request, AcquisitionSystemRepository $acquisitionSystemRepository, RoomRepository $roomRepository, ApiService $apiService, AlertManager $alertManager, EntityManagerInterface $entityManager): Response
    {
        // Update last capture data for rooms using the API service
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Check and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Create the search form for acquisition systems
        $form = $this->createForm(SearchAquisitionSystemeType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        // Fetch all acquisition systems by default
        $ASSearch = $acquisitionSystemRepository->findAll();

        // If the form is submitted and valid, filter acquisition systems based on search criteria
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($data !== null && is_array($data)) {
                $ASSearch = $acquisitionSystemRepository->findByFilters($data);
            }
        }

        // Render the list page with filtered acquisition systems
        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $ASSearch,
            'AS' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/add', name: 'app_acquisition_syteme_add')]
    public function addAS(Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        // Check and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Initialize a new acquisition system entity
        $acquisitionSystem = new AcquisitionSystem();

        // Create the form for adding a new acquisition system
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Set default state as "Available"
            $acquisitionSystem->setEtat(EtatAS::Disponible);

            // Handle special states that require an installation entity
            if (($acquisitionSystem->getEtat() == EtatAS::En_Installation
                    || $acquisitionSystem->getEtat() == EtatAS::A_Reparer
                    || $acquisitionSystem->getEtat() == EtatAS::A_Desinstaller)
                && $acquisitionSystem->getRoom() != null) {
                $installation = new Installation();
                $installation->setSA($acquisitionSystem);
                $installation->setRoom($acquisitionSystem->getRoom());
                $installation->setComment($acquisitionSystem->getWording());
                $entityManager->persist($installation);
                $entityManager->flush();
            }

            // Set default sensor values
            $acquisitionSystem->setTemperature(0);
            $acquisitionSystem->setHumidity(0);
            $acquisitionSystem->setCO2(0);

            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            // Add success message and redirect to the list page
            $this->addFlash('success', 'Système d\'acquisitions "' . $acquisitionSystem->getName() . '" ajouté avec succès.');
            return $this->redirectToRoute('app_acquisition_syteme_liste');
        }

        // Render the add page with the form
        return $this->render('acquisition_syteme/add.html.twig', [
            'acquisition_systems' => $entityManager->getRepository(AcquisitionSystem::class)->findAll(),
            'ASForm' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/{id}', name: 'app_acquisition_syteme_delete', methods: ['POST'])]
    public function delete(AcquisitionSystem $acquisitionSystem, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        // Check and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Remove alerts associated with the acquisition system's room
        if ($acquisitionSystem->getRoom() != null) {
            $alertManager->deleteAlerts($acquisitionSystem->getRoom());
        }

        // Remove the acquisition system from the database
        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        // Add success message and redirect to the list page
        $this->addFlash('success', 'Système d\'acquisition "' . $acquisitionSystem->getName() . '" supprimé avec succès.');
        return $this->redirectToRoute('app_acquisition_syteme_liste');
    }

    #[isGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/{id}/edit', name: 'app_acquisition_syteme_edit')]
    public function edit(AcquisitionSystem $acquisitionSystem, Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        // Ensure the user has admin privileges
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }

        // Check and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Create the edit form for the acquisition system
        $form = $this->createForm(AcquisitionSystemeType::class, $acquisitionSystem);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle special states that require an installation entity
            if (($acquisitionSystem->getEtat() == EtatAS::En_Installation
                    || $acquisitionSystem->getEtat() == EtatAS::A_Reparer
                    || $acquisitionSystem->getEtat() == EtatAS::A_Desinstaller)
                && $acquisitionSystem->getRoom() != null) {
                $installation = new Installation();
                $installation->setSA($acquisitionSystem);
                $installation->setRoom($acquisitionSystem->getRoom());
                $installation->setComment("Request for installation");
                $entityManager->persist($installation);
            }
            $entityManager->flush();

            // Add success message and redirect to the list page
            $this->addFlash('success', 'Système d\'acquisition "' . $acquisitionSystem->getName() . '" modifié avec succès.');
            return $this->redirectToRoute('app_acquisition_syteme_liste');
        }

        // Render the edit page with the form
        return $this->render('acquisition_syteme/edit.html.twig', [
            'acquisition_systems' => $acquisitionSystem,
            'ASForm' => $form->createView(),
        ]);
    }
}