<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('animal_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
//        $this->addFlash('danger', $error->getMessage());
        $this->addFlash('danger', "Bad username or password.");

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // redirect to a route with parameters
        return $this->redirectToRoute('animal_index', [
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
