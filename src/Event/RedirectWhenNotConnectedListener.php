<?php

namespace App\Event;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedirectWhenNotConnectedListener {

    private TokenStorageInterface $tokenStorageInterface;
    private RouterInterface $routerInterface;

    public function __construct(TokenStorageInterface $tokenStorageInterface, RouterInterface $routerInterface) {
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->routerInterface = $routerInterface;
    }

    public function onKernelRequest(RequestEvent $event) {
    }

}
