<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * InternalSequence
 *
 * @ORM\Table(name="internal_sequence",
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_internal_sequence__internal_sequence_code", columns={"internal_sequence_code"}),
 *      @ORM\UniqueConstraint(name="uk_internal_sequence__internal_sequence_alignment_code", columns={"internal_sequence_alignment_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_353CF669A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_353CF66988085E0F", columns={"internal_sequence_status_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @UniqueEntity(fields={"alignmentCode"}, message="This code is already registered")
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequence extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="internal_sequence_code", type="string", length=1024, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="internal_sequence_creation_date", type="date", nullable=true)
   */
  private ?\DateTime $creationDate;

  /**
   * @ORM\Column(name="internal_sequence_accession_number", type="string", length=255, nullable=true)
   */
  private ?string $accessionNumber;

  /**
   * @ORM\Column(name="internal_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private ?string $alignmentCode;

  /**
   * @ORM\Column(name="internal_sequence_comments", type="text", nullable=true)
   */
  private ?string $comment;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $datePrecision;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="internal_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $status;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="internal_sequence_is_assembled_by",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $assemblers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   * @ORM\JoinTable(name="internal_sequence_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalSequence", cascade={"persist"})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $taxonIdentifications;

  /**
   * @ORM\ManyToMany(targetEntity="Chromatogram", cascade={"persist"})
   * @ORM\JoinTable(name="chromatogram_is_processed_to",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="chromatogram_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $chromatograms;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->chromatograms = new ArrayCollection();
  }

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set code
   */
  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   */
  public function getCode(): string {
    return $this->code;
  }

  private function _generateCode(): string {
    $chromatoCodes = $this->getChromatoCodes();
    $chromatoCodeStr = join('-', $chromatoCodes);

    return join('_', [
      $this->getStatusCode(),
      $this->getSpecimen()->getMolecularCode(),
      $chromatoCodeStr,
    ]);
  }

  /**
   * Set creationDate
   */
  public function setCreationDate(?\DateTime $creationDate): self {
    $this->creationDate = $creationDate;

    return $this;
  }

  /**
   * Get creationDate
   */
  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  /**
   * Set accessionNumber
   */
  public function setAccessionNumber(?string $accessionNumber): self {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  /**
   * Get accessionNumber
   */
  public function getAccessionNumber(): ?string {
    return $this->accessionNumber;
  }

  /**
   * Set alignmentCode
   */
  public function setAlignmentCode(?string $alignmentCode): self {
    $this->alignmentCode = $alignmentCode;

    return $this;
  }

  /**
   * Get alignmentCode
   *
   * @return string
   */
  public function getAlignmentCode(): ?string {
    return $this->alignmentCode;
  }

  /**
   * Set comment
   */
  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set datePrecision
   */
  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  /**
   * Get datePrecision
   */
  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  /**
   * Set status
   */
  public function setStatus(Voc $status): self {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   */
  public function getStatus(): Voc {
    return $this->status;
  }

  /**
   * Add assembler
   */
  public function addAssembler(Person $assembler): self {
    $this->assemblers[] = $assembler;

    return $this;
  }

  /**
   * Remove assembler
   */
  public function removeAssembler(Person $assembler): self {
    $this->assemblers->removeElement($assembler);

    return $this;
  }

  /**
   * Get assemblers
   */
  public function getAssemblers(): Collection {
    return $this->assemblers;
  }

  /**
   * Add publication
   */
  public function addPublication(Source $publication): self {
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   */
  public function removePublication(Source $publication): self {
    $this->publications->removeElement($publication);

    return $this;
  }

  /**
   * Get publications
   */
  public function getPublications(): Collection {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   */
  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalSequence($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  /**
   * Remove taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalSequence(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  /**
   * Get taxonIdentifications
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }

  /**
   * Add chromatogram
   */
  public function addChromatogram(Chromatogram $chromatogram): self {
    $this->chromatograms[] = $chromatogram;

    return $this;
  }

  /**
   * Remove chromatogram
   */
  public function removeChromatogram(Chromatogram $chromatogram): self {
    $this->chromatograms->removeElement($chromatogram);

    return $this;
  }

  /**
   * Get chromatogram
   */
  public function getChromatograms(): Collection {
    return $this->chromatograms;
  }

  /**
   * Get gene
   *
   * This assumes that a sequence matches ONE gene only,
   * even if it was processed through multiple chromatograms
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
   * this assumes that a sequence matches ONE specimen only,
   * even if it was processed through multiple chromatograms
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
      ->map(function ($chromato) {
        return $chromato->getCodeSpecificity();
      })
      ->toArray();
  }

  public function getStatusCode(): string {
    $statusCode = $this->getStatus()->getCode();

    return 'VALID' != substr($statusCode, 0, 5) ? $statusCode : '';
  }

  /**
   * Generate alignment code
   *
   * generates an alignment code from sequence metadata
   * generated code is saved as the sequence alignment code
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
}
