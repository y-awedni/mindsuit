<?php

namespace App\Controller\Operator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class OperatorLoginController extends AbstractController
{
    /**
     * @Route("/login", name="operator_login")
     */
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('operator/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login_check", name="operator_login_check")
     */
    public function loginCheck(): void
    {
        throw new \LogicException('Intercepted by security firewall.');
    }

    /**
     * @Route("/logout", name="operator_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('Intercepted by security firewall.');
    }
}
