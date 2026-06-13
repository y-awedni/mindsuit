<?php

// src/AppBundle/EventListener/SearchIndexerSubscriber.php

namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
// for Doctrine 2.4: Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use AppBundle\Entity\User;
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
            $user=$this->tokenStorageInterface->getToken()->getUser();
            if($user){
                $entity->setCreatedUser($user);
                $entity->setUpdatedUser($user);
            }
            $entity->setCreatedAt(new \DateTime());
            $entity->setUpdatedAt(new \DateTime());
        }
    }


    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if (!$entity instanceof User) {
            $user=$this->tokenStorageInterface->getToken()->getUser();
            if($user){
                $entity->setUpdatedUser($user);
            }
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}
