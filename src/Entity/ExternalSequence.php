<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalSequence extends AbstractTimestampedEntity {
  /**
   * @var integer
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
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $geneVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_origin_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $originVocFk;

  /**
   * @var \Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $samplingFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $status;

  /**
   * @ORM\OneToMany(targetEntity="ExternalSequenceAssembler", mappedBy="externalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $assemblers;

  /**
   * @ORM\OneToMany(targetEntity="ExternalSequencePublication", mappedBy="externalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $externalSequencePublications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->assemblers = new ArrayCollection();
    $this->externalSequencePublications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
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
   * @return ExternalSequence
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
   * Set dateCreation
   *
   * @param \DateTime $dateCreation
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
   * Set geneVocFk
   *
   * @param Voc $geneVocFk
   *
   * @return ExternalSequence
   */
  public function setGeneVocFk(Voc $geneVocFk = null) {
    $this->geneVocFk = $geneVocFk;

    return $this;
  }

  /**
   * Get geneVocFk
   *
   * @return Voc
   */
  public function getGeneVocFk() {
    return $this->geneVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param Voc $datePrecisionVocFk
   *
   * @return ExternalSequence
   */
  public function setDatePrecisionVocFk(Voc $datePrecisionVocFk = null) {
    $this->datePrecisionVocFk = $datePrecisionVocFk;

    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return Voc
   */
  public function getDatePrecisionVocFk() {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set originVocFk
   *
   * @param Voc $originVocFk
   *
   * @return ExternalSequence
   */
  public function setOriginVocFk(Voc $originVocFk = null) {
    $this->originVocFk = $originVocFk;

    return $this;
  }

  /**
   * Get originVocFk
   *
   * @return Voc
   */
  public function getOriginVocFk() {
    return $this->originVocFk;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   *
   * @return ExternalSequence
   */
  public function setSamplingFk(Sampling $samplingFk = null) {
    $this->samplingFk = $samplingFk;

    return $this;
  }

  /**
   * Get samplingFk
   *
   * @return Sampling
   */
  public function getSamplingFk() {
    return $this->samplingFk;
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
   * @param ExternalSequenceAssembler $assembler
   *
   * @return ExternalSequence
   */
  public function addAssembler(ExternalSequenceAssembler $assembler) {
    $assembler->setExternalSequenceFk($this);
    $this->assemblers[] = $assembler;

    return $this;
  }

  /**
   * Remove assembler
   *
   * @param ExternalSequenceAssembler $assembler
   */
  public function removeAssembler(ExternalSequenceAssembler $assembler) {
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
   * @param ExternalSequencePublication $externalSequencePublication
   *
   * @return ExternalSequence
   */
  public function addExternalSequencePublication(ExternalSequencePublication $externalSequencePublication) {
    $externalSequencePublication->setExternalSequenceFk($this);
    $this->externalSequencePublications[] = $externalSequencePublication;

    return $this;
  }

  /**
   * Remove externalSequencePublication
   *
   * @param ExternalSequencePublication $externalSequencePublication
   */
  public function removeExternalSequencePublication(ExternalSequencePublication $externalSequencePublication) {
    $this->externalSequencePublications->removeElement($externalSequencePublication);
  }

  /**
   * Get externalSequencePublications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getExternalSequencePublications() {
    return $this->externalSequencePublications;
  }

  /**
   * Add taxonIdentification
   *
   * @param TaxonIdentification $taxonIdentification
   *
   * @return ExternalSequence
   */
  public function addTaxonIdentification(TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setExternalSequenceFk($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param TaxonIdentification $taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonIdentification) {
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
