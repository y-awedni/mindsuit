<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneDevis;

/**
 * Devis
 *
 * @ORM\Table(name="devis", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\DevisRepository")
 * @UniqueEntity("code")
 */
class Devis {

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
     * @ORM\Column(name="ht", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ht = '0.000';
    
    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $remise = '0.000';
    
    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $tva = '0.000';
    
    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $total = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="net_a_payer", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $netAPayer = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="termine", type="boolean", length=65535, nullable=true)
     */
    private $termine=false;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateCreation;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="date_validite", type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateValidite;

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
     * @var \Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $client;

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
     * @ORM\OneToMany(targetEntity="LigneDevis", mappedBy="devis",cascade={"all"})
     * @Assert\Valid()
     */
    protected $lignesDevis;
    
    

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
     * @return Devis
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
     * Set ht
     *
     * @param string $ht
     *
     * @return Devis
     */
    public function setHt($ht) {
        $this->ht = $ht;

        return $this;
    }

    /**
     * Get ht
     *
     * @return string
     */
    public function getHt() {
        return $this->ht;
    }
    
    /**
     * Set remise
     *
     * @param string $remise
     *
     * @return Devis
     */
    public function setRemise($remise) {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return string
     */
    public function getRemise() {
        return $this->remise;
    }
    
    /**
     * Set tva
     *
     * @param string $tva
     *
     * @return Devis
     */
    public function setTva($tva) {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva() {
        return $this->tva;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Devis
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set netAPayer
     *
     * @param string $netAPayer
     *
     * @return Devis
     */
    public function setNetAPayer($netAPayer) {
        $this->netAPayer = $netAPayer;

        return $this;
    }

    /**
     * Get netAPayer
     *
     * @return string
     */
    public function getNetAPayer() {
        return $this->netAPayer;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Devis
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
     * @return Devis
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
     * @param string $dateCreation
     *
     * @return Devis
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return string
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }
    
    /**
     * Set dateValidite
     *
     * @param string $dateValidite
     *
     * @return Devis
     */
    public function setDateValidite($dateValidite) {
        $this->dateValidite = $dateValidite;

        return $this;
    }

    /**
     * Get dateValidite
     *
     * @return string
     */
    public function getDateValidite() {
        return $this->dateValidite;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Devis
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
     * @return Devis
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
     * Set client
     *
     * @param \App\Entity\Client $client
     *
     * @return Devis
     */
    public function setClient(\App\Entity\Client $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \App\Entity\Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return Devis
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
     * @return Devis
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
        $this->lignesDevis = new ArrayCollection();
        $this->dateCreation=new \DateTime();
        $this->dateValidite=new \DateTime();
    }

    /**
     * Get lignesDevis
     *
     * @return \App\Entity\LigneDevis
     */
    public function getLignesDevis() {
        return $this->lignesDevis;
    }

    public function addLignesDevi(LigneDevis $l) {
        $l->setDevis($this);
        $this->lignesDevis->add($l);
    }

    public function removeLignesDevi(LigneDevis $ligneDevis) {
        $this->lignesDevis->removeElement($ligneDevis);
    }
    

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cin", type="string", length=255, nullable=true)
     */
    private $cin;
    
    
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Devis
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }
    
    /**
     * Set cin
     *
     * @param string $cin
     *
     * @return Devis
     */
    public function setCin($cin) {
        $this->cin = $cin;

        return $this;
    }

    /**
     * Get cin
     *
     * @return string
     */
    public function getCin() {
        return $this->cin;
    }
}
