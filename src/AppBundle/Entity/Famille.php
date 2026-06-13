<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Famille
 *
 * @ORM\Table(name="famille", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"})})
 * @ORM\Entity
 * @UniqueEntity("libelle")
 */
class Famille {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"famille", "categorie"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Groups({"famille", "categorie"})
     */
    private $libelle;
    
    /**
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie",inversedBy="familles",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     * @Groups({"categorie"})
     */
    private $categorie;

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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Sousfamille", mappedBy="famille",cascade={"all"})
     */
    protected $sousFamilles;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return Famille
     */
    public function setLibelle($libelle) {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle() {
        return $this->libelle;
    }
    
    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Famille
     */
    public function setCategorie($categorie) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Stock
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
     * @return Stock
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
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return Tva
     */
    public function setUpdatedUser(\AppBundle\Entity\User $updatedUser = null) {
        $this->updatedUser = $updatedUser;


        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getUpdatedUser() {
        return $this->updatedUser;
    }

    /**
     * Set createdUser
     *
     * @param \AppBundle\Entity\User $createdUser
     *
     * @return Tva
     */
    public function setCreatedUser(\AppBundle\Entity\User $createdUser = null) {
        $this->createdUser = $createdUser;

        return $this;
    }
    
    /**
     * Get sousFamilles
     *
     * @return \AppBundle\Entity\SousFamille
     */
    public function getSousFamilles() {
        return $this->sousFamilles;
    }

    /**
     * Get createdUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedUser() {
        return $this->createdUser;
    }

    public function __toString() {
        return $this->libelle;
    }

}
