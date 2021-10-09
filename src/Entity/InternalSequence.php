<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A DNA sequence for which full traceability is available
 */
#[ORM\Entity]
#[ORM\Table(name: 'internal_sequence')]
#[ORM\UniqueConstraint(
  name: 'uk_internal_sequence__internal_sequence_code',
  columns: ['internal_sequence_code']
)]
#[ORM\UniqueConstraint(
  name: 'uk_internal_sequence__internal_sequence_alignment_code',
  columns: ['internal_sequence_alignment_code']
)]
#[ORM\Index(name: 'IDX_353CF669A30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_353CF66988085E0F', columns: ['internal_sequence_status_voc_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
#[UniqueEntity(fields: ['alignmentCode'], message: 'This code is already registered')]

class InternalSequence extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'internal_sequence_code', type: 'string', length: 1024, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'internal_sequence_creation_date', type: 'date', nullable: true)]
  private ?\DateTime $creationDate = null;

  #[ORM\Column(name: 'internal_sequence_accession_number', type: 'string', length: 255, nullable: true)]
  private ?string $accessionNumber = null;

  #[ORM\Column(name: 'internal_sequence_alignment_code', type: 'string', length: 1024, nullable: true)]
  private ?string $alignmentCode = null;

  #[ORM\Column(name: 'internal_sequence_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'internal_sequence_status_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $status;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'internal_sequence_is_assembled_by')]
  #[ORM\JoinColumn(name: 'internal_sequence_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $assemblers;

  #[ORM\ManyToMany(targetEntity: 'Source', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'internal_sequence_is_published_in')]
  #[ORM\JoinColumn(name: 'internal_sequence_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'source_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $publications;

  #[ORM\OneToMany(
    targetEntity: 'TaxonIdentification',
    mappedBy: 'internalSequence',
    cascade: ['persist']
  )]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $taxonIdentifications;

  #[ORM\ManyToMany(targetEntity: 'Chromatogram', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'chromatogram_is_processed_to')]
  #[ORM\JoinColumn(name: 'internal_sequence_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'chromatogram_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $chromatograms;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->chromatograms = new ArrayCollection();
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

  public function setCreationDate(?\DateTime $creationDate): self {
    $this->creationDate = $creationDate;

    return $this;
  }

  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  public function setAccessionNumber(?string $accessionNumber): self {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  public function getAccessionNumber(): ?string {
    return $this->accessionNumber;
  }

  public function setAlignmentCode(?string $alignmentCode): self {
    $this->alignmentCode = $alignmentCode;

    return $this;
  }

  public function getAlignmentCode(): ?string {
    return $this->alignmentCode;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
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

  public function addPublication(Source $publication): self {
    $this->publications[] = $publication;

    return $this;
  }

  public function removePublication(Source $publication): self {
    $this->publications->removeElement($publication);

    return $this;
  }

  public function getPublications(): Collection {
    return $this->publications;
  }

  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalSequence($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalSequence(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }

  public function addChromatogram(Chromatogram $chromatogram): self {
    $this->chromatograms[] = $chromatogram;

    return $this;
  }

  public function removeChromatogram(Chromatogram $chromatogram): self {
    $this->chromatograms->removeElement($chromatogram);

    return $this;
  }

  public function getChromatograms(): Collection {
    return $this->chromatograms;
  }

  /**
   * Get gene
   *
   * This assumes that a sequence matches ONE gene only, even if it was processed through multiple chromatograms
   */
  public function getGene(): ?Voc {
    $process = $this->chromatograms->first();

    return $process
    ? $process
      ->getPcr()
      ->getGene()
    : null;
  }

  /**
   * Get specimen
   *
   * this assumes that a sequence matches ONE specimen only, even if it was processed through multiple chromatograms
   */
  public function getSpecimen(): ?Specimen {
    $chromato = $this->chromatograms->first();

    return $chromato
    ? $chromato
      ->getPcr()
      ->getDna()
      ->getSpecimen()
    : null;
  }

  public function getChromatoCodes(): array {
    return $this->getChromatograms()
      ->map(fn ($chromato) => $chromato->getCodeSpecificity())
      ->toArray();
  }

  public function getStatusCode(): string {
    $statusCode = $this->getStatus()
      ->getCode();

    return 'VALID' != substr($statusCode, 0, 5) ? $statusCode : '';
  }

  /**
   * Generate alignment code
   *
   * generates an alignment code from sequence metadata generated code is saved as the sequence alignment code
   */
  public function generateAlignmentCode(): string {
    $nbChromato = count($this->getChromatograms());
    $nbIdentifiedSpecies = count($this->getTaxonIdentifications());
    if ($nbChromato < 1 || $nbIdentifiedSpecies < 1) {
      $seqCode = null;
    } else {
      $lastTaxonCode = $this->getTaxonIdentifications()
        ->last()
        ->getTaxon()
        ->getCode();

      $specimen = $this->getSpecimen();
      $specimenCode = $specimen->getMolecularNumber();
      $samplingCode = $specimen
        ->getInternalLot()
        ->getSampling()
        ->getCode();

      $chromatoCodeList = $this->getChromatoCodes();
      $chromatoCodeStr = join('-', $chromatoCodeList);

      $seqCode = join('_', [
        $this->getStatusCode(),
        $lastTaxonCode,
        $samplingCode,
        $specimenCode,
        $chromatoCodeStr,
      ]);
    }
    $this->setAlignmentCode($seqCode);

    return $seqCode;
  }

  private function _generateCode(): string {
    $chromatoCodes = $this->getChromatoCodes();
    $chromatoCodeStr = join('-', $chromatoCodes);

    return join(
      '_',
      [$this->getStatusCode(), $this->getSpecimen()->getMolecularCode(), $chromatoCodeStr]
    );
  }
}
