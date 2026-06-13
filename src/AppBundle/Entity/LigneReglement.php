<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * LigneReglement
 *
 * @ORM\Table(name="ligne_reglement", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="facture_id", columns={"facture_id"}), @ORM\Index(name="reglement_id", columns={"reglement_id"}), @ORM\Index(name="mode_reglement_id", columns={"mode_reglement_id"}), @ORM\Index(name="updated_user_id_2", columns={"updated_user_id"}), @ORM\Index(name="banque_rec_id", columns={"compte_id"}), @ORM\Index(name="compte_id", columns={"compte_id"}), @ORM\Index(name="compte_id_2", columns={"compte_id"})})
 * @ORM\Entity
 */
class LigneReglement
{
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
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=3, nullable=false)
     * 
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $montant;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="date_reglement", type="date", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $dateReglement;

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
     * @ORM\Column(name="mode_reglement", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $modeReglement;

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
     * @var \Facture
     *
     * @ORM\ManyToOne(targetEntity="Facture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     * })
     */
    private $facture;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set montant
     *
     * @param string $montant
     *
     * @return LigneReglement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }
    
    /**
     * Set dateReglement
     *
     * @param string $dateReglement
     *
     * @return LigneReglement
     */
    public function setDateReglement($dateReglement) {
        $this->dateReglement = $dateReglement;

        return $this;
    }

    /**
     * Get dateReglement
     *
     * @return string
     */
    public function getDateReglement() {
        return $this->dateReglement;
    }

    /**
     * Set dateEcheanceCheque
     *
     * @param \DateTime $dateEcheanceCheque
     *
     * @return LigneReglement
     */
    public function setDateEcheanceCheque($dateEcheanceCheque)
    {
        $this->dateEcheanceCheque = $dateEcheanceCheque;

        return $this;
    }

    /**
     * Get dateEcheanceCheque
     *
     * @return \DateTime
     */
    public function getDateEcheanceCheque()
    {
        return $this->dateEcheanceCheque;
    }

    /**
     * Set numCheque
     *
     * @param string $numCheque
     *
     * @return LigneReglement
     */
    public function setNumCheque($numCheque)
    {
        $this->numCheque = $numCheque;

        return $this;
    }

    /**
     * Get numCheque
     *
     * @return string
     */
    public function getNumCheque()
    {
        return $this->numCheque;
    }

    /**
     * Set dateEcheanceTraite
     *
     * @param \DateTime $dateEcheanceTraite
     *
     * @return LigneReglement
     */
    public function setDateEcheanceTraite($dateEcheanceTraite)
    {
        $this->dateEcheanceTraite = $dateEcheanceTraite;

        return $this;
    }

    /**
     * Get dateEcheanceTraite
     *
     * @return \DateTime
     */
    public function getDateEcheanceTraite()
    {
        return $this->dateEcheanceTraite;
    }

    /**
     * Set numTraite
     *
     * @param string $numTraite
     *
     * @return LigneReglement
     */
    public function setNumTraite($numTraite)
    {
        $this->numTraite = $numTraite;

        return $this;
    }

    /**
     * Get numTraite
     *
     * @return string
     */
    public function getNumTraite()
    {
        return $this->numTraite;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return LigneReglement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return LigneReglement
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return LigneReglement
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return LigneReglement
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return LigneReglement
     */
    public function setFacture(\AppBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AppBundle\Entity\Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Set reglement
     *
     * @param \AppBundle\Entity\Reglement $reglement
     *
     * @return LigneReglement
     */
    public function setReglement(\AppBundle\Entity\Reglement $reglement = null)
    {
        $this->reglement = $reglement;

        return $this;
    }

    /**
     * Get reglement
     *
     * @return \AppBundle\Entity\Reglement
     */
    public function getReglement()
    {
        return $this->reglement;
    }

    /**
     * Set modeReglement
     *
     * @param string $modeReglement
     *
     * @return LigneReglement
     */
    public function setModeReglement($modeReglement)
    {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return string
     */
    public function getModeReglement()
    {
        return $this->modeReglement;
    }

    /**
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return LigneReglement
     */
    public function setUpdatedUser(\AppBundle\Entity\User $updatedUser = null)
    {
        $this->updatedUser = $updatedUser;

        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getUpdatedUser()
    {
        return $this->updatedUser;
    }

    /**
     * Set createdUser
     *
     * @param \AppBundle\Entity\User $createdUser
     *
     * @return LigneReglement
     */
    public function setCreatedUser(\AppBundle\Entity\User $createdUser = null)
    {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get createdUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }

    /**
     * Set compte
     *
     * @param \AppBundle\Entity\Compte $compte
     *
     * @return LigneReglement
     */
    public function setCompte(\AppBundle\Entity\Compte $compte = null)
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return \AppBundle\Entity\Compte
     */
    public function getCompte()
    {
        return $this->compte;
    }
    
    
    public function getDetails(){
        switch ($this->modeReglement){
            case "Espéce":
                return "";
            case "Chéque":
                $date="Non défini";
                if($this->dateEcheanceCheque){
                    $date=date_format($this->dateEcheanceCheque,"d-m-Y");
                }
                $numCheque="Non défini";
                if($this->numCheque){
                    $numCheque=$this->numCheque;
                }
                return "Date écheance : ". $date.PHP_EOL."N°chéque : ".$numCheque;
            case "Traite":
                $date="Non défini";
                if($this->dateEcheanceTraite){
                    $date=date_format($this->dateEcheanceTraite,"d-m-Y");
                }
                $numTraite="Non défini";
                if($this->numTraite){
                    $numTraite=$this->numTraite;
                }
                return "Date écheance : ".$date.PHP_EOL."N°traite : ".$numTraite;
            case "Virement":
                return "Compte : ".$this->compte;
            default :
                return "";
        }
    }
    
    public function __construct() {
        $this->dateReglement=new \DateTime();
    }
    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
        $totalAvoirNonRembourseClient=$this->getFacture()->getClient()->getTotalAvoirNonRembourse();
        if($this->getModeReglement()==='Avoir' and $this->montant > $totalAvoirNonRembourseClient){
            $context->buildViolation('Le montant doit étre <= '.$totalAvoirNonRembourseClient)
                    ->atPath('montant')
                    ->addViolation();
        }
    }
}
