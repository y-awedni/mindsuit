<?php

// src/EventListener/SearchIndexerSubscriber.php

namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
// for Doctrine 2.4: Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchIndexerSubscriber implements EventSubscriber {
    

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorageInterface;
    public function __construct(TokenStorageInterface $tokenStorageInterface) {
        $this->tokenStorageInterface = $tokenStorageInterface;
    }
    
    public function getSubscribedEvents() {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (!$entity instanceof User) {
            $token = $this->tokenStorageInterface->getToken();
            $user = $token ? $token->getUser() : null;
            if ($user instanceof User) {
                $this->callIfExists($entity, 'setCreatedUser', $user);
                $this->callIfExists($entity, 'setUpdatedUser', $user);
            }
            $this->callIfExists($entity, 'setCreatedAt', new \DateTime());
            $this->callIfExists($entity, 'setUpdatedAt', new \DateTime());
        }
    }


    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            $token = $this->tokenStorageInterface->getToken();
            $user = $token ? $token->getUser() : null;
            if ($user instanceof User) {
                $this->callIfExists($entity, 'setUpdatedUser', $user);
            }
            $this->callIfExists($entity, 'setUpdatedAt', new \DateTime());
        }
    }

    /**
     * Audit fields are a convention most entities follow, but not all (e.g. the
     * single-row Timbre config). Only stamp the ones an entity actually exposes
     * so this global subscriber never fatals on a lightweight entity.
     */
    private function callIfExists(object $entity, string $method, $value): void
    {
        if (method_exists($entity, $method)) {
            $entity->$method($value);
        }
    }
}
