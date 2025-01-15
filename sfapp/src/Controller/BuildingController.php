<?php
##################################################################
##  @Name of file : BuildingController.php                      ##
##  @brief : Controller for managing buildings.                 ##
##          Provides functionality to display, add, edit,       ##
##          and delete buildings within the system.             ##
##  @Function :                                                 ##
##      - index (Page that displays all buildings)              ##
##      - add   (Page that adds a new building)                 ##
##      - delete (Page that deletes an existing building)       ##
##      - edit   (Page that edits a building's details)         ##
##################################################################

namespace App\Controller;

use App\Form\SearchBuldingType;
use App\Service\AlertManager;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Building;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use App\Repository\RoomRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BuildingController extends AbstractController
{
    /**
     * Displays the list of buildings with search functionality.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building', name: 'app_building')]
    public function index(
        Request $request,
        BuildingRepository $buildingRepository,
        EntityManagerInterface $entityManager,
        ApiService $apiService,
        RoomRepository $roomRepository,
        AlertManager $alertManager
    ): Response {
        // Update the last capture data for rooms using the API service
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Check existing conditions and create new alerts if necessary
        $alertManager->checkAndCreateAlerts();

        // Create a new building entity to hold search criteria
        $building = new Building();

        // Create the search form
        $form = $this->createForm(SearchBuldingType::class, $building, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        // Fetch all buildings initially
        $buildingSearch = $buildingRepository->findAll();

        // If the form is submitted and valid, filter buildings by name
        if ($form->isSubmitted() && $form->isValid()) {
            $nameBuilding = $building->getNameBuilding();

            if (!empty($nameBuilding)) {
                $buildingSearch = $buildingRepository->findBuildingByName($nameBuilding);
            }
        }

        // Render the index page with the list of buildings and the search form
        return $this->render('building/index.html.twig', [
            'buildings' => $buildingSearch,
            'building' => $form->createView(),
        ]);
    }

    /**
     * Adds a new building.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building/add', name: 'app_building_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts as necessary
        $alertManager->checkAndCreateAlerts();

        // Create a new building entity
        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the new building to the database
            $entityManager->persist($building);
            $entityManager->flush();

            // Add a success message and redirect to the building list
            $this->addFlash('success', 'Building "' . $building->getNameBuilding() . '" added successfully.');
            return $this->redirectToRoute('app_building');
        }

        // Fetch all buildings to pass to the view
        $building = $entityManager->getRepository(Building::class)->findAll();

        // Render the add page with the form
        return $this->render('building/add.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }

    /**
     * Deletes an existing building.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(
        Building $building,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts as necessary
        $alertManager->checkAndCreateAlerts();

        // Remove the building from the database
        $entityManager->remove($building);
        $entityManager->flush();

        // Add a success message and redirect to the building list
        $this->addFlash('success', 'Building "' . $building->getNameBuilding() . '" deleted successfully.');
        return $this->redirectToRoute('app_building');
    }

    /**
     * Edits an existing building.
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building/{id}/edit', name: 'app_building_edit')]
    public function edit(
        Building $building,
        Request $request,
        EntityManagerInterface $entityManager,
        AlertManager $alertManager
    ): Response {
        // Check and create alerts as necessary
        $alertManager->checkAndCreateAlerts();

        // Create the edit form
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the updated building to the database
            $entityManager->flush();

            // Add a success message and redirect to the building list
            $this->addFlash('success', 'Building "' . $building->getNameBuilding() . '" updated successfully.');
            return $this->redirectToRoute('app_building');
        }

        // Render the edit page with the form
        return $this->render('building/edit.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }
}