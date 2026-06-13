<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

class FlashMessages {

    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function getName($entity) {
        $name = substr(get_class($entity), strrpos(get_class($entity), '\\') + 1);
        switch ($name) {
            case 'LigneReglement':
                return 'Règlement facture';
            case 'LigneReglementBonReception':
                return 'Règlement bon de réception';
            default :
                return $name;
        }
    }

    public function postUpdate(PostUpdateEventArgs $args) {
        $entity = $args->getObject();
        if($entity instanceof User){
            return;
        }
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }

        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityEdited', ['%name%' => $name]
                )
        );
    }

    public function postRemove(PostRemoveEventArgs $args) {
        $entity = $args->getObject();
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityRemoved', ['%name%' => $name]
                )
        );
    }

    public function postPersist(PostPersistEventArgs $args) {
        $entity = $args->getObject();
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityAdded', ['%name%' => $name]
                )
        );
    }

}
