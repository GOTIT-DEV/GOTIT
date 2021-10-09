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
 * A PCR amplification of a DNA sample
 */
#[ORM\Entity]
#[ORM\Table(name: 'pcr')]
#[ORM\UniqueConstraint(name: 'uk_pcr__pcr_code', columns: ['pcr_code'])]
#[ORM\Index(name: 'IDX_5B6B99369D3CDB05', columns: ['gene_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B99368B4A1710', columns: ['pcr_quality_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B99366CCC2566', columns: ['pcr_specificity_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B99362C5B04A7', columns: ['forward_primer_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B9936F1694267', columns: ['reverse_primer_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B9936A30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_5B6B99364B06319D', columns: ['dna_fk'])]
#[UniqueEntity(fields: ['code'], message: 'The PCR code {{ value }} is already registered')]
#[ApiResource]
class Pcr extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: ['item', 'dna:list'])]
  private int $id;

  #[ORM\Column(name: 'pcr_code', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(groups: ['item', 'dna:list'])]
  #[Assert\Expression(
    'this.hasValidCode()',
    groups: ['code'],
    message: 'Code {{ value }} differs from specification.'
  )]
  private string $code;

  #[ORM\Column(name: 'pcr_number', type: 'string', length: 255, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $number;

  #[ORM\Column(name: 'pcr_date', type: 'date', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?\DateTime $date = null;

  #[ORM\Column(name: 'pcr_details', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $details = null;

  #[ORM\Column(name: 'pcr_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'gene_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $gene;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'pcr_quality_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $quality;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'pcr_specificity_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $specificity;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'forward_primer_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $primerStart;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'reverse_primer_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $primerEnd;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Dna', inversedBy: 'pcrs')]
  #[ORM\JoinColumn(name: 'dna_fk', referencedColumnName: 'id', nullable: false)]
  private Dna $dna;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'pcr_is_done_by')]
  #[ORM\JoinColumn(name: 'pcr_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $producers;

  public function __construct() {
    $this->producers = new ArrayCollection();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  public function getCode(): string {
    return $this->code;
  }

  public function setNumber(string $number): self {
    $this->number = $number;

    return $this;
  }

  public function getNumber(): string {
    return $this->number;
  }

  public function setDate(?\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDate(): ?\DateTime {
    return $this->date;
  }

  public function setDetails(?string $details): self {
    $this->details = $details;

    return $this;
  }

  public function getDetails(): ?string {
    return $this->details;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setGene(Voc $gene): self {
    $this->gene = $gene;

    return $this;
  }

  public function getGene(): Voc {
    return $this->gene;
  }

  public function setQuality(Voc $quality): self {
    $this->quality = $quality;

    return $this;
  }

  public function getQuality(): Voc {
    return $this->quality;
  }

  public function setSpecificity(Voc $specificity): self {
    $this->specificity = $specificity;

    return $this;
  }

  public function getSpecificity(): Voc {
    return $this->specificity;
  }

  public function setPrimerStart(Voc $primerStart): self {
    $this->primerStart = $primerStart;

    return $this;
  }

  public function getPrimerStart(): Voc {
    return $this->primerStart;
  }

  public function setPrimerEnd(Voc $primerEnd): self {
    $this->primerEnd = $primerEnd;

    return $this;
  }

  public function getPrimerEnd(): Voc {
    return $this->primerEnd;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  public function setDna(Dna $dna): self {
    $this->dna = $dna;

    return $this;
  }

  public function getDna(): Dna {
    return $this->dna;
  }

  public function addPcrProducer(Person $pcrProducer): self {
    $this->producers[] = $pcrProducer;

    return $this;
  }

  public function removePcrProducer(Person $pcrProducer): self {
    $this->producers->removeElement($pcrProducer);

    return $this;
  }

  public function getProducers(): Collection {
    return $this->producers;
  }

  /**
   * Generate composite code from PCR properties
   */
  private function _generateCode(): string {
    return join('_', [
      $this->getDna()->getCode(),
      $this->getNumber(),
      $this->getPrimerStart()->getCode(),
      $this->getPrimerEnd()->getCode(),
    ]);
  }
}
