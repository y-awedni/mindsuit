<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\User;

class FlashMessages {

    private $session;
    protected $translator;

    public function __construct(Session $session, TranslatorInterface $translator) {
        $this->session = $session;
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

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof User){
            return;
        }
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }
        
        $this->session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityEdited', array('%name%' => $name)
                )
        );
    }

    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }
        $this->session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityRemoved', array('%name%' => $name)
                )
        );
    }
//ajout
    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $name = $this->getName($entity);
        if(in_array($name, ['Media','LigneDevis','LigneFacture','LigneBonLivraison','LigneBonReception','LigneBonCommandeFrs'])){
            return;
        }
        $this->session->getFlashBag()->add(
                'notice', $this->translator->trans(
                        '%name% entityAdded', array('%name%' => $name)
                )
        );
    }

}
