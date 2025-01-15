<?php
##################################################################
##  @Name of file : TechnicianController.php                   ##
##  @Brief : Controller for technician operations.             ##
##          Provides functionality for managing installations   ##
##          and system details.                                ##
##  @Functions :                                               ##
##      - index  : Displays all installations.                 ##
##      - detail : Displays and processes details of a         ##
##                 specific installation.                      ##
##################################################################

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
    /**
     * Displays the list of all installations for technicians.
     *
     * @param RoomRepository $roomRepository Repository for accessing room data.
     * @param ApiService $apiService Service to update room capture data.
     * @param AlertManager $alertManager Service to manage alerts.
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param InstallationRepository $installationRepository Repository for accessing installations.
     * @return Response
     */
    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/technician', name: 'app_technician')]
    public function index(
        RoomRepository $roomRepository,
        ApiService $apiService,
        AlertManager $alertManager,
        EntityManagerInterface $entityManager,
        InstallationRepository $installationRepository
    ): Response {
        // Check for and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Update capture data for rooms
        $apiService->updateLastCapturesForRooms($roomRepository, $entityManager);

        // Cleanup installations where acquisition systems are no longer relevant
        $installations = $entityManager->getRepository(Installation::class)->findAll();
        foreach ($installations as $installation) {
            $acquisitionSystem = $installation->getAS();
            if ($acquisitionSystem &&
                !in_array($acquisitionSystem->getEtat(), [EtatAS::En_Installation, EtatAS::A_Reparer, EtatAS::A_Desinstaller], true) &&
                $acquisitionSystem->getRoom() === null
            ) {
                $entityManager->remove($installation);
            }
        }
        $entityManager->flush();

        // Render the technician dashboard
        return $this->render('technician/index.html.twig', [
            'installations' => $installationRepository->findAll(),
        ]);
    }

    /**
     * Displays the details of a specific installation and processes updates.
     *
     * @param Request $request HTTP request containing installation data.
     * @param EntityManagerInterface $entityManager Entity manager for database operations.
     * @param InstallationRepository $installationRepository Repository for accessing installations.
     * @param AlertManager $alertManager Service to manage alerts.
     * @return Response
     */
    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/technician/{id}/detail', name: 'app_technician_detail')]
    public function detail(
        Request $request,
        EntityManagerInterface $entityManager,
        InstallationRepository $installationRepository,
        AlertManager $alertManager
    ): Response {
        // Check for and create necessary alerts
        $alertManager->checkAndCreateAlerts();

        // Create a form for technician actions
        $form = $this->createForm(TechnicianType::class);
        $form->handleRequest($request);

        // Retrieve the installation by ID
        $installation = $entityManager->getRepository(Installation::class)->find($request->get('id'));
        if (!$installation) {
            throw $this->createNotFoundException('Installation not found');
        }

        // Process the form submission
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

        // Render the technician detail page
        return $this->render('technician/details.html.twig', [
            'technicianForm' => $form->createView(),
            'installations' => $installation,
        ]);
    }
}