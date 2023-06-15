<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Pcr
 *
 * @ORM\Table(name="pcr",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_pcr__pcr_code", columns={"pcr_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_5B6B99369D3CDB05", columns={"gene_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B99368B4A1710", columns={"pcr_quality_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B99366CCC2566", columns={"pcr_specificity_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B99362C5B04A7", columns={"forward_primer_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B9936F1694267", columns={"reverse_primer_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B9936A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_5B6B99364B06319D", columns={"dna_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codePcr"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Pcr {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="pcr_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_code", type="string", length=255, nullable=false)
   */
  private $codePcr;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_number", type="string", length=255, nullable=false)
   */
  private $numPcr;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="pcr_date", type="date", nullable=true)
   */
  private $datePcr;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_details", type="text", nullable=true)
   */
  private $detailPcr;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_comments", type="text", nullable=true)
   */
  private $remarquePcr;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
   */
  private $dateCre;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
   */
  private $dateMaj;

  /**
   * @var integer
   *
   * @ORM\Column(name="creation_user_name", type="bigint", nullable=true)
   */
  private $userCre;

  /**
   * @var integer
   *
   * @ORM\Column(name="update_user_name", type="bigint", nullable=true)
   */
  private $userMaj;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $geneVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="pcr_quality_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $qualitePcrVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="pcr_specificity_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $specificiteVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="forward_primer_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $primerPcrStartVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="reverse_primer_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $primerPcrEndVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $datePrecisionVocFk;

  /**
   * @var \Adn
   *
   * @ORM\ManyToOne(targetEntity="Adn")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $adnFk;

  /**
   * @ORM\OneToMany(targetEntity="PcrEstRealisePar", mappedBy="pcrFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $pcrEstRealisePars;
  
  /**
   * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="pcrFk", cascade={"persist"}, orphanRemoval=true)
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $especeIdentifiees;

  public function __construct() {
    $this->pcrEstRealisePars = new ArrayCollection();
    $this->especeIdentifiees = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set codePcr
   *
   * @param string $codePcr
   *
   * @return Pcr
   */
  public function setCodePcr($codePcr) {
    $this->codePcr = $codePcr;

    return $this;
  }

  /**
   * Get codePcr
   *
   * @return string
   */
  public function getCodePcr() {
    return $this->codePcr;
  }

  /**
   * Set numPcr
   *
   * @param string $numPcr
   *
   * @return Pcr
   */
  public function setNumPcr($numPcr) {
    $this->numPcr = $numPcr;

    return $this;
  }

  /**
   * Get numPcr
   *
   * @return string
   */
  public function getNumPcr() {
    return $this->numPcr;
  }

  /**
   * Set datePcr
   *
   * @param \DateTime $datePcr
   *
   * @return Pcr
   */
  public function setDatePcr($datePcr) {
    $this->datePcr = $datePcr;

    return $this;
  }

  /**
   * Get datePcr
   *
   * @return \DateTime
   */
  public function getDatePcr() {
    return $this->datePcr;
  }

  /**
   * Set detailPcr
   *
   * @param string $detailPcr
   *
   * @return Pcr
   */
  public function setDetailPcr($detailPcr) {
    $this->detailPcr = $detailPcr;

    return $this;
  }

  /**
   * Get detailPcr
   *
   * @return string
   */
  public function getDetailPcr() {
    return $this->detailPcr;
  }

  /**
   * Set remarquePcr
   *
   * @param string $remarquePcr
   *
   * @return Pcr
   */
  public function setRemarquePcr($remarquePcr) {
    $this->remarquePcr = $remarquePcr;

    return $this;
  }

  /**
   * Get remarquePcr
   *
   * @return string
   */
  public function getRemarquePcr() {
    return $this->remarquePcr;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return Pcr
   */
  public function setDateCre($dateCre) {
    $this->dateCre = $dateCre;

    return $this;
  }

  /**
   * Get dateCre
   *
   * @return \DateTime
   */
  public function getDateCre() {
    return $this->dateCre;
  }

  /**
   * Set dateMaj
   *
   * @param \DateTime $dateMaj
   *
   * @return Pcr
   */
  public function setDateMaj($dateMaj) {
    $this->dateMaj = $dateMaj;

    return $this;
  }

  /**
   * Get dateMaj
   *
   * @return \DateTime
   */
  public function getDateMaj() {
    return $this->dateMaj;
  }

  /**
   * Set userCre
   *
   * @param integer $userCre
   *
   * @return Pcr
   */
  public function setUserCre($userCre) {
    $this->userCre = $userCre;

    return $this;
  }

  /**
   * Get userCre
   *
   * @return integer
   */
  public function getUserCre() {
    return $this->userCre;
  }

  /**
   * Set userMaj
   *
   * @param integer $userMaj
   *
   * @return Pcr
   */
  public function setUserMaj($userMaj) {
    $this->userMaj = $userMaj;

    return $this;
  }

  /**
   * Get userMaj
   *
   * @return integer
   */
  public function getUserMaj() {
    return $this->userMaj;
  }

  /**
   * Set geneVocFk
   *
   * @param \App\Entity\Voc $geneVocFk
   *
   * @return Pcr
   */
  public function setGeneVocFk(\App\Entity\Voc $geneVocFk = null) {
    $this->geneVocFk = $geneVocFk;

    return $this;
  }

  /**
   * Get geneVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getGeneVocFk() {
    return $this->geneVocFk;
  }

  /**
   * Set qualitePcrVocFk
   *
   * @param \App\Entity\Voc $qualitePcrVocFk
   *
   * @return Pcr
   */
  public function setQualitePcrVocFk(\App\Entity\Voc $qualitePcrVocFk = null) {
    $this->qualitePcrVocFk = $qualitePcrVocFk;

    return $this;
  }

  /**
   * Get qualitePcrVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getQualitePcrVocFk() {
    return $this->qualitePcrVocFk;
  }

  /**
   * Set specificiteVocFk
   *
   * @param \App\Entity\Voc $specificiteVocFk
   *
   * @return Pcr
   */
  public function setSpecificiteVocFk(\App\Entity\Voc $specificiteVocFk = null) {
    $this->specificiteVocFk = $specificiteVocFk;

    return $this;
  }

  /**
   * Get specificiteVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getSpecificiteVocFk() {
    return $this->specificiteVocFk;
  }

  /**
   * Set primerPcrStartVocFk
   *
   * @param \App\Entity\Voc $primerPcrStartVocFk
   *
   * @return Pcr
   */
  public function setPrimerPcrStartVocFk(\App\Entity\Voc $primerPcrStartVocFk = null) {
    $this->primerPcrStartVocFk = $primerPcrStartVocFk;

    return $this;
  }

  /**
   * Get primerPcrStartVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPrimerPcrStartVocFk() {
    return $this->primerPcrStartVocFk;
  }

  /**
   * Set primerPcrEndVocFk
   *
   * @param \App\Entity\Voc $primerPcrEndVocFk
   *
   * @return Pcr
   */
  public function setPrimerPcrEndVocFk(\App\Entity\Voc $primerPcrEndVocFk = null) {
    $this->primerPcrEndVocFk = $primerPcrEndVocFk;

    return $this;
  }

  /**
   * Get primerPcrEndVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPrimerPcrEndVocFk() {
    return $this->primerPcrEndVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return Pcr
   */
  public function setDatePrecisionVocFk(\App\Entity\Voc $datePrecisionVocFk = null) {
    $this->datePrecisionVocFk = $datePrecisionVocFk;

    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getDatePrecisionVocFk() {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set adnFk
   *
   * @param \App\Entity\Adn $adnFk
   *
   * @return Pcr
   */
  public function setAdnFk(\App\Entity\Adn $adnFk = null) {
    $this->adnFk = $adnFk;

    return $this;
  }

  /**
   * Get adnFk
   *
   * @return \App\Entity\Adn
   */
  public function getAdnFk() {
    return $this->adnFk;
  }

  /**
   * Add pcrEstRealisePar
   *
   * @param \App\Entity\PcrEstRealisePar $pcrEstRealisePar
   *
   * @return Pcr
   */
  public function addPcrEstRealisePar(\App\Entity\PcrEstRealisePar $pcrEstRealisePar) {
    $pcrEstRealisePar->setPcrFk($this);
    $this->pcrEstRealisePars[] = $pcrEstRealisePar;

    return $this;
  }

  /**
   * Remove pcrEstRealisePar
   *
   * @param \App\Entity\PcrEstRealisePar $pcrEstRealisePar
   */
  public function removePcrEstRealisePar(\App\Entity\PcrEstRealisePar $pcrEstRealisePar) {
    $this->pcrEstRealisePars->removeElement($pcrEstRealisePar);
  }

  /**
   * Get pcrEstRealisePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPcrEstRealisePars() {
    return $this->pcrEstRealisePars;
  }

  
  /**
   * @return Collection<int, EspeceIdentifiee>
   */
  public function getEspeceIdentifiees(): Collection
  {
      return $this->especeIdentifiees;
  }

  public function addEspeceIdentifiee(EspeceIdentifiee $especeIdentifiee): self
  {
      if (!$this->especeIdentifiees->contains($especeIdentifiee)) {
          $this->especeIdentifiees[] = $especeIdentifiee;
          $especeIdentifiee->setPcrFk($this);
      }

      return $this;
  }

  public function removeEspeceIdentifiee(EspeceIdentifiee $especeIdentifiee): self
  {
      if ($this->especeIdentifiees->removeElement($especeIdentifiee)) {
          // set the owning side to null (unless already changed)
          if ($especeIdentifiee->getPcrFk() === $this) {
              $especeIdentifiee->setPcrFk(null);
          }
      }
      return $this;
  }

  
  
}
