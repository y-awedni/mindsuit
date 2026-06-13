<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Article;
use AppBundle\Entity\Stock;

class SetQteArticleListener {

    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Stock and $entity->getTypeDoc() !== 'ca' and $entity->getArticle()->getStockable()) {
            //if entity is instance of stock
            //if typedoc not article creation
            //if article is stockable
            $em = $args->getObjectManager();
            $article = $entity->getArticle();

            $qte = $article->getQteEnStock();

            if ($entity->getMouvement()) {//entré
                $article->setQteEnStock($qte + $entity->getQte());
            } else {//sortie
                $article->setQteEnStock($qte - $entity->getQte());
            }
            $em->flush($article);
            $notificationSystem = $this->container->get("app.notification_system");
            $notificationSystem->add($article);
        }
    }

}
