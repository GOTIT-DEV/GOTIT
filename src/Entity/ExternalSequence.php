<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
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
   * @var int
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_code", type="string", length=1024, nullable=false, unique=true)
   */
  private $code;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="external_sequence_creation_date", type="date", nullable=true)
   */
  private $dateCreation;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_accession_number", type="string", length=255, nullable=false)
   */
  private $accessionNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private $alignmentCode;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_specimen_number", type="string", length=255, nullable=false)
   */
  private $specimenMolecularNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_primary_taxon", type="string", length=255, nullable=true)
   */
  private $primaryTaxon;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $gene;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecision;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="external_sequence_origin_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $origin;

  /**
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private $sampling;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="external_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $status;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="external_sequence_is_entered_by",
   *  joinColumns={@ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @Assert\Count(min=1, minMessage="At least one person is required as provider")
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected $assemblers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   *  @ORM\JoinTable(name="external_sequence_is_entered_by",
   *  joinColumns={@ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalSequence", cascade={"persist"})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
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
   */
  public function setCode($code): ExternalSequence {
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
   * Set dateCreation
   *
   * @param \DateTime $dateCreation
   * @param mixed     $date
   *
   * @return ExternalSequence
   */
  public function setDateCreation($date) {
    $this->dateCreation = $date;

    return $this;
  }

  /**
   * Get dateCreation
   *
   * @return \DateTime
   */
  public function getDateCreation() {
    return $this->dateCreation;
  }

  /**
   * Set accessionNumber
   *
   * @param string $accessionNumber
   *
   * @return ExternalSequence
   */
  public function setAccessionNumber($accessionNumber) {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  /**
   * Get accessionNumber
   *
   * @return string
   */
  public function getAccessionNumber() {
    return $this->accessionNumber;
  }

  /**
   * Set alignmentCode
   *
   * @param string $alignmentCode
   *
   * @return ExternalSequence
   */
  public function setAlignmentCode($alignmentCode) {
    $this->alignmentCode = $alignmentCode;

    return $this;
  }

  /**
   * Get alignmentCode
   *
   * @return string
   */
  public function getAlignmentCode() {
    return $this->alignmentCode;
  }

  /**
   * Set specimenMolecularNumber
   *
   * @param string $specimenMolecularNumber
   *
   * @return ExternalSequence
   */
  public function setSpecimenMolecularNumber($specimenMolecularNumber) {
    $this->specimenMolecularNumber = $specimenMolecularNumber;

    return $this;
  }

  /**
   * Get specimenMolecularNumber
   *
   * @return string
   */
  public function getSpecimenMolecularNumber() {
    return $this->specimenMolecularNumber;
  }

  /**
   * Set primaryTaxon
   *
   * @param string $primaryTaxon
   * @param mixed  $taxon
   *
   * @return ExternalSequence
   */
  public function setPrimaryTaxon($taxon) {
    $this->primaryTaxon = $taxon;

    return $this;
  }

  /**
   * Get primaryTaxon
   *
   * @return string
   */
  public function getPrimaryTaxon() {
    return $this->primaryTaxon;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return ExternalSequence
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
   * Set gene
   *
   * @param Voc $gene
   *
   * @return ExternalSequence
   */
  public function setGene(Voc $gene = null) {
    $this->gene = $gene;

    return $this;
  }

  /**
   * Get gene
   *
   * @return Voc
   */
  public function getGene() {
    return $this->gene;
  }

  /**
   * Set datePrecision
   *
   * @param Voc $datePrecision
   *
   * @return ExternalSequence
   */
  public function setDatePrecision(Voc $datePrecision = null) {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  /**
   * Get datePrecision
   *
   * @return Voc
   */
  public function getDatePrecision() {
    return $this->datePrecision;
  }

  /**
   * Set origin
   *
   * @param Voc $origin
   *
   * @return ExternalSequence
   */
  public function setOrigin(Voc $origin = null) {
    $this->origin = $origin;

    return $this;
  }

  /**
   * Get origin
   *
   * @return Voc
   */
  public function getOrigin() {
    return $this->origin;
  }

  /**
   * Set sampling
   *
   * @param Sampling $sampling
   *
   * @return ExternalSequence
   */
  public function setSampling(Sampling $sampling = null) {
    $this->sampling = $sampling;

    return $this;
  }

  /**
   * Get sampling
   *
   * @return Sampling
   */
  public function getSampling() {
    return $this->sampling;
  }

  /**
   * Set status
   *
   * @param Voc $status
   *
   * @return ExternalSequence
   */
  public function setStatus(Voc $status = null) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return Voc
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Add assembler
   *
   * @return ExternalSequence
   */
  public function addAssembler(Person $assembler) {
    $this->assemblers[] = $assembler;

    return $this;
  }

  /**
   * Remove assembler
   */
  public function removeAssembler(Person $assembler) {
    $this->assemblers->removeElement($assembler);
  }

  /**
   * Get assemblers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAssemblers() {
    return $this->assemblers;
  }

  /**
   * Add externalSequencePublication
   *
   * @return ExternalSequence
   */
  public function addPublication(Source $externalSequencePublication) {
    $externalSequencePublication->setExternalSequence($this);
    $this->publications[] = $externalSequencePublication;

    return $this;
  }

  /**
   * Remove externalSequencePublication
   */
  public function removePublication(Source $externalSequencePublication) {
    $this->publications->removeElement($externalSequencePublication);
  }

  /**
   * Get publications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPublications() {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   *
   * @return ExternalSequence
   */
  public function addTaxonIdentification(
        TaxonIdentification $taxonIdentification,
    ) {
    $taxonIdentification->setExternalSequence($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  /**
   * Remove taxonIdentification
   */
  public function removeTaxonIdentification(
        TaxonIdentification $taxonIdentification,
    ) {
    $this->taxonIdentifications->removeElement($taxonIdentification);
  }

  /**
   * Get taxonIdentifications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonIdentifications() {
    return $this->taxonIdentifications;
  }
}
