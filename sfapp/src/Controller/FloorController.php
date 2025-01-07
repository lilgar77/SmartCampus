<?php
###############################################################
##  @Name of file :FloorController.php                       ##
##  @brief :Controller for the floor.                        ##
##          Integration of different routes for the floor    ##
##  @Function :                                              ##
##      - index (Page that displays floor)                   ##
##      - add  (Page that adds floor)                        ##
##      - delete (Page that deletes floor)                   ##
##      - edit   (Page that edits floor)                     ##
###############################################################
namespace App\Controller;

use App\Service\AlertManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Floor;
use App\Repository\FloorRepository;
use App\Form\FloorType;
use App\Form\SearchFloorType;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FloorController extends AbstractController
{
    private AlertManager $alertManager;



    public function __construct(AlertManager $alertManager)
    {
        $this->alertManager = $alertManager;
    }

    #[Route('/floor', name: 'app_floor')]
    public function index(Request $request, FloorRepository $floorRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }

        $floor = new Floor();
        $form = $this->createForm(SearchFloorType::class, $floor, [
            'method' => 'GET',
        ]);

        $form->handleRequest($request);

        $floorSearch=$floorRepository->findFloorByBuilding($floor);

        if ($form->isSubmitted() && $form->isValid()) {
            $floorSearch = $floorRepository->findFloorByBuilding($floor);
        }
        
        $this->alertManager->checkAndCreateAlerts();

        return $this->render('floor/index.html.twig', [
            'floor' => $form->createView(),
            'floors' => $floorSearch,
        ]);
    }

    #[Route('/floor/add', name: 'app_floor_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $this->alertManager->checkAndCreateAlerts();

        $floor = new Floor();
        $form = $this->createForm(FloorType::class, $floor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($floor);
            $entityManager->flush();

            $this->addFlash('success', 'Étage "'. $floor->getNumberFloor() . '" ajouté avec succès ');

            return $this->redirectToRoute('app_floor');
        }

        $floor = $entityManager->getRepository(Floor::class)->findAll();

        return $this->render('floor/add.html.twig', [
            'floor' => $floor,
            'floorForm' => $form->createView(),
        ]);
    }

    
    #[Route('/floor/{id}', name: 'app_floor_delete', methods: ['POST'])]
    public function delete(Floor $floor, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $this->alertManager->checkAndCreateAlerts();

        $entityManager->remove($floor);
        $entityManager->flush();

        $this->addFlash('success', 'Étage "'. $floor->getNumberFloor() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_floor');
    }

    
    #[Route('/floor/{id}/edit', name: 'app_floor_edit')]
    public function edit(Floor $floor, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $this->alertManager->checkAndCreateAlerts();

        $form = $this->createForm(FloorType::class, $floor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Étage "'. $floor->getNumberFloor() . '" modifié avec succès ');

            return $this->redirectToRoute('app_floor');
        }

        return $this->render('floor/edit.html.twig', [
            'floor' => $floor,
            'floorForm' => $form->createView(),
        ]);
    }
}
