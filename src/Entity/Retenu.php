<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Retenu
 *
 * @ORM\Table(name="retenu", uniqueConstraints={@ORM\UniqueConstraint(name="taux", columns={"taux"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"})})
 * @ORM\Entity
 * @UniqueEntity("taux")
 */
class Retenu {

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
     * @ORM\Column(name="taux", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     * )
     * @Assert\Type("numeric")
     */
    private $taux;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     * @Assert\Range(
     *      min = 0
     * )
     */
    private $montant;

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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id")
     * })
     */
    private $updatedUser;

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
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set taux
     *
     * @param string $taux
     *
     * @return Retenu
     */
    public function setTaux($taux) {
        $this->taux = $taux;

        return $this;
    }

    /**
     * Get taux
     *
     * @return string
     */
    public function getTaux() {
        return $this->taux;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return Retenu
     */
    public function setMontant($montant) {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant() {
        return $this->montant;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Retenu
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
     * @return Retenu
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
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return Retenu
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

    /**
     * Set createdUser
     *
     * @param \App\Entity\User $createdUser
     *
     * @return Retenu
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
    
    public function __toString() {
        return $this->taux;
    }

}
