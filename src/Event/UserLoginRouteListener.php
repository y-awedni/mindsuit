<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserLoginRouteListener {

    private TokenStorageInterface $tokenStorageInterface;
    private RouterInterface $routerInterface;

    public function __construct(TokenStorageInterface $tokenStorageInterface, RouterInterface $routerInterface) {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->routerInterface = $routerInterface;
    }

    public function onKernelRequest(RequestEvent $event) {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->get('_route') !== 'app_login') {
            return;
        }

        $token = $this->tokenStorageInterface->getToken();
        if (null === $token || !$token->getUser() instanceof User) {
            return;
        }

        $event->setResponse(
            new RedirectResponse($this->routerInterface->generate('homepage'))
        );
    }

}
