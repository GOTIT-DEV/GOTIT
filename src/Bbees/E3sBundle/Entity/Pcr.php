<?php

namespace Bbees\E3sBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pcr
 *
 * @ORM\Table(name="pcr", uniqueConstraints={@ORM\UniqueConstraint(name="cu_pcr_cle_primaire", columns={"code_pcr"})}, indexes={@ORM\Index(name="IDX_5B6B99369D3CDB05", columns={"gene_voc_fk"}), @ORM\Index(name="IDX_5B6B99368B4A1710", columns={"qualite_pcr_voc_fk"}), @ORM\Index(name="IDX_5B6B99366CCC2566", columns={"specificite_voc_fk"}), @ORM\Index(name="IDX_5B6B99362C5B04A7", columns={"primer_pcr_start_voc_fk"}), @ORM\Index(name="IDX_5B6B9936F1694267", columns={"primer_pcr_end_voc_fk"}), @ORM\Index(name="IDX_5B6B9936A30C442F", columns={"date_precision_voc_fk"}), @ORM\Index(name="IDX_5B6B99364B06319D", columns={"adn_fk"})})
 * @ORM\Entity
 */
class Pcr
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="pcr_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code_pcr", type="string", length=255, nullable=false)
     */
    private $codePcr;

    /**
     * @var string
     *
     * @ORM\Column(name="num_pcr", type="string", length=255, nullable=false)
     */
    private $numPcr;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_pcr", type="date", nullable=true)
     */
    private $datePcr;

    /**
     * @var string
     *
     * @ORM\Column(name="detail_pcr", type="text", nullable=true)
     */
    private $detailPcr;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque_pcr", type="text", nullable=true)
     */
    private $remarquePcr;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_cre", type="datetime", nullable=true)
     */
    private $dateCre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_maj", type="datetime", nullable=true)
     */
    private $dateMaj;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_cre", type="bigint", nullable=true)
     */
    private $userCre;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_maj", type="bigint", nullable=true)
     */
    private $userMaj;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id")
     * })
     */
    private $geneVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="qualite_pcr_voc_fk", referencedColumnName="id")
     * })
     */
    private $qualitePcrVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specificite_voc_fk", referencedColumnName="id")
     * })
     */
    private $specificiteVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primer_pcr_start_voc_fk", referencedColumnName="id")
     * })
     */
    private $primerPcrStartVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primer_pcr_end_voc_fk", referencedColumnName="id")
     * })
     */
    private $primerPcrEndVocFk;

    /**
     * @var \Voc
     *
     * @ORM\ManyToOne(targetEntity="Voc")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id")
     * })
     */
    private $datePrecisionVocFk;

    /**
     * @var \Adn
     *
     * @ORM\ManyToOne(targetEntity="Adn")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="adn_fk", referencedColumnName="id")
     * })
     */
    private $adnFk;

    
    /**
     * @ORM\OneToMany(targetEntity="PcrEstRealisePar", mappedBy="pcrFk", cascade={"persist"})
     */
    protected $pcrEstRealisePars;
    
    
    
    public function __construct()
    {
        $this->pcrEstRealisePars = new ArrayCollection();
    }


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
     * Set codePcr
     *
     * @param string $codePcr
     *
     * @return Pcr
     */
    public function setCodePcr($codePcr)
    {
        $this->codePcr = $codePcr;

        return $this;
    }

    /**
     * Get codePcr
     *
     * @return string
     */
    public function getCodePcr()
    {
        return $this->codePcr;
    }

    /**
     * Set numPcr
     *
     * @param string $numPcr
     *
     * @return Pcr
     */
    public function setNumPcr($numPcr)
    {
        $this->numPcr = $numPcr;

        return $this;
    }

    /**
     * Get numPcr
     *
     * @return string
     */
    public function getNumPcr()
    {
        return $this->numPcr;
    }

    /**
     * Set datePcr
     *
     * @param \DateTime $datePcr
     *
     * @return Pcr
     */
    public function setDatePcr($datePcr)
    {
        $this->datePcr = $datePcr;

        return $this;
    }

    /**
     * Get datePcr
     *
     * @return \DateTime
     */
    public function getDatePcr()
    {
        return $this->datePcr;
    }

    /**
     * Set detailPcr
     *
     * @param string $detailPcr
     *
     * @return Pcr
     */
    public function setDetailPcr($detailPcr)
    {
        $this->detailPcr = $detailPcr;

        return $this;
    }

    /**
     * Get detailPcr
     *
     * @return string
     */
    public function getDetailPcr()
    {
        return $this->detailPcr;
    }

    /**
     * Set remarquePcr
     *
     * @param string $remarquePcr
     *
     * @return Pcr
     */
    public function setRemarquePcr($remarquePcr)
    {
        $this->remarquePcr = $remarquePcr;

        return $this;
    }

    /**
     * Get remarquePcr
     *
     * @return string
     */
    public function getRemarquePcr()
    {
        return $this->remarquePcr;
    }

    /**
     * Set dateCre
     *
     * @param \DateTime $dateCre
     *
     * @return Pcr
     */
    public function setDateCre($dateCre)
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    /**
     * Get dateCre
     *
     * @return \DateTime
     */
    public function getDateCre()
    {
        return $this->dateCre;
    }

    /**
     * Set dateMaj
     *
     * @param \DateTime $dateMaj
     *
     * @return Pcr
     */
    public function setDateMaj($dateMaj)
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    /**
     * Get dateMaj
     *
     * @return \DateTime
     */
    public function getDateMaj()
    {
        return $this->dateMaj;
    }

    /**
     * Set userCre
     *
     * @param integer $userCre
     *
     * @return Pcr
     */
    public function setUserCre($userCre)
    {
        $this->userCre = $userCre;

        return $this;
    }

    /**
     * Get userCre
     *
     * @return integer
     */
    public function getUserCre()
    {
        return $this->userCre;
    }

    /**
     * Set userMaj
     *
     * @param integer $userMaj
     *
     * @return Pcr
     */
    public function setUserMaj($userMaj)
    {
        $this->userMaj = $userMaj;

        return $this;
    }

    /**
     * Get userMaj
     *
     * @return integer
     */
    public function getUserMaj()
    {
        return $this->userMaj;
    }

    /**
     * Set geneVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $geneVocFk
     *
     * @return Pcr
     */
    public function setGeneVocFk(\Bbees\E3sBundle\Entity\Voc $geneVocFk = null)
    {
        $this->geneVocFk = $geneVocFk;

        return $this;
    }

    /**
     * Get geneVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getGeneVocFk()
    {
        return $this->geneVocFk;
    }

    /**
     * Set qualitePcrVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $qualitePcrVocFk
     *
     * @return Pcr
     */
    public function setQualitePcrVocFk(\Bbees\E3sBundle\Entity\Voc $qualitePcrVocFk = null)
    {
        $this->qualitePcrVocFk = $qualitePcrVocFk;

        return $this;
    }

    /**
     * Get qualitePcrVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getQualitePcrVocFk()
    {
        return $this->qualitePcrVocFk;
    }

    /**
     * Set specificiteVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $specificiteVocFk
     *
     * @return Pcr
     */
    public function setSpecificiteVocFk(\Bbees\E3sBundle\Entity\Voc $specificiteVocFk = null)
    {
        $this->specificiteVocFk = $specificiteVocFk;

        return $this;
    }

    /**
     * Get specificiteVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getSpecificiteVocFk()
    {
        return $this->specificiteVocFk;
    }

    /**
     * Set primerPcrStartVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $primerPcrStartVocFk
     *
     * @return Pcr
     */
    public function setPrimerPcrStartVocFk(\Bbees\E3sBundle\Entity\Voc $primerPcrStartVocFk = null)
    {
        $this->primerPcrStartVocFk = $primerPcrStartVocFk;

        return $this;
    }

    /**
     * Get primerPcrStartVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getPrimerPcrStartVocFk()
    {
        return $this->primerPcrStartVocFk;
    }

    /**
     * Set primerPcrEndVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $primerPcrEndVocFk
     *
     * @return Pcr
     */
    public function setPrimerPcrEndVocFk(\Bbees\E3sBundle\Entity\Voc $primerPcrEndVocFk = null)
    {
        $this->primerPcrEndVocFk = $primerPcrEndVocFk;

        return $this;
    }

    /**
     * Get primerPcrEndVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getPrimerPcrEndVocFk()
    {
        return $this->primerPcrEndVocFk;
    }

    /**
     * Set datePrecisionVocFk
     *
     * @param \Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk
     *
     * @return Pcr
     */
    public function setDatePrecisionVocFk(\Bbees\E3sBundle\Entity\Voc $datePrecisionVocFk = null)
    {
        $this->datePrecisionVocFk = $datePrecisionVocFk;

        return $this;
    }

    /**
     * Get datePrecisionVocFk
     *
     * @return \Bbees\E3sBundle\Entity\Voc
     */
    public function getDatePrecisionVocFk()
    {
        return $this->datePrecisionVocFk;
    }

    /**
     * Set adnFk
     *
     * @param \Bbees\E3sBundle\Entity\Adn $adnFk
     *
     * @return Pcr
     */
    public function setAdnFk(\Bbees\E3sBundle\Entity\Adn $adnFk = null)
    {
        $this->adnFk = $adnFk;

        return $this;
    }

    /**
     * Get adnFk
     *
     * @return \Bbees\E3sBundle\Entity\Adn
     */
    public function getAdnFk()
    {
        return $this->adnFk;
    }

    /**
     * Add pcrEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\PcrEstRealisePar $pcrEstRealisePar
     *
     * @return Pcr
     */
    public function addPcrEstRealisePar(\Bbees\E3sBundle\Entity\PcrEstRealisePar $pcrEstRealisePar)
    {
        $pcrEstRealisePar->setPcrFk($this);
        $this->pcrEstRealisePars[] = $pcrEstRealisePar;

        return $this;
    }

    /**
     * Remove pcrEstRealisePar
     *
     * @param \Bbees\E3sBundle\Entity\PcrEstRealisePar $pcrEstRealisePar
     */
    public function removePcrEstRealisePar(\Bbees\E3sBundle\Entity\PcrEstRealisePar $pcrEstRealisePar)
    {
        $this->pcrEstRealisePars->removeElement($pcrEstRealisePar);
    }

    /**
     * Get pcrEstRealisePars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPcrEstRealisePars()
    {
        return $this->pcrEstRealisePars;
    }
}
