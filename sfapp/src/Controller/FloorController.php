<?php
################################################################################
## @Name of file : FloorController.php                                        ##
## @brief : Controller for floor management                                   ##
## @Function : Manages display, addition, editing, and deletion of floors.    ##
####                                                                          ##
## Uses Symfony to handle HTTP requests and CRUD operations on Floor entities ##
##                                                                            ##
################################################################################

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Floor;
use App\Repository\FloorRepository;
use App\Form\FloorType;

class FloorController extends AbstractController
{
    #[Route('/floor', name: 'app_floor')]
    public function index(FloorRepository $floorRepository): Response
    {
        return $this->render('floor/index.html.twig', [
            'floors' => $floorRepository->findAll(),
        ]);
    }

    #[Route('/floor/add', name: 'app_floor_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $floor = new Floor();
        $form = $this->createForm(FloorType::class, $floor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($floor);
            $entityManager->flush();

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
        $entityManager->remove($floor);
        $entityManager->flush();

        return $this->redirectToRoute('app_floor');
    }

    #[Route('/floor/{id}/edit', name: 'app_floor_edit')]
    public function edit(Floor $floor, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FloorType::class, $floor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_floor');
        }

        return $this->render('floor/edit.html.twig', [
            'floor' => $floor,
            'floorForm' => $form->createView(),
        ]);
    }
}
