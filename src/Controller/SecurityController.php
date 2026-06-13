<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        if (null === $error && null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        $lastUsername = null !== $session ? $session->get(Security::LAST_USERNAME) : '';

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/login_check", name="app_login_check")
     */
    public function loginCheckAction()
    {
        // Intercepted by the security firewall (form_login check_path).
        throw new \LogicException('This method is intercepted by the security firewall.');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logoutAction()
    {
        // Intercepted by the security firewall (logout).
        throw new \LogicException('This method is intercepted by the security firewall.');
    }
}
