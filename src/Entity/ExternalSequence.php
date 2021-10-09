<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A DNA sequence with partial traceability
 */
#[ORM\Entity]
#[ORM\Table(name: 'external_sequence')]
#[ORM\Index(name: 'IDX_9E9F85CF9D3CDB05', columns: ['gene_voc_fk'])]
#[ORM\Index(name: 'IDX_9E9F85CFA30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_9E9F85CF514D78E0', columns: ['external_sequence_origin_voc_fk'])]
#[ORM\Index(name: 'IDX_9E9F85CF662D9B98', columns: ['sampling_fk'])]
#[ORM\Index(name: 'IDX_9E9F85CF88085E0F', columns: ['external_sequence_status_voc_fk'])]
#[ORM\UniqueConstraint(
  name: 'uk_external_sequence__external_sequence_code',
  columns: ['external_sequence_code']
)]
#[ORM\UniqueConstraint(
  name: 'uk_external_sequence__external_sequence_alignment_code',
  columns: ['external_sequence_alignment_code']
)]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
#[UniqueEntity(fields: ['alignmentCode'], message: 'This code is already registered')]
class ExternalSequence extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  private int $id;

  #[ORM\Column(name: 'external_sequence_code', type: 'string', length: 1024, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'external_sequence_creation_date', type: 'date', nullable: true)]
  private ?\DateTime $dateCreation = null;

  #[ORM\Column(name: 'external_sequence_accession_number', type: 'string', length: 255, nullable: false)]
  private string $accessionNumber;

  #[ORM\Column(name: 'external_sequence_alignment_code', type: 'string', length: 1024, nullable: true)]
  private ?string $alignmentCode = null;

  #[ORM\Column(name: 'external_sequence_specimen_number', type: 'string', length: 255, nullable: false)]
  private string $specimenMolecularNumber;

  #[ORM\Column(name: 'external_sequence_primary_taxon', type: 'string', length: 255, nullable: true)]
  private ?string $primaryTaxon = null;

  #[ORM\Column(name: 'external_sequence_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'gene_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $gene;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'external_sequence_origin_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $origin;

  #[ORM\ManyToOne(targetEntity: 'Sampling')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id', nullable: false)]
  private Sampling $sampling;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'external_sequence_status_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $status;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'external_sequence_is_entered_by')]
  #[ORM\JoinColumn(name: 'external_sequence_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  #[Assert\Count(min: 1, minMessage: 'At least one person is required as provider')]
  private Collection $assemblers;

  #[ORM\ManyToMany(targetEntity: 'Source', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'external_sequence_is_published_in')]
  #[ORM\JoinColumn(name: 'external_sequence_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'source_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $publications;

  #[ORM\OneToMany(
    targetEntity: 'TaxonIdentification',
    mappedBy: 'externalSequence',
    cascade: ['persist']
  )]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $taxonIdentifications;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
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

  public function setDateCreation(?\DateTime $date): self {
    $this->dateCreation = $date;

    return $this;
  }

  public function getDateCreation(): ?\DateTime {
    return $this->dateCreation;
  }

  public function setAccessionNumber(string $accessionNumber): self {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  public function getAccessionNumber(): string {
    return $this->accessionNumber;
  }

  public function setAlignmentCode(?string $alignmentCode): self {
    $this->alignmentCode = $alignmentCode;

    return $this;
  }

  public function getAlignmentCode(): ?string {
    return $this->alignmentCode;
  }

  public function setSpecimenMolecularNumber(string $specimenMolecularNumber): self {
    $this->specimenMolecularNumber = $specimenMolecularNumber;

    return $this;
  }

  public function getSpecimenMolecularNumber(): string {
    return $this->specimenMolecularNumber;
  }

  public function setPrimaryTaxon(?string $taxon): self {
    $this->primaryTaxon = $taxon;

    return $this;
  }

  public function getPrimaryTaxon(): ?string {
    return $this->primaryTaxon;
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

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  public function setOrigin(Voc $origin): self {
    $this->origin = $origin;

    return $this;
  }

  public function getOrigin(): Voc {
    return $this->origin;
  }

  public function setSampling(Sampling $sampling): self {
    $this->sampling = $sampling;

    return $this;
  }

  public function getSampling(): Sampling {
    return $this->sampling;
  }

  public function setStatus(Voc $status): self {
    $this->status = $status;

    return $this;
  }

  public function getStatus(): Voc {
    return $this->status;
  }

  public function addAssembler(Person $assembler): self {
    $this->assemblers[] = $assembler;

    return $this;
  }

  public function removeAssembler(Person $assembler): self {
    $this->assemblers->removeElement($assembler);

    return $this;
  }

  public function getAssemblers(): Collection {
    return $this->assemblers;
  }

  public function addPublication(Source $externalSequencePublication): self {
    $this->publications[] = $externalSequencePublication;

    return $this;
  }

  public function removePublication(Source $externalSequencePublication): self {
    $this->publications->removeElement($externalSequencePublication);

    return $this;
  }

  public function getPublications(): Collection {
    return $this->publications;
  }

  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalSequence($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalSequence(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
