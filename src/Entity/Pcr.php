<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
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
 * @UniqueEntity(fields={"code"}, message="The PCR code {{ value }} is already registered")
 * @ApiResource
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
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_code", type="string", length=255, nullable=false, unique=true)
   * @Assert\Expression("this.hasValidCode()",
   *  groups={"code"},
   *  message="Code {{ value }} differs from specification.")
   * @Groups({"item"})
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_number", type="string", length=255, nullable=false)
   * @Groups({"item"})
   */
  private $number;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="pcr_date", type="date", nullable=true)
   * @Groups({"item"})
   */
  private $date;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_details", type="text", nullable=true)
   * @Groups({"item"})
   */
  private $details;

  /**
   * @var string
   *
   * @ORM\Column(name="pcr_comments", type="text", nullable=true)
   * @Groups({"item"})
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $gene;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pcr_quality_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $quality;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pcr_specificity_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $specificity;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="forward_primer_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $primerStart;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="reverse_primer_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $primerEnd;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecision;

  /**
   * @var \Dna
   *
   * @ORM\ManyToOne(targetEntity="Dna", inversedBy="pcrs")
   * @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false)
   */
  private $dna;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="pcr_is_done_by",
   *  joinColumns={@ORM\JoinColumn(name="pcr_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $producers;

  public function __construct() {
    $this->producers = new ArrayCollection();
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
      $this->getDna()->getCode(),
      $this->getNumber(),
      $this->getPrimerStart()->getCode(),
      $this->getPrimerEnd()->getCode(),
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
   * Set gene
   *
   * @param Voc $gene
   *
   * @return Pcr
   */
  public function setGene(Voc $gene = null): Pcr {
    $this->gene = $gene;
    return $this;
  }

  /**
   * Get gene
   *
   * @return Voc
   */
  public function getGene(): ?Voc {
    return $this->gene;
  }

  /**
   * Set quality
   *
   * @param Voc $quality
   *
   * @return Pcr
   */
  public function setQuality(Voc $quality = null): Pcr {
    $this->quality = $quality;
    return $this;
  }

  /**
   * Get quality
   *
   * @return Voc
   */
  public function getQuality(): ?Voc {
    return $this->quality;
  }

  /**
   * Set specificity
   *
   * @param Voc $specificity
   *
   * @return Pcr
   */
  public function setSpecificity(Voc $specificity = null): Pcr {
    $this->specificity = $specificity;
    return $this;
  }

  /**
   * Get specificity
   *
   * @return Voc
   */
  public function getSpecificity(): ?Voc {
    return $this->specificity;
  }

  /**
   * Set primerStart
   *
   * @param Voc $primerStart
   *
   * @return Pcr
   */
  public function setPrimerStart(Voc $primerStart = null): Pcr {
    $this->primerStart = $primerStart;
    return $this;
  }

  /**
   * Get primerStart
   *
   * @return Voc
   */
  public function getPrimerStart(): ?Voc {
    return $this->primerStart;
  }

  /**
   * Set primerEnd
   *
   * @param Voc $primerEnd
   *
   * @return Pcr
   */
  public function setPrimerEnd(Voc $primerEnd = null): Pcr {
    $this->primerEnd = $primerEnd;
    return $this;
  }

  /**
   * Get primerEnd
   *
   * @return Voc
   */
  public function getPrimerEnd(): ?Voc {
    return $this->primerEnd;
  }

  /**
   * Set datePrecision
   *
   * @param Voc $datePrecision
   *
   * @return Pcr
   */
  public function setDatePrecision(Voc $datePrecision = null): Pcr {
    $this->datePrecision = $datePrecision;
    return $this;
  }

  /**
   * Get datePrecision
   *
   * @return Voc
   */
  public function getDatePrecision(): ?Voc {
    return $this->datePrecision;
  }

  /**
   * Set dna
   *
   * @param Dna $dna
   *
   * @return Pcr
   */
  public function setDna(Dna $dna = null): Pcr {
    $this->dna = $dna;
    return $this;
  }

  /**
   * Get dna
   *
   * @return Dna
   */
  public function getDna(): ?Dna {
    return $this->dna;
  }

  /**
   * Add pcrProducer
   *
   * @param Person $pcrProducer
   *
   * @return Pcr
   */
  public function addPcrProducer(Person $pcrProducer): Pcr {
    $this->producers[] = $pcrProducer;
    return $this;
  }

  /**
   * Remove pcrProducer
   *
   * @param Person $pcrProducer
   */
  public function removePcrProducer(Person $pcrProducer): Pcr {
    $this->producers->removeElement($pcrProducer);
    return $this;
  }

  /**
   * Get producers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProducers(): Collection {
    return $this->producers;
  }
}
