<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
   * @ORM\Column(name="internal_sequence_code", type="string", length=1024, nullable=false)
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
   *   @ORM\JoinColumn(name="internal_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $status;

  /**
   * @ORM\OneToMany(targetEntity="InternalSequenceAssembler", mappedBy="internalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $assemblers;

  /**
   * @ORM\OneToMany(targetEntity="InternalSequencePublication", mappedBy="internalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalSequenceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="InternalSequenceAssembly", mappedBy="internalSequenceFk", cascade={"persist"})
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
   * @return InternalSequence
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
   * Set creationDate
   *
   * @param \DateTime $creationDate
   *
   * @return InternalSequence
   */
  public function setCreationDate($creationDate) {
    $this->creationDate = $creationDate;

    return $this;
  }

  /**
   * Get creationDate
   *
   * @return \DateTime
   */
  public function getCreationDate() {
    return $this->creationDate;
  }

  /**
   * Set accessionNumber
   *
   * @param string $accessionNumber
   *
   * @return InternalSequence
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
   * @return InternalSequence
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
   * Set comment
   *
   * @param string $comment
   *
   * @return InternalSequence
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
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return InternalSequence
   */
  public function setDatePrecisionVocFk(\App\Entity\Voc $datePrecisionVocFk = null) {
    $this->datePrecisionVocFk = $datePrecisionVocFk;

    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getDatePrecisionVocFk() {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set status
   *
   * @param \App\Entity\Voc $status
   *
   * @return InternalSequence
   */
  public function setStatus(\App\Entity\Voc $status = null) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return \App\Entity\Voc
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Add assembler
   *
   * @param \App\Entity\InternalSequenceAssembler $assembler
   *
   * @return InternalSequence
   */
  public function addAssembler(\App\Entity\InternalSequenceAssembler $assembler) {
    $assembler->setInternalSequenceFk($this);
    $this->assemblers[] = $assembler;

    return $this;
  }

  /**
   * Remove assembler
   *
   * @param \App\Entity\InternalSequenceAssembler $assembler
   */
  public function removeAssembler(\App\Entity\InternalSequenceAssembler $assembler) {
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
   * Add publication
   *
   * @param \App\Entity\InternalSequencePublication $publication
   *
   * @return InternalSequence
   */
  public function addPublication(\App\Entity\InternalSequencePublication $publication) {
    $publication->setInternalSequenceFk($this);
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   *
   * @param \App\Entity\InternalSequencePublication $publication
   */
  public function removePublication(\App\Entity\InternalSequencePublication $publication) {
    $this->publications->removeElement($publication);
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
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   *
   * @return InternalSequence
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setInternalSequenceFk($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   */
  public function removeTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
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

  /**
   * Add assembly
   *
   * @param \App\Entity\InternalSequenceAssembly $assembly
   *
   * @return InternalSequence
   */
  public function addAssembly(\App\Entity\InternalSequenceAssembly $assembly) {
    $assembly->setInternalSequenceFk($this);
    $this->assemblies[] = $assembly;

    return $this;
  }

  /**
   * Remove assembly
   *
   * @param \App\Entity\InternalSequenceAssembly $assembly
   */
  public function removeAssembly(\App\Entity\InternalSequenceAssembly $assembly) {
    $this->assemblies->removeElement($assembly);
  }

  /**
   * Get assembly
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAssemblies() {
    return $this->assemblies;
  }

  /**
   * Get geneVocFk
   *
   * This assumes that a sequence matches ONE gene only,
   * even if it was processed through multiple chromatograms
   *
   * @return mixed
   */
  public function getGeneVocFk() {
    $process = $this->assemblies->first();
    return $process
    ? $process
      ->getChromatogramFk()
      ->getPcrFk()
      ->getGeneVocFk()
    : null;
  }

  /**
   * Get specimenFk
   *
   * this assumes that a sequence matches ONE specimen only,
   * even if it was processed through multiple chromatograms
   *
   * @return mixed
   */
  public function getSpecimenFk() {
    $process = $this->assemblies->first();
    return $process
    ? $process
      ->getChromatogramFk()
      ->getPcrFk()
      ->getDnaFk()
      ->getSpecimenFk()
    : null;
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
      $seqCodeElts = [];
      $statusCode = $this->getStatus()->getCode();
      if (substr($statusCode, 0, 5) != 'VALID') {
        $seqCodeElts[] = $statusCode;
      }

      $lastTaxonCode = $this->getTaxonIdentifications()->last()
        ->getTaxonFk()
        ->getCode();
      $seqCodeElts[] = $lastTaxonCode;

      $specimen = $this->getSpecimenFk();
      $samplingCode = $specimen->getInternalLotFk()
        ->getSamplingFk()
        ->getCodeCollecte();
      $seqCodeElts[] = $samplingCode;

      $specimenCode = $specimen->getNumIndBiomol();
      $seqCodeElts[] = $specimenCode;

      $chromatoCodeList = $this->getAssemblies()
        ->map(
          function ($seqProcessing) {
            $chromato = $seqProcessing->getChromatogramFk();
            $code = $chromato->getCode();
            $specificite = $chromato->getPcrFk()->getSpecificityVocFk()->getCode();
            return $code . '|' . $specificite;
          }
        )
        ->toArray();
      $chromatoCodeStr = implode("-", $chromatoCodeList);
      $seqCodeElts[] = $chromatoCodeStr;
      $seqCode = implode("_", $seqCodeElts);
    }
    $this->setAlignmentCode($seqCode);
    return $seqCode;
  }
}
