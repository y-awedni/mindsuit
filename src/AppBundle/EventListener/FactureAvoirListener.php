<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\FactureAvoir;
use AppBundle\Entity\Mouvement;

class FactureAvoirListener {

    public function addMouvementFn($em, $ligne) {
        //formule magique, d'apres yassine, monta et abdellatif
        //calcule de mouvement financiers
        //montant=(facture.regle-(facture.ttc-factureAvoir.ttc)
        $facture = $ligne->getFactureAvoir()->getFacture();
        $client = $facture->getClient();
        if ($ligne->getReglement()) {//avoir remboursé
            $entity = $ligne->getFactureAvoir();
            $mouvement = new Mouvement();
            $mouvement->setDesignation($entity->getCode());
            $mouvement->setClient($entity->getFacture()->getClient());
            $mouvement->setTypeDoc('avc');
            $mouvement->setDateCreation($entity->getDateCreation());
            $mouvement->setModeReglement('Avoir');
            $montant = $ligne->getFactureAvoir()->getFacture()->getRegle() - ($ligne->getFactureAvoir()->getFacture()->getTotal() - $ligne->getTtc());
            if ($montant < 0) {
                $mouvement->setMouvement('revenu');
                $mouvement->setTtc(0 - $montant);
            } else {
                $mouvement->setMouvement('depense');
                $mouvement->setTtc($montant);
            }
            if ($montant !== 0) {
                $em->persist($mouvement);
                $em->flush($mouvement);
                $facture->setTotalAvoirRembourse($facture->getTotalAvoirRembourse() + $ligne->getTtc());
                $facture->setReste($facture->getReste() - $ligne->getTtc());
                $client->setTotalAvoirRembourse($client->getTotalAvoirRembourse() + $ligne->getTtc());
                $em->flush();
            }
        } else {//avoir non remboursé
            $facture->setTotalAvoirNonRembourse($facture->getTotalAvoirNonRembourse() + $ligne->getTtc());
            $facture->setReste($facture->getReste() - $ligne->getTtc());
            $client->setTotalAvoirNonRembourse($client->getTotalAvoirNonRembourse() + $ligne->getTtc());
            $em->flush();
        }
    }

    public function addMouvement($args) {
        $entity = $args->getEntity();
        $em = $args->getObjectManager();
        if ($entity instanceof FactureAvoir and $entity->getTermine()) {
            foreach ($entity->getLigneFactureAvoirs() as $ligne) {
                if($ligne->getQte()>0){
                    return $this->addMouvementFn($em, $ligne);
                }
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args) {
        return $this->addMouvement($args);
    }

    public function postUpdate(LifecycleEventArgs $args) {
        return $this->addMouvement($args);
    }

}
