<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte
 *
 * @ORM\Table(name="compte", indexes={@ORM\Index(name="banque_id", columns={"banque_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="updated_user_id", columns={"updated_user_id"})})
 * @ORM\Entity
 */
class Compte {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="rub", type="string", length=255, nullable=false)
     */
    private $rub;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \Banque
     *
     * @ORM\ManyToOne(targetEntity="Banque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="banque_id", referencedColumnName="id")
     * })
     */
    private $banque;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
     * })
     */
    private $createdUser;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id")
     * })
     */
    private $updatedUser;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set rub
     *
     * @param string $rub
     *
     * @return Compte
     */
    public function setRub($rub) {
        $this->rub = $rub;

        return $this;
    }

    /**
     * Get rub
     *
     * @return string
     */
    public function getRub() {
        return $this->rub;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Compte
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Compte
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set banque
     *
     * @param \App\Entity\Banque $banque
     *
     * @return Compte
     */
    public function setBanque(\App\Entity\Banque $banque = null) {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return \App\Entity\Banque
     */
    public function getBanque() {
        return $this->banque;
    }

    /**
     * Set createdUser
     *
     * @param \App\Entity\User $createdUser
     *
     * @return Compte
     */
    public function setCreatedUser(\App\Entity\User $createdUser = null) {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get createdUser
     *
     * @return \App\Entity\User
     */
    public function getCreatedUser() {
        return $this->createdUser;
    }

    /**
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return Compte
     */
    public function setUpdatedUser(\App\Entity\User $updatedUser = null) {
        $this->updatedUser = $updatedUser;

        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \App\Entity\User
     */
    public function getUpdatedUser() {
        return $this->updatedUser;
    }
    public function __toString() {
        return $this->rub." ".$this->banque;
    }
}
