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
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        $alertManager->checkAndCreateAlerts();

        // Créer le formulaire
        $form = $this->createForm(SearchAquisitionSystemeType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $ASSearch = $acquisitionSystemRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Vérifier que les données ne sont pas nulles avant d'appeler la méthode
            if ($data !== null && is_array($data)) {
                $ASSearch = $acquisitionSystemRepository->findByFilters($data);
            }
        }

        return $this->render('acquisition_syteme/index.html.twig', [
            'acquisition_systems' => $ASSearch,
            'AS' => $form->createView(),
        ]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/add', name: 'app_acquisition_syteme_add')]
    public function addAS(Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {

        $alertManager->checkAndCreateAlerts();

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

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/{id}', name: 'app_acquisition_syteme_delete', methods: ['POST'])]
    public function delete(AcquisitionSystem $acquisitionSystem, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $alertManager->checkAndCreateAlerts();

        if($acquisitionSystem->getRoom()!=null){
            $alertManager->deleteAlerts($acquisitionSystem->getRoom());

        }

        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        $this->addFlash('success', 'Système d\'acquisition "'. $acquisitionSystem->getName() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_acquisition_syteme_liste');
    }

    #[isGranted("ROLE_ADMIN")]
    #[Route('/acquisitionsyteme/{id}/edit', name: 'app_acquisition_syteme_edit')]
    public function edit(AcquisitionSystem $acquisitionSystem, Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $alertManager->checkAndCreateAlerts();

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
                $installation->setComment("Requête pour installation");
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
