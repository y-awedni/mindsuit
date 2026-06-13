<?php

namespace App\EventListener;

use App\Entity\Media;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MediaListener {

    private $imagesDirectory;
    public $tempFile;
    public $oldFile;

    public function __construct($imagesDirectory) {
        $this->imagesDirectory = $imagesDirectory;
    }
    
    
    //functions_________________________________________________________________
    public function getUploadRootDir() {
        return $this->imagesDirectory;
    }
    
    public function getAbsolutePath($media) {
        return null === $media->getPath() ? null : $this->getUploadRootDir() . '/' . $media->getPath();
    }
    public function preUpload($media) {
        $this->tempFile = $this->getAbsolutePath($media);
        $this->oldFile = $media->getPath();
        $this->updateAt = new \DateTime();
        if (null !== $media->file) {
            $media->setPath(sha1(uniqid(mt_rand(), true)) . '.' . $media->file->guessExtension());
        }
    }
    
    public function postUpload($media) {
        if (null !== $media->file) {
            $media->file->move($this->getUploadRootDir(), $media->getPath());
            unset($media->file);

            if ($this->oldFile != null) {
                unlink($this->tempFile);
            }
        }
    }
    
    public function preRemoveUpload($media) {
        $this->tempFile = $this->getAbsolutePath($media);
    }
    
    public function postRemoveUpload() {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
    
    //events listener___________________________________________________________

    public function prePersist(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->preUpload($args->getEntity());
        }
    }
    
    public function preUpdate(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->preUpload($args->getEntity());
        }
    }
    
    public function postPersist(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->postUpload($args->getEntity());
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->postUpload($args->getEntity());
        }
    }
    
    public function preRemove(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->preRemoveUpload($args->getEntity());
        }
    }
    
    public function postRemove(LifecycleEventArgs $args) {
        if ($args->getEntity() instanceof Media) {
            $this->postRemoveUpload();
        }
    }
}
