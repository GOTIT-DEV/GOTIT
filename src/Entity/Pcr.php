<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
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
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
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
   * @Groups({"field"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_code", type="string", length=255, nullable=false)
   * @Groups({"field"})
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_number", type="string", length=255, nullable=false)
   * @Groups({"field"})
   */
  private $number;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="pcr_date", type="date", nullable=true)
   * @Groups({"field"})
   */
  private $date;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_details", type="text", nullable=true)
   * @Groups({"field"})
   */
  private $details;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_comments", type="text", nullable=true)
   * @Groups({"field"})
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $geneVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pcr_quality_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $qualityVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pcr_specificity_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $specificityVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="forward_primer_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $primerStartVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="reverse_primer_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $primerEndVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecisionVocFk;

  /**
   * @var \Dna
   *
   * @ORM\ManyToOne(targetEntity="Dna", inversedBy="pcrs")
   * @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false)
   */
  private $dnaFk;

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
   * Set code
   *
   * @param string $code
   *
   * @return Pcr
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
   * Set number
   *
   * @param string $number
   *
   * @return Pcr
   */
  public function setNumber($number) {
    $this->number = $number;

    return $this;
  }

  /**
   * Get number
   *
   * @return string
   */
  public function getNumber() {
    return $this->number;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   *
   * @return Pcr
   */
  public function setDate($date) {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * Set details
   *
   * @param string $details
   *
   * @return Pcr
   */
  public function setDetails($details) {
    $this->details = $details;

    return $this;
  }

  /**
   * Get details
   *
   * @return string
   */
  public function getDetails() {
    return $this->details;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Pcr
   */
  public function setComment($comment) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment() {
    return $this->comment;
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
   * Set qualityVocFk
   *
   * @param \App\Entity\Voc $qualityVocFk
   *
   * @return Pcr
   */
  public function setQualityVocFk(\App\Entity\Voc $qualityVocFk = null) {
    $this->qualityVocFk = $qualityVocFk;

    return $this;
  }

  /**
   * Get qualityVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getQualityVocFk() {
    return $this->qualityVocFk;
  }

  /**
   * Set specificityVocFk
   *
   * @param \App\Entity\Voc $specificityVocFk
   *
   * @return Pcr
   */
  public function setSpecificityVocFk(\App\Entity\Voc $specificityVocFk = null) {
    $this->specificityVocFk = $specificityVocFk;

    return $this;
  }

  /**
   * Get specificityVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getSpecificityVocFk() {
    return $this->specificityVocFk;
  }

  /**
   * Set primerStartVocFk
   *
   * @param \App\Entity\Voc $primerStartVocFk
   *
   * @return Pcr
   */
  public function setPrimerStartVocFk(\App\Entity\Voc $primerStartVocFk = null) {
    $this->primerStartVocFk = $primerStartVocFk;

    return $this;
  }

  /**
   * Get primerStartVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPrimerStartVocFk() {
    return $this->primerStartVocFk;
  }

  /**
   * Set primerEndVocFk
   *
   * @param \App\Entity\Voc $primerEndVocFk
   *
   * @return Pcr
   */
  public function setPrimerEndVocFk(\App\Entity\Voc $primerEndVocFk = null) {
    $this->primerEndVocFk = $primerEndVocFk;

    return $this;
  }

  /**
   * Get primerEndVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPrimerEndVocFk() {
    return $this->primerEndVocFk;
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
   * Set dnaFk
   *
   * @param \App\Entity\Dna $dnaFk
   *
   * @return Pcr
   */
  public function setDnaFk(\App\Entity\Dna $dnaFk = null) {
    $this->dnaFk = $dnaFk;

    return $this;
  }

  /**
   * Get dnaFk
   *
   * @return \App\Entity\Dna
   */
  public function getDnaFk() {
    return $this->dnaFk;
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
