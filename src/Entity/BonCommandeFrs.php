<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\LigneBonCommandeFrs;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * BonCommandeFrs
 *
 * @ORM\Table(name="bon_commande_frs", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="fournisseur_id", columns={"fournisseur_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BonCommandeFrsRepository")
 * @UniqueEntity("code")
 */
class BonCommandeFrs {

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
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="termine", type="boolean", nullable=false)
     */
    private $termine = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_commande", type="date", nullable=true)
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $dateCommande;

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
     * @var \Fournisseur
     *
     * @ORM\ManyToOne(targetEntity="Fournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fournisseur_id", referencedColumnName="id")
     * })
     */
    private $fournisseur;

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
     * @ORM\OneToMany(targetEntity="LigneBonCommandeFrs", mappedBy="bonCommandeFrs",cascade={"all"})
     * @Assert\Valid()
     */
    protected $ligneBonCommandeFrss;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return BonCommandeFrs
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return BonCommandeFrs
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Set termine
     *
     * @param boolean $termine
     *
     * @return BonCommandeFrs
     */
    public function setTermine($termine) {
        $this->termine = $termine;

        return $this;
    }

    /**
     * Get termine
     *
     * @return boolean
     */
    public function getTermine() {
        return $this->termine;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return BonCommandeFrs
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return BonCommandeFrs
     */
    public function setDateCommande($dateCommande) {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande() {
        return $this->dateCommande;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BonCommandeFrs
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
     * @return BonCommandeFrs
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
     * Set fournisseur
     *
     * @param \App\Entity\Fournisseur $fournisseur
     *
     * @return BonCommandeFrs
     */
    public function setFournisseur(\App\Entity\Fournisseur $fournisseur = null) {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \App\Entity\Fournisseur
     */
    public function getFournisseur() {
        return $this->fournisseur;
    }

    /**
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return BonCommandeFrs
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
     * @return BonCommandeFrs
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
        return "" . $this->code;
    }

    public function __construct() {
        $this->ligneBonCommandeFrss = new ArrayCollection();
        $this->dateCommande = new \DateTime();
        $this->dateCreation = new \DateTime();
    }

    /**
     * Get ligneBonCommandeFrss
     *
     * @return \App\Entity\LigneBonCommandeFrs
     */
    public function getLigneBonCommandeFrss() {
        return $this->ligneBonCommandeFrss;
    }

    public function addLigneBonCommandeFrs(LigneBonCommandeFrs $l) {
        $l->setBonCommandeFrs($this);
        $this->ligneBonCommandeFrss->add($l);
    }

    public function removeLigneBonCommandeFrs(LigneBonCommandeFrs $l) {
        $this->ligneBonCommandeFrss->removeElement($l);
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
        $verif = true;
        if(count($this->getLigneBonCommandeFrss())===0){
            $verif=false;
        }
        if (!$verif) {
            $context->buildViolation('Il faut ajouter au moins une ligne')
                    ->atPath('ligneBonCommandeFrss')
                    ->addViolation();
        }
    }

}
