<?php

namespace App\Controller;

use App\Enum\Role;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if ($user !== null) {
            $role = $user->getRole();

            if ($role === Role::ADMIN) {
                return $this->redirectToRoute('app_user_index');
            } else {
                // The user is a regular user
                // Do something for regular users
                return $this->redirectToRoute('profile');
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile', name: 'profile')]
    public function profile(Security $security, UserRepository $userRepository)
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        

        if ($user !== null) {
            $role = $user->getRole();
            if ($role === Role::abonne) {
                return $this->render('user/profile.html.twig', [
                    'user' => $user,
                    'hide_password' => true,
                    'controller_name' => 'SecurityController',
                ]);
            } elseif ($role === Role::ADMIN) {
                return $this->redirectToRoute('app_user_index');
            }
        }
        // Now $user contains the currently authenticated user

        return $this->redirectToRoute('app_login');
    }
}
