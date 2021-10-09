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
 * ExternalSequence
 *
 * @ORM\Table(name="external_sequence",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_external_sequence__external_sequence_code", columns={"external_sequence_code"}),
 *      @ORM\UniqueConstraint(name="uk_external_sequence__external_sequence_alignment_code", columns={"external_sequence_alignment_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_9E9F85CF9D3CDB05", columns={"gene_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CFA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF514D78E0", columns={"external_sequence_origin_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF88085E0F", columns={"external_sequence_status_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @UniqueEntity(fields={"alignmentCode"}, message="This code is already registered")
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalSequence extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="external_sequence_code", type="string", length=1024, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="external_sequence_creation_date", type="date", nullable=true)
   */
  private ?\DateTime $dateCreation;

  /**
   * @ORM\Column(name="external_sequence_accession_number", type="string", length=255, nullable=false)
   */
  private string $accessionNumber;

  /**
   * @ORM\Column(name="external_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private ?string $alignmentCode;

  /**
   * @ORM\Column(name="external_sequence_specimen_number", type="string", length=255, nullable=false)
   */
  private string $specimenMolecularNumber;

  /**
   * @ORM\Column(name="external_sequence_primary_taxon", type="string", length=255, nullable=true)
   */
  private ?string $primaryTaxon;

  /**
   * @ORM\Column(name="external_sequence_comments", type="text", nullable=true)
   */
  private ?string $comment;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $gene;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $datePrecision;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="external_sequence_origin_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $origin;

  /**
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private Sampling $sampling;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="external_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $status;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="external_sequence_is_entered_by",
   *  joinColumns={@ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @Assert\Count(min=1, minMessage="At least one person is required as provider")
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $assemblers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   *  @ORM\JoinTable(name="external_sequence_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalSequence", cascade={"persist"})
   * @ORM\OrderBy({"id": "ASC"})
   */
  private Collection $taxonIdentifications;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
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

  /**
   * Set dateCreation
   */
  public function setDateCreation(?\DateTime $date): self {
    $this->dateCreation = $date;

    return $this;
  }

  /**
   * Get dateCreation
   */
  public function getDateCreation(): ?\DateTime {
    return $this->dateCreation;
  }

  /**
   * Set accessionNumber
   */
  public function setAccessionNumber(string $accessionNumber): self {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  /**
   * Get accessionNumber
   */
  public function getAccessionNumber(): string {
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
   */
  public function getAlignmentCode(): ?string {
    return $this->alignmentCode;
  }

  /**
   * Set specimenMolecularNumber
   */
  public function setSpecimenMolecularNumber(string $specimenMolecularNumber): self {
    $this->specimenMolecularNumber = $specimenMolecularNumber;

    return $this;
  }

  /**
   * Get specimenMolecularNumber
   */
  public function getSpecimenMolecularNumber(): string {
    return $this->specimenMolecularNumber;
  }

  /**
   * Set primaryTaxon
   */
  public function setPrimaryTaxon(?string $taxon): self {
    $this->primaryTaxon = $taxon;

    return $this;
  }

  /**
   * Get primaryTaxon
   */
  public function getPrimaryTaxon(): ?string {
    return $this->primaryTaxon;
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
   * Set gene
   */
  public function setGene(Voc $gene): self {
    $this->gene = $gene;

    return $this;
  }

  /**
   * Get gene
   */
  public function getGene(): Voc {
    return $this->gene;
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
   * Set origin
   */
  public function setOrigin(Voc $origin): self {
    $this->origin = $origin;

    return $this;
  }

  /**
   * Get origin
   */
  public function getOrigin(): Voc {
    return $this->origin;
  }

  /**
   * Set sampling
   */
  public function setSampling(Sampling $sampling): self {
    $this->sampling = $sampling;

    return $this;
  }

  /**
   * Get sampling
   */
  public function getSampling(): Sampling {
    return $this->sampling;
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
   * Add externalSequencePublication
   */
  public function addPublication(Source $externalSequencePublication): self {
    $this->publications[] = $externalSequencePublication;

    return $this;
  }

  /**
   * Remove externalSequencePublication
   */
  public function removePublication(Source $externalSequencePublication): self {
    $this->publications->removeElement($externalSequencePublication);

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
   *
   * @return ExternalSequence
   */
  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalSequence($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  /**
   * Remove taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalSequence(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  /**
   * Get taxonIdentifications
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
