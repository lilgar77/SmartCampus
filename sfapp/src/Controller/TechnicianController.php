<?php

namespace App\Controller;

use App\Entity\Installation;
use App\Form\TechnicianType;
use App\Model\EtatAS;
use App\Repository\InstallationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TechnicianController extends AbstractController
{
    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/technician', name: 'app_technician')]
    public function index(EntityManagerInterface $entityManager,InstallationRepository $installationRepository): Response
    {
        $installation=$entityManager->getRepository(Installation::class)->findAll();
        foreach ($installation as $installations) {
            if ($installations->getAS()->getEtat() != EtatAS::En_Installation) {
                $entityManager->remove($installations);
                $entityManager->flush();
            }
        }

        return $this->render('technician/index.html.twig', [
            'installations' => $installationRepository->findAll(),
        ]);

    }
    #[IsGranted("ROLE_TECHNICIEN")]
    #[Route('/technician/{id}/detail', name: 'app_technician_detail')]
    public function detail(Request $request,  EntityManagerInterface $entityManager, InstallationRepository $installationRepository): Response
    {
        $form = $this->createForm(TechnicianType::class);
        $form->handleRequest($request);

        $installation = $entityManager->getRepository(Installation::class)->find($request->get('id'));

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($installation);
            $installation->getAS()->setEtat(EtatAS::Installer);
            $entityManager->flush();


            $this->addFlash('success', 'Le système d\'acquisition "' . $installation->getAS() . '" a été relié avec succès à la salle"' . $installation->getRoom() . '');

            return $this->redirectToRoute('app_technician');
        }

        return $this->render('technician/details.html.twig', [
            'technicianForm' => $form->createView(),
            'installations' => $installation
        ]);
    }
}
