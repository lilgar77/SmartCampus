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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Building;
use App\Form\BuildingType;
use App\Repository\BuildingRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BuildingController extends AbstractController
{
    /**
    @Name of function : index                                           ##
    @brief :Page that displays SA with buttons add delete and edit      ##
    @param :                                                            ##
        $buildingRepository (Access the building table in the database) ##
     **/
    #[Route('/building', name: 'app_building')]
    public function index(BuildingRepository $buildingRepository): Response
    {
        return $this->render('building/index.html.twig', [
            'buildings' => $buildingRepository->findAll(),
        ]);
    }

    /**
    @Name of function : add                                             ##
    @brief :Page that adds a new building with the different attributes ##
    @param :                                                            ##
        $request (Encapsulates HTTP request data)                       ##
        $entityManager (Used to interact with the database)             ##
     **/
    #[Route('/building/add', name: 'app_building_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
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

    /**
    @Name of function : delete                                          ##
    @brief :Page that deletes the selected building                     ##
    @param :                                                            ##
        $building (Recovers the building from the database)    ##
        $entityManager (Used to interact with the database)             ##
     **/
    #[Route('/building/{id}', name: 'app_building_delete', methods: ['POST'])]
    public function delete(Building $building, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($building);
        $entityManager->flush();

        $this->addFlash('success', 'Bâtiment "'. $building->getNameBuilding() . '" supprimé avec succès ');

        return $this->redirectToRoute('app_building');
    }

    /**
    @Name of function : edit                                                        ##
    @brief :Page that edits an existing building                                    ##
    @param :                                                                        ##
        $acquisitionSystem (Fetches the building to be edited from the database)    ##
        $request (Encapsulates HTTP request data)                                   ##
        $entityManager (Used to interact with the database)                         ##
     **/
    #[Route('/building/{id}/edit', name: 'app_building_edit')]
    public function edit(Building $building, Request $request, EntityManagerInterface $entityManager): Response
    {
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
