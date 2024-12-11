<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator){}

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('usename', '');
        // Vérification de la validité des données
        if (!is_string($username)) {
            throw new \InvalidArgumentException('Username must be a string.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        // Récupérer le mot de passe, en s'assurant qu'il s'agit d'une chaîne
        $password = $request->request->get('password', '');
        if (!is_string($password)) {
            throw new \InvalidArgumentException('Password must be a string.');
        }

        // Récupérer le CSRF token, s'assurer que c'est une chaîne
        $csrfToken = $request->request->get('_csrf_token', '');
        if (!is_string($csrfToken)) {
            throw new \InvalidArgumentException('CSRF token must be a string.');
        }

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifie si une redirection cible est définie (comme après un login ou autre).
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Vérification du rôle de l'utilisateur.
        $user = $token->getUser();
        if ($user instanceof UserInterface) { // Ensure the user is not null and is an instance of UserInterface
            if ($this->hasRole($user, 'ROLE_TECHNICIEN')) {
                return new RedirectResponse($this->urlGenerator->generate('app_technician'));
            } elseif ($this->hasRole($user, 'ROLE_ADMIN')) {
                return new RedirectResponse($this->urlGenerator->generate('app_rooms'));
            }
        }

        // Redirection par défaut si aucun rôle spécifique n'est trouvé.
        return new RedirectResponse($this->urlGenerator->generate('app_welcome'));
    }

    // Corrected the parameter type hint to match the correct UserInterface
    private function hasRole(UserInterface $user, string $role): bool
    {
        return in_array($role, $user->getRoles());
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
