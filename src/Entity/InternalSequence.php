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
 * @UniqueEntity(fields={"codeSqcAss"}, message="This code is already registered")
 * @UniqueEntity(fields={"codeSqcAlignement"}, message="This code is already registered")
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
  private $codeSqcAss;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="internal_sequence_creation_date", type="date", nullable=true)
   */
  private $dateCreationSqcAss;

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
  private $codeSqcAlignement;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_comments", type="text", nullable=true)
   */
  private $commentaireSqcAss;

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
  private $statutSqcAssVocFk;

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
   * Set codeSqcAss
   *
   * @param string $codeSqcAss
   *
   * @return InternalSequence
   */
  public function setCodeSqcAss($codeSqcAss) {
    $this->codeSqcAss = $codeSqcAss;

    return $this;
  }

  /**
   * Get codeSqcAss
   *
   * @return string
   */
  public function getCodeSqcAss() {
    return $this->codeSqcAss;
  }

  /**
   * Set dateCreationSqcAss
   *
   * @param \DateTime $dateCreationSqcAss
   *
   * @return InternalSequence
   */
  public function setDateCreationSqcAss($dateCreationSqcAss) {
    $this->dateCreationSqcAss = $dateCreationSqcAss;

    return $this;
  }

  /**
   * Get dateCreationSqcAss
   *
   * @return \DateTime
   */
  public function getDateCreationSqcAss() {
    return $this->dateCreationSqcAss;
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
   * Set codeSqcAlignement
   *
   * @param string $codeSqcAlignement
   *
   * @return InternalSequence
   */
  public function setCodeSqcAlignement($codeSqcAlignement) {
    $this->codeSqcAlignement = $codeSqcAlignement;

    return $this;
  }

  /**
   * Get codeSqcAlignement
   *
   * @return string
   */
  public function getCodeSqcAlignement() {
    return $this->codeSqcAlignement;
  }

  /**
   * Set commentaireSqcAss
   *
   * @param string $commentaireSqcAss
   *
   * @return InternalSequence
   */
  public function setCommentaireSqcAss($commentaireSqcAss) {
    $this->commentaireSqcAss = $commentaireSqcAss;

    return $this;
  }

  /**
   * Get commentaireSqcAss
   *
   * @return string
   */
  public function getCommentaireSqcAss() {
    return $this->commentaireSqcAss;
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
   * Set statutSqcAssVocFk
   *
   * @param \App\Entity\Voc $statutSqcAssVocFk
   *
   * @return InternalSequence
   */
  public function setStatutSqcAssVocFk(\App\Entity\Voc $statutSqcAssVocFk = null) {
    $this->statutSqcAssVocFk = $statutSqcAssVocFk;

    return $this;
  }

  /**
   * Get statutSqcAssVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getStatutSqcAssVocFk() {
    return $this->statutSqcAssVocFk;
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
      ->getChromatogrammeFk()
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
      ->getChromatogrammeFk()
      ->getPcrFk()
      ->getAdnFk()
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
      $statusCode = $this->getStatutSqcAssVocFk()->getCode();
      if (substr($statusCode, 0, 5) != 'VALID') {
        $seqCodeElts[] = $statusCode;
      }

      $lastTaxonCode = $this->getTaxonIdentifications()->last()
        ->getReferentielTaxonFk()
        ->getCodeTaxon();
      $seqCodeElts[] = $lastTaxonCode;

      $specimen = $this->getSpecimenFk();
      $samplingCode = $specimen->getInternalLotFk()
        ->getCollecteFk()
        ->getCodeCollecte();
      $seqCodeElts[] = $samplingCode;

      $specimenCode = $specimen->getNumIndBiomol();
      $seqCodeElts[] = $specimenCode;

      $chromatoCodeList = $this->getAssemblies()
        ->map(
          function ($seqProcessing) {
            $chromato = $seqProcessing->getChromatogrammeFk();
            $code = $chromato->getCodeChromato();
            $specificite = $chromato->getPcrFk()->getSpecificiteVocFk()->getCode();
            return $code . '|' . $specificite;
          }
        )
        ->toArray();
      $chromatoCodeStr = implode("-", $chromatoCodeList);
      $seqCodeElts[] = $chromatoCodeStr;
      $seqCode = implode("_", $seqCodeElts);
    }
    $this->setCodeSqcAlignement($seqCode);
    return $seqCode;
  }
}
