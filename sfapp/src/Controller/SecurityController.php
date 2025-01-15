<?php
##################################################################
##  @Name of file : SecurityController.php                     ##
##  @brief : Handles user authentication and logout.           ##
##  @Functions :                                               ##
##      - login  : Displays the login page and handles errors  ##
##      - logout : Handles user logout (managed by firewall)   ##
##################################################################

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Displays the login page and handles authentication errors.
     *
     * @param AuthenticationUtils $authenticationUtils Provides authentication error and last username.
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Retrieve the last authentication error, if any
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login template with the retrieved data
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Handles the user logout.
     *
     * This method is intercepted by the logout key in the security firewall.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // LogicException is thrown as the method is not meant to be executed directly.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}