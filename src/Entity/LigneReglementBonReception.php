<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LigneReglementBonReception
 *
 * @ORM\Table(name="ligne_reglement_bon_reception", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="bon_reception_id", columns={"bon_reception_id"}), @ORM\Index(name="reglement_id", columns={"reglement_id"}), @ORM\Index(name="mode_reglement_id", columns={"mode_reglement"}), @ORM\Index(name="updated_user_id_2", columns={"updated_user_id"}), @ORM\Index(name="banque_rec_id", columns={"compte_id"}), @ORM\Index(name="compte_id", columns={"compte_id"}), @ORM\Index(name="compte_id_2", columns={"compte_id"})})
 * @ORM\Entity
 */
class LigneReglementBonReception {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reglement", type="date", nullable=false)
     */
    private $dateReglement;

    /**
     * @var string
     *
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="mode_reglement", type="string", length=255, nullable=false)
     */
    private $modeReglement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance_cheque", type="date", nullable=true)
     */
    private $dateEcheanceCheque;

    /**
     * @var string
     *
     * @ORM\Column(name="num_cheque", type="string", length=255, nullable=true)
     */
    private $numCheque;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance_traite", type="date", nullable=true)
     */
    private $dateEcheanceTraite;

    /**
     * @var string
     *
     * @ORM\Column(name="num_traite", type="string", length=255, nullable=true)
     */
    private $numTraite;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

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
     * @var \BonReception
     *
     * @ORM\ManyToOne(targetEntity="BonReception")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_reception_id", referencedColumnName="id")
     * })
     */
    private $bonReception;

    /**
     * @var \Reglement
     *
     * @ORM\ManyToOne(targetEntity="Reglement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="reglement_id", referencedColumnName="id")
     * })
     */
    private $reglement;

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
     * @var \Compte
     *
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_id", referencedColumnName="id")
     * })
     */
    private $compte;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set dateReglement
     *
     * @param \DateTime $dateReglement
     *
     * @return LigneReglementBonReception
     */
    public function setDateReglement($dateReglement) {
        $this->dateReglement = $dateReglement;

        return $this;
    }

    /**
     * Get dateReglement
     *
     * @return \DateTime
     */
    public function getDateReglement() {
        return $this->dateReglement;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return LigneReglementBonReception
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
     * Set modeReglement
     *
     * @param string $modeReglement
     *
     * @return LigneReglementBonReception
     */
    public function setModeReglement($modeReglement) {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return string
     */
    public function getModeReglement() {
        return $this->modeReglement;
    }

    /**
     * Set dateEcheanceCheque
     *
     * @param \DateTime $dateEcheanceCheque
     *
     * @return LigneReglementBonReception
     */
    public function setDateEcheanceCheque($dateEcheanceCheque) {
        $this->dateEcheanceCheque = $dateEcheanceCheque;

        return $this;
    }

    /**
     * Get dateEcheanceCheque
     *
     * @return \DateTime
     */
    public function getDateEcheanceCheque() {
        return $this->dateEcheanceCheque;
    }

    /**
     * Set numCheque
     *
     * @param string $numCheque
     *
     * @return LigneReglementBonReception
     */
    public function setNumCheque($numCheque) {
        $this->numCheque = $numCheque;

        return $this;
    }

    /**
     * Get numCheque
     *
     * @return string
     */
    public function getNumCheque() {
        return $this->numCheque;
    }

    /**
     * Set dateEcheanceTraite
     *
     * @param \DateTime $dateEcheanceTraite
     *
     * @return LigneReglementBonReception
     */
    public function setDateEcheanceTraite($dateEcheanceTraite) {
        $this->dateEcheanceTraite = $dateEcheanceTraite;

        return $this;
    }

    /**
     * Get dateEcheanceTraite
     *
     * @return \DateTime
     */
    public function getDateEcheanceTraite() {
        return $this->dateEcheanceTraite;
    }

    /**
     * Set numTraite
     *
     * @param string $numTraite
     *
     * @return LigneReglementBonReception
     */
    public function setNumTraite($numTraite) {
        $this->numTraite = $numTraite;

        return $this;
    }

    /**
     * Get numTraite
     *
     * @return string
     */
    public function getNumTraite() {
        return $this->numTraite;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return LigneReglementBonReception
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return LigneReglementBonReception
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return LigneReglementBonReception
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
     * @return LigneReglementBonReception
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
     * Set bonReception
     *
     * @param \App\Entity\BonReception $bonReception
     *
     * @return LigneReglementBonReception
     */
    public function setBonReception(\App\Entity\BonReception $bonReception = null) {
        $this->bonReception = $bonReception;

        return $this;
    }

    /**
     * Get bonReception
     *
     * @return \App\Entity\BonReception
     */
    public function getBonReception() {
        return $this->bonReception;
    }

    /**
     * Set reglement
     *
     * @param \App\Entity\Reglement $reglement
     *
     * @return LigneReglementBonReception
     */
    public function setReglement(\App\Entity\Reglement $reglement = null) {
        $this->reglement = $reglement;

        return $this;
    }

    /**
     * Get reglement
     *
     * @return \App\Entity\Reglement
     */
    public function getReglement() {
        return $this->reglement;
    }

    /**
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return LigneReglementBonReception
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
     * @return LigneReglementBonReception
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
     * Set compte
     *
     * @param \App\Entity\Compte $compte
     *
     * @return LigneReglementBonReception
     */
    public function setCompte(\App\Entity\Compte $compte = null) {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return \App\Entity\Compte
     */
    public function getCompte() {
        return $this->compte;
    }

    public function getDetails(){
        switch ($this->modeReglement){
            case "Espéce":
                return "";
            case "Chéque":
                return "Date écheance : ".date_format($this->dateEcheanceCheque,"d-m-Y") .PHP_EOL."N°chéque : ".$this->numCheque;
            case "Traite":
                return "Date écheance : ".date_format($this->dateEcheanceTraite,"d-m-Y").PHP_EOL."N°traite : ".$this->numTraite;
            case "Virement":
                return "Compte : ".$this->compte;
            default :
                return "";
        }
    }
    
    public function __construct() {
        $this->dateReglement=new \DateTime();
    }
}
