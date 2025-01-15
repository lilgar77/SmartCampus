<?php
##################################################################
##  @Name of file : FloorController.php                        ##
##  @brief : Controller for managing floors.                   ##
##          Handles CRUD operations and interactions for floors ##
##  @Functions :                                               ##
##      - index : Displays a list of floors                    ##
##      - add   : Handles the addition of a new floor          ##
##      - delete: Handles the deletion of an existing floor    ##
##      - edit  : Handles the editing of a floor               ##
##################################################################

namespace App\Controller;

use App\Repository\RoomRepository;
use App\Service\AlertManager;
use App\Service\ApiService;
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
    /**
     * Displays a list of floors with search functionality.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/floor', name: 'app_floor')]
    public function index(
        FloorRepository $floorRepository,
        RoomRepository $roomRepository,
        ApiService $apiService,
        AlertManager $alertManager,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        // Update room captures and check for alerts
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $alertManager->checkAndCreateAlerts();

        // Create and handle the search form
        $floor = new Floor();
        $form = $this->createForm(SearchFloorType::class, $floor, [
            'method' => 'GET',
        ]);

        $form->handleRequest($request);

        $floorSearch = $form->isSubmitted() && $form->isValid()
            ? $floorRepository->findFloorByBuilding($floor)
            : $floorRepository->findFloorByBuilding($floor);

        return $this->render('floor/index.html.twig', [
            'floor' => $form->createView(),
            'floors' => $floorSearch,
        ]);
    }

    /**
     * Handles the addition of a new floor.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/floor/add', name: 'app_floor_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Create and handle the add floor form
        $floor = new Floor();
        $form = $this->createForm(FloorType::class, $floor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($floor);
            $entityManager->flush();

            $this->addFlash('success', 'Étage "' . $floor->getNumberFloor() . '"  ajouté avec succès.');

            return $this->redirectToRoute('app_floor');
        }

        return $this->render('floor/add.html.twig', [
            'floorForm' => $form->createView(),
        ]);
    }

    /**
     * Handles the deletion of a floor.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/floor/{id}', name: 'app_floor_delete', methods: ['POST'])]
    public function delete(
        Floor $floor,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Remove the specified floor
        $entityManager->remove($floor);
        $entityManager->flush();

        $this->addFlash('success', 'Étage "' . $floor->getNumberFloor() . '" supprimé avec succès.');

        return $this->redirectToRoute('app_floor');
    }

    /**
     * Handles the editing of a floor's details.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/floor/{id}/edit', name: 'app_floor_edit')]
    public function edit(
        Floor $floor,
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Create and handle the edit floor form
        $form = $this->createForm(FloorType::class, $floor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Étage "' . $floor->getNumberFloor() . '"  modifié avec succès.');

            return $this->redirectToRoute('app_floor');
        }

        return $this->render('floor/edit.html.twig', [
            'floor' => $floor,
            'floorForm' => $form->createView(),
        ]);
    }
}