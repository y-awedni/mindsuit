<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Notification;

class NotificationSystem {

    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }

    public function addAlertOrRupture($article, $type) {
        $notification = new Notification();
        $notification->setDescription('Article ' . $article->getCode() . $type);
        $notification->setUrl('article_show');
        $notification->setValueId($article->getId());
        $notification->setVu(false);
        $this->em->persist($notification);
        $this->em->flush($notification);
    }

    public function add($article) {
        if ($article instanceof Article) {
            if ($article->getSeuilAlert() > $article->getQteEnStock()) {
                $this->addAlertOrRupture($article, " en alerte de stock");
            }
            if ($article->getQteEnStock() <= 0) {
                $this->addAlertOrRupture($article, " en rupture de stock");
            }
        }
    }

}
