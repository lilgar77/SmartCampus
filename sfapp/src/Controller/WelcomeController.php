<?php
#######################################################################
## @Name of file : WelcomeController.php                             ##
## @brief : Controller for the welcome page                          ##
## @Function : Renders the welcome page                              ##
####                                                                 ##
## Uses Symfony to handle HTTP requests and display the welcome page ##
##                                                                   ##
#######################################################################

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    #[Route('/welcome', name: 'app_welcome')]
    public function index(): Response
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
        ]);
    }
}
