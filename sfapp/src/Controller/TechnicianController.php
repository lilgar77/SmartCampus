<?php

namespace App\Controller;

use App\Entity\Installation;
use App\Form\TechnicianType;
use App\Model\EtatAS;
use App\Repository\InstallationRepository;
use App\Repository\RoomRepository;
use App\Service\AlertManager;

use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TechnicianController extends AbstractController
{
    #[Route('/technician', name: 'app_technician')]
    public function index(RoomRepository $roomRepository, ApiService $apiService, AlertManager $alertManager,EntityManagerInterface $entityManager, InstallationRepository $installationRepository): Response
    {

         if (!$this->isGranted('ROLE_TECHNICIEN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $alertManager->checkAndCreateAlerts();
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);
        $installations = $entityManager->getRepository(Installation::class)->findAll();
        foreach ($installations as $installation) {

            $acquisitionSystem = $installation->getAS();

            if ($acquisitionSystem &&
                ($acquisitionSystem->getEtat() != EtatAS::En_Installation
                    && $acquisitionSystem->getEtat() != EtatAS::A_Reparer
                    && $acquisitionSystem->getEtat() != EtatAS::A_Desinstaller) && $acquisitionSystem->getRoom() == null)
            {
                $entityManager->remove($installation);
            }

        }
        $entityManager->flush();

        return $this->render('technician/index.html.twig', [
            'installations' => $installationRepository->findAll(),
        ]);
    }
    #[Route('/technician/{id}/detail', name: 'app_technician_detail')]
    public function detail(Request $request, EntityManagerInterface $entityManager, InstallationRepository $installationRepository, AlertManager $alertManager): Response
    {
        if (!$this->isGranted('ROLE_TECHNICIEN')) {
            return $this->redirectToRoute('app_error_403');
        }
        $alertManager->checkAndCreateAlerts();
        $form = $this->createForm(TechnicianType::class);
        $form->handleRequest($request);

        $installation = $entityManager->getRepository(Installation::class)->find($request->get('id'));

        if (!$installation) {
            throw $this->createNotFoundException('Installation not found');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $acquisitionSystem = $installation->getAS();
            if ($acquisitionSystem) {
                $acquisitionSystem->setEtat(EtatAS::Installer);
            }
            $entityManager->remove($installation);
            $entityManager->flush();

            $this->addFlash('success', 'Le système d\'acquisition "' . $acquisitionSystem . '" a été relié avec succès à la salle "' . $installation->getRoom() . '"');

            return $this->redirectToRoute('app_technician');
        }

        return $this->render('technician/details.html.twig', [
            'technicianForm' => $form->createView(),
            'installations' => $installation
        ]);
    }
}
