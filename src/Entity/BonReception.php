<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneBonReception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BonReception
 *
 * @ORM\Table(name="bon_reception", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="fournisseur_id", columns={"fournisseur_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\BonReceptionRepository")
 * @UniqueEntity("code")
 * @ORM\HasLifecycleCallbacks()
 */
class BonReception {

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
     * @var \BonCommandeFrs
     *
     * @ORM\ManyToOne(targetEntity="BonCommandeFrs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_commande_id", referencedColumnName="id")
     * })
     */
    private $bonCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="ht", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ht;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $remise;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $tva;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $total;
    
    /**
     * @var string
     *
     * @ORM\Column(name="taux_retenu", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $tauxRetenu = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_retenu", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $totalRetenu = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="regle", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $regle = '0.000';
    
    /**
     * @var string
     *
     * @ORM\Column(name="reste", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $reste ;

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
    private $termine;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reception", type="date", nullable=true)
     * @Assert\NotBlank()
     */
    private $dateReception;

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
     * 
     */
    private $createdUser;

    /**
     * @var \Fournisseur
     *
     * @ORM\ManyToOne(targetEntity="Fournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fournisseur_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $fournisseur;

    /**
     * @ORM\OneToMany(targetEntity="LigneBonReception", mappedBy="bonReception",cascade={"all"})
     * @Assert\Valid()
     */
    protected $ligneBonReceptions;

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
     * @return BonReception
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
     * Set bonCommande
     *
     * @param \App\Entity\BonCommandeFrs $bonCommande
     *
     * @return BonCommandeFrs
     */
    public function setBonCommande(\App\Entity\BonCommandeFrs $bonCommande = null) {
        $this->bonCommande = $bonCommande;

        return $this;
    }

    /**
     * Get bonCommande
     *
     * @return \App\Entity\BonCommandeFrs
     */
    public function getBonCommande() {
        return $this->bonCommande;
    }

    /**
     * Set ht
     *
     * @param string $ht
     *
     * @return BonReception
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
     * @return BonReception
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
     * @return BonReception
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
     * @return BonReception
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
     * Set tauxRetenu
     *
     * @param string $tauxRetenu
     *
     * @return BonReception
     */
    public function setTauxRetenu($tauxRetenu) {
        $this->tauxRetenu = $tauxRetenu;

        return $this;
    }

    /**
     * Get tauxRetenu
     *
     * @return string
     */
    public function getTauxRetenu() {
        return $this->tauxRetenu;
    }
    
    /**
     * Set totalRetenu
     *
     * @param string $totalRetenu
     *
     * @return BonReception
     */
    public function setTotalRetenu($totalRetenu) {
        $this->totalRetenu = $totalRetenu;

        return $this;
    }

    /**
     * Get totalRetenu
     *
     * @return string
     */
    public function getTotalRetenu() {
        return $this->totalRetenu;
    }

    /**
     * Set regle
     *
     * @param string $regle
     *
     * @return BonReception
     */
    public function setRegle($regle) {
        $this->regle = $regle;

        return $this;
    }

    /**
     * Get regle
     *
     * @return string
     */
    public function getRegle() {
        return $this->regle;
    }
    
    /**
     * Set reste
     *
     * @param string $reste
     *
     * @return BonReception
     */
    public function setReste($reste) {
        $this->reste = $reste;

        return $this;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return BonReception
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
     * @return BonReception
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
     * Set dateReception
     *
     * @param \DateTime $dateReception
     *
     * @return BonReception
     */
    public function setDateReception($dateReception) {
        $this->dateReception = $dateReception;

        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime
     */
    public function getDateReception() {
        return $this->dateReception;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return BonReception
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
     * @return BonReception
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
     * @return BonReception
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
     * @return BonReception
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
     * Set fournisseur
     *
     * @param \App\Entity\Fournisseur $fournisseur
     *
     * @return BonReception
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

    public function __toString() {
        return "" . $this->code;
    }

    public function __construct() {
        $this->ligneBonReceptions = new ArrayCollection();
        $this->dateReception=new \DateTime();
    }

    /**
     * Get ligneBonReceptions
     *
     * @return \App\Entity\LigneBonReception
     */
    public function getLigneBonReceptions() {
        return $this->ligneBonReceptions;
    }

    public function addLigneBonReception(LigneBonReception $l) {
        $l->setBonReception($this);
        $this->ligneBonReceptions->add($l);
    }

    public function removeLigneBonReception(LigneBonReception $l) {
        $this->ligneBonReceptions->removeElement($l);
    }

    public function getReste() {
        return $this->total - $this->regle;
    }
    
    public function isTermine(){
        return $this->termine===true;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setResteValue() {
        $this->reste = $this->total;
    }

}
