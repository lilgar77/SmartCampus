<?php
##################################################################
##  @Name of file :BuildingController.php                       ##
##  @brief :Controller for the Buiding.                         ##
##          Integration of different routes for the building    ##
##  @Function :                                                 ##
##      - index (Page that displays building)                   ##
##      - add  (Page that adds building)                        ##
##      - delete (Page that deletes building)                   ##
##      - edit   (Page that edits building)                     ##
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
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building', name: 'app_building')]
    public function index(Request $request, BuildingRepository $buildingRepository, EntityManagerInterface $entityManager, ApiService $apiService, RoomRepository $roomRepository, AlertManager $alertManager): Response
    {


        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        $alertManager->checkAndCreateAlerts();

        $building = new Building();
        $form = $this->createForm(SearchBuldingType::class, $building, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $buildingSearch=$buildingRepository->findAll();
        if ($form->isSubmitted() && $form->isValid()) {
            $nameBuilding = $building->getNameBuilding();

            if (!empty($nameBuilding)) {
                $buildingSearch = $buildingRepository->findBuildingByName($nameBuilding);
            }
        }

        return $this->render('building/index.html.twig', [
            'buildings' => $buildingSearch,
            'building' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/building/add', name: 'app_building_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $alertManager->checkAndCreateAlerts();

        $building = new Building();
        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($building);
            $entityManager->flush();
            $this->addFlash('success', 'Bâtiment "'. $building->getNameBuilding() . '" ajouté avec succès ');

            return $this->redirectToRoute('app_building');
        }

        $building = $entityManager->getRepository(Building::class)->findAll();

        return $this->render('building/add.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]

    #[Route('/building/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(Building $building, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $alertManager->checkAndCreateAlerts();

        $entityManager->remove($building);
        $entityManager->flush();

        $this->addFlash('success', 'Bâtiment "'. $building->getNameBuilding() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_building');
    }

    #[IsGranted("ROLE_ADMIN")]

    #[Route('/building/{id}/edit', name: 'app_building_edit')]
    public function edit(Building $building, Request $request, EntityManagerInterface $entityManager, AlertManager $alertManager): Response
    {
        $alertManager->checkAndCreateAlerts();

        $form = $this->createForm(BuildingType::class, $building);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Bâtiment "'. $building->getNameBuilding() . '" modifié avec succès ');

            return $this->redirectToRoute('app_building');
        }

        return $this->render('building/edit.html.twig', [
            'building' => $building,
            'buildingForm' => $form->createView(),
        ]);
    }
}
