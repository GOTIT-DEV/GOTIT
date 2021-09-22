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
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequence extends AbstractTimestampedEntity {

  use CompositeCodeEntityTrait;

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_code", type="string", length=1024, nullable=false, unique=true)
   */
  private $code;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="internal_sequence_creation_date", type="date", nullable=true)
   */
  private $creationDate;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_accession_number", type="string", length=255, nullable=true)
   */
  private $accessionNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private $alignmentCode;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecision;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="internal_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $status;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="internal_sequence_is_assembled_by",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $assemblers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   * @ORM\JoinTable(name="internal_sequence_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalSequence", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\ManyToMany(targetEntity="Chromatogram", cascade={"persist"})
   * @ORM\JoinTable(name="chromatogram_is_processed_to",
   *  joinColumns={@ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="chromatogram_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $assemblies;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->assemblies = new ArrayCollection();
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
   * @return InternalSequence
   */
  public function setCode($code): InternalSequence {
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
   *
   * @param \DateTime $creationDate
   *
   * @return InternalSequence
   */
  public function setCreationDate($creationDate): InternalSequence {
    $this->creationDate = $creationDate;
    return $this;
  }

  /**
   * Get creationDate
   *
   * @return \DateTime
   */
  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  /**
   * Set accessionNumber
   *
   * @param string $accessionNumber
   *
   * @return InternalSequence
   */
  public function setAccessionNumber($accessionNumber): InternalSequence {
    $this->accessionNumber = $accessionNumber;
    return $this;
  }

  /**
   * Get accessionNumber
   *
   * @return string
   */
  public function getAccessionNumber(): ?string {
    return $this->accessionNumber;
  }

  /**
   * Set alignmentCode
   *
   * @param string $alignmentCode
   *
   * @return InternalSequence
   */
  public function setAlignmentCode($alignmentCode): InternalSequence {
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
   *
   * @param string $comment
   *
   * @return InternalSequence
   */
  public function setComment($comment): InternalSequence {
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
   * Set datePrecision
   *
   * @param Voc $datePrecision
   *
   * @return InternalSequence
   */
  public function setDatePrecision(Voc $datePrecision = null): InternalSequence {
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
   * Set status
   *
   * @param Voc $status
   *
   * @return InternalSequence
   */
  public function setStatus(Voc $status = null): InternalSequence {
    $this->status = $status;
    return $this;
  }

  /**
   * Get status
   *
   * @return Voc
   */
  public function getStatus(): ?Voc {
    return $this->status;
  }

  /**
   * Add assembler
   *
   * @param Person $assembler
   *
   * @return InternalSequence
   */
  public function addAssembler(Person $assembler): InternalSequence {
    $this->assemblers[] = $assembler;
    return $this;
  }

  /**
   * Remove assembler
   *
   * @param Person $assembler
   */
  public function removeAssembler(Person $assembler): InternalSequence {
    $this->assemblers->removeElement($assembler);
    return $this;
  }

  /**
   * Get assemblers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAssemblers(): Collection {
    return $this->assemblers;
  }

  /**
   * Add publication
   *
   * @param Source $publication
   *
   * @return InternalSequence
   */
  public function addPublication(Source $publication): InternalSequence {
    $this->publications[] = $publication;
    return $this;
  }

  /**
   * Remove publication
   *
   * @param Source $publication
   */
  public function removePublication(Source $publication): InternalSequence {
    $this->publications->removeElement($publication);
    return $this;
  }

  /**
   * Get publications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPublications(): Collection {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   *
   * @param TaxonIdentification $taxonId
   *
   * @return InternalSequence
   */
  public function addTaxonIdentification(TaxonIdentification $taxonId): InternalSequence {
    $taxonId->setInternalSequence($this);
    $this->taxonIdentifications[] = $taxonId;
    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param TaxonIdentification $taxonId
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): InternalSequence {
    $this->taxonIdentifications->removeElement($taxonId);
    return $this;
  }

  /**
   * Get taxonIdentifications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }

  /**
   * Add assembly
   *
   * @param Chromatogram $assembly
   *
   * @return InternalSequence
   */
  public function addAssembly(Chromatogram $assembly): InternalSequence {
    $this->assemblies[] = $assembly;
    return $this;
  }

  /**
   * Remove assembly
   *
   * @param Chromatogram $assembly
   */
  public function removeAssembly(Chromatogram $assembly): InternalSequence {
    $this->assemblies->removeElement($assembly);
    return $this;
  }

  /**
   * Get assembly
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAssemblies(): Collection {
    return $this->assemblies;
  }

  /**
   * Get gene
   *
   * This assumes that a sequence matches ONE gene only,
   * even if it was processed through multiple chromatograms
   *
   * @return Voc
   */
  public function getGene(): ?Voc{
    $process = $this->assemblies->first();
    return $process
    ? $process
      ->getChromatogramFk()
      ->getPcr()
      ->getGene()
    : null;
  }

  /**
   * Get specimen
   *
   * this assumes that a sequence matches ONE specimen only,
   * even if it was processed through multiple chromatograms
   *
   * @return Specimen
   */
  public function getSpecimen(): ?Specimen{
    $chromato = $this->assemblies->first();
    return $chromato
    ? $chromato
      ->getPcr()
      ->getDna()
      ->getSpecimen()
    : null;
  }

  public function getChromatoCodes(): array{
    $this->getAssemblies()
      ->map(
        function ($chromato) {
          return $chromato->getCodeSpecificity();
        }
      )
      ->toArray();
  }

  public function getStatusCode(): string {
    $statusCode = $this->getStatus()->getCode();
    return substr($statusCode, 0, 5) != 'VALID' ? $statusCode : "";
  }

  /**
   * Generate alignment code
   *
   * generates an alignment code from sequence metadata
   * generated code is saved as the sequence alignment code
   *
   * @return string
   */
  public function generateAlignmentCode() {
    $nbChromato = count($this->getAssemblies());
    $nbIdentifiedSpecies = count($this->getTaxonIdentifications());
    if ($nbChromato < 1 || $nbIdentifiedSpecies < 1) {
      $seqCode = null;
    } else {
      $lastTaxonCode = $this->getTaxonIdentifications()
        ->last()->getTaxon()->getCode();

      $specimen = $this->getSpecimen();
      $specimenCode = $specimen->getMolecularNumber();
      $samplingCode = $specimen->getInternalLot()->getSampling()->getCode();

      $chromatoCodeList = $this->getChromatoCodes();
      $chromatoCodeStr = join("-", $chromatoCodeList);

      $seqCode = join("_", [
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
