<?php

namespace App\Entity;

use App\Entity\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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

  use CompositeCodeEntityTrait;

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
   * @Assert\Expression("this.hasValidCode()",
   *  groups={"code"},
   *  message="Code {{ value }} differs from specification.")
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
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * Set code
   *
   * @param string $code
   *
   * @return Pcr
   */
  public function setCode($code): Pcr {
    $this->code = $code;
    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode(): ?string {
    return $this->code;
  }

  /**
   * Generate composite code from PCR properties
   *
   * @return string
   */
  private function _generateCode(): string {
    return join('_', [
      $this->getDnaFk()->getCode(),
      $this->getNumber(),
      $this->getPrimerStartVocFk()->getCode(),
      $this->getPrimerEndVocFk()->getCode(),
    ]);
  }

  /**
   * Set number
   *
   * @param string $number
   *
   * @return Pcr
   */
  public function setNumber($number): Pcr {
    $this->number = $number;
    return $this;
  }

  /**
   * Get number
   *
   * @return string
   */
  public function getNumber(): ?string {
    return $this->number;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   *
   * @return Pcr
   */
  public function setDate($date): Pcr {
    $this->date = $date;
    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate(): ?\Datetime {
    return $this->date;
  }

  /**
   * Set details
   *
   * @param string $details
   *
   * @return Pcr
   */
  public function setDetails($details): Pcr {
    $this->details = $details;
    return $this;
  }

  /**
   * Get details
   *
   * @return string
   */
  public function getDetails(): ?string {
    return $this->details;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Pcr
   */
  public function setComment($comment): Pcr {
    $this->comment = $comment;
    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set geneVocFk
   *
   * @param Voc $geneVocFk
   *
   * @return Pcr
   */
  public function setGeneVocFk(Voc $geneVocFk = null): Pcr {
    $this->geneVocFk = $geneVocFk;
    return $this;
  }

  /**
   * Get geneVocFk
   *
   * @return Voc
   */
  public function getGeneVocFk(): ?Voc {
    return $this->geneVocFk;
  }

  /**
   * Set qualityVocFk
   *
   * @param Voc $qualityVocFk
   *
   * @return Pcr
   */
  public function setQualityVocFk(Voc $qualityVocFk = null): Pcr {
    $this->qualityVocFk = $qualityVocFk;
    return $this;
  }

  /**
   * Get qualityVocFk
   *
   * @return Voc
   */
  public function getQualityVocFk(): ?Voc {
    return $this->qualityVocFk;
  }

  /**
   * Set specificityVocFk
   *
   * @param Voc $specificityVocFk
   *
   * @return Pcr
   */
  public function setSpecificityVocFk(Voc $specificityVocFk = null): Pcr {
    $this->specificityVocFk = $specificityVocFk;
    return $this;
  }

  /**
   * Get specificityVocFk
   *
   * @return Voc
   */
  public function getSpecificityVocFk(): ?Voc {
    return $this->specificityVocFk;
  }

  /**
   * Set primerStartVocFk
   *
   * @param Voc $primerStartVocFk
   *
   * @return Pcr
   */
  public function setPrimerStartVocFk(Voc $primerStartVocFk = null): Pcr {
    $this->primerStartVocFk = $primerStartVocFk;
    return $this;
  }

  /**
   * Get primerStartVocFk
   *
   * @return Voc
   */
  public function getPrimerStartVocFk(): ?Voc {
    return $this->primerStartVocFk;
  }

  /**
   * Set primerEndVocFk
   *
   * @param Voc $primerEndVocFk
   *
   * @return Pcr
   */
  public function setPrimerEndVocFk(Voc $primerEndVocFk = null): Pcr {
    $this->primerEndVocFk = $primerEndVocFk;
    return $this;
  }

  /**
   * Get primerEndVocFk
   *
   * @return Voc
   */
  public function getPrimerEndVocFk(): ?Voc {
    return $this->primerEndVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param Voc $datePrecisionVocFk
   *
   * @return Pcr
   */
  public function setDatePrecisionVocFk(Voc $datePrecisionVocFk = null): Pcr {
    $this->datePrecisionVocFk = $datePrecisionVocFk;
    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return Voc
   */
  public function getDatePrecisionVocFk(): ?Voc {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set dnaFk
   *
   * @param Dna $dnaFk
   *
   * @return Pcr
   */
  public function setDnaFk(Dna $dnaFk = null): Pcr {
    $this->dnaFk = $dnaFk;
    return $this;
  }

  /**
   * Get dnaFk
   *
   * @return Dna
   */
  public function getDnaFk(): ?Dna {
    return $this->dnaFk;
  }

  /**
   * Add pcrProducer
   *
   * @param PcrProducer $pcrProducer
   *
   * @return Pcr
   */
  public function addPcrProducer(PcrProducer $pcrProducer): Pcr {
    $pcrProducer->setPcrFk($this);
    $this->pcrProducers[] = $pcrProducer;
    return $this;
  }

  /**
   * Remove pcrProducer
   *
   * @param PcrProducer $pcrProducer
   */
  public function removePcrProducer(PcrProducer $pcrProducer): Pcr {
    $this->pcrProducers->removeElement($pcrProducer);
    return $this;
  }

  /**
   * Get pcrProducers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPcrProducers(): Collection {
    return $this->pcrProducers;
  }
}
