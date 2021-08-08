<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
class Pcr extends AbstractTimestampedEntity {
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
   * @var \Dna
   *
   * @ORM\ManyToOne(targetEntity="Dna")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $adnFk;

  /**
   * @ORM\OneToMany(targetEntity="PcrProducer", mappedBy="pcrFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $pcrProducers;

  public function __construct() {
    $this->pcrProducers = new ArrayCollection();
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
   * @param \App\Entity\Dna $adnFk
   *
   * @return Pcr
   */
  public function setAdnFk(\App\Entity\Dna $adnFk = null) {
    $this->adnFk = $adnFk;

    return $this;
  }

  /**
   * Get adnFk
   *
   * @return \App\Entity\Dna
   */
  public function getAdnFk() {
    return $this->adnFk;
  }

  /**
   * Add pcrProducer
   *
   * @param \App\Entity\PcrProducer $pcrProducer
   *
   * @return Pcr
   */
  public function addPcrProducer(\App\Entity\PcrProducer $pcrProducer) {
    $pcrProducer->setPcrFk($this);
    $this->pcrProducers[] = $pcrProducer;

    return $this;
  }

  /**
   * Remove pcrProducer
   *
   * @param \App\Entity\PcrProducer $pcrProducer
   */
  public function removePcrProducer(\App\Entity\PcrProducer $pcrProducer) {
    $this->pcrProducers->removeElement($pcrProducer);
  }

  /**
   * Get pcrProducers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPcrProducers() {
    return $this->pcrProducers;
  }
}
