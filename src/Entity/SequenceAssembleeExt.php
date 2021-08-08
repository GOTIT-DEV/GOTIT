<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SequenceAssembleeExt
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
 * @UniqueEntity(fields={"codeSqcAssExt"}, message="This code is already registered")
 * @UniqueEntity(fields={"codeSqcAssExtAlignement"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeExt extends AbstractTimestampedEntity {
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
  private $codeSqcAssExt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="external_sequence_creation_date", type="date", nullable=true)
   */
  private $dateCreationSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_accession_number", type="string", length=255, nullable=false)
   */
  private $accessionNumberSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private $codeSqcAssExtAlignement;

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
  private $taxonOrigineSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_comments", type="text", nullable=true)
   */
  private $commentaireSqcAssExt;

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
  private $origineSqcAssExtVocFk;

  /**
   * @var \Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $collecteFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $statutSqcAssVocFk;

  /**
   * @ORM\OneToMany(targetEntity="SqcExtEstRealisePar", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sqcExtEstRealisePars;

  /**
   * @ORM\OneToMany(targetEntity="ExternalSequencePublication", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $externalSequencePublications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->sqcExtEstRealisePars = new ArrayCollection();
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
   * Set codeSqcAssExt
   *
   * @param string $codeSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setCodeSqcAssExt($codeSqcAssExt) {
    $this->codeSqcAssExt = $codeSqcAssExt;

    return $this;
  }

  /**
   * Get codeSqcAssExt
   *
   * @return string
   */
  public function getCodeSqcAssExt() {
    return $this->codeSqcAssExt;
  }

  /**
   * Set dateCreationSqcAssExt
   *
   * @param \DateTime $dateCreationSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setDateCreationSqcAssExt($dateCreationSqcAssExt) {
    $this->dateCreationSqcAssExt = $dateCreationSqcAssExt;

    return $this;
  }

  /**
   * Get dateCreationSqcAssExt
   *
   * @return \DateTime
   */
  public function getDateCreationSqcAssExt() {
    return $this->dateCreationSqcAssExt;
  }

  /**
   * Set accessionNumberSqcAssExt
   *
   * @param string $accessionNumberSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setAccessionNumberSqcAssExt($accessionNumberSqcAssExt) {
    $this->accessionNumberSqcAssExt = $accessionNumberSqcAssExt;

    return $this;
  }

  /**
   * Get accessionNumberSqcAssExt
   *
   * @return string
   */
  public function getAccessionNumberSqcAssExt() {
    return $this->accessionNumberSqcAssExt;
  }

  /**
   * Set codeSqcAssExtAlignement
   *
   * @param string $codeSqcAssExtAlignement
   *
   * @return SequenceAssembleeExt
   */
  public function setCodeSqcAssExtAlignement($codeSqcAssExtAlignement) {
    $this->codeSqcAssExtAlignement = $codeSqcAssExtAlignement;

    return $this;
  }

  /**
   * Get codeSqcAssExtAlignement
   *
   * @return string
   */
  public function getCodeSqcAssExtAlignement() {
    return $this->codeSqcAssExtAlignement;
  }

  /**
   * Set specimenMolecularNumber
   *
   * @param string $specimenMolecularNumber
   *
   * @return SequenceAssembleeExt
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
   * Set taxonOrigineSqcAssExt
   *
   * @param string $taxonOrigineSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setTaxonOrigineSqcAssExt($taxonOrigineSqcAssExt) {
    $this->taxonOrigineSqcAssExt = $taxonOrigineSqcAssExt;

    return $this;
  }

  /**
   * Get taxonOrigineSqcAssExt
   *
   * @return string
   */
  public function getTaxonOrigineSqcAssExt() {
    return $this->taxonOrigineSqcAssExt;
  }

  /**
   * Set commentaireSqcAssExt
   *
   * @param string $commentaireSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setCommentaireSqcAssExt($commentaireSqcAssExt) {
    $this->commentaireSqcAssExt = $commentaireSqcAssExt;

    return $this;
  }

  /**
   * Get commentaireSqcAssExt
   *
   * @return string
   */
  public function getCommentaireSqcAssExt() {
    return $this->commentaireSqcAssExt;
  }

  /**
   * Set geneVocFk
   *
   * @param \App\Entity\Voc $geneVocFk
   *
   * @return SequenceAssembleeExt
   */
  public function setGeneVocFk(\App\Entity\Voc $geneVocFk = null) {
    $this->geneVocFk = $geneVocFk;

    return $this;
  }

  /**
   * Get geneVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getGeneVocFk() {
    return $this->geneVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return SequenceAssembleeExt
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
   * Set origineSqcAssExtVocFk
   *
   * @param \App\Entity\Voc $origineSqcAssExtVocFk
   *
   * @return SequenceAssembleeExt
   */
  public function setOrigineSqcAssExtVocFk(\App\Entity\Voc $origineSqcAssExtVocFk = null) {
    $this->origineSqcAssExtVocFk = $origineSqcAssExtVocFk;

    return $this;
  }

  /**
   * Get origineSqcAssExtVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getOrigineSqcAssExtVocFk() {
    return $this->origineSqcAssExtVocFk;
  }

  /**
   * Set collecteFk
   *
   * @param \App\Entity\Collecte $collecteFk
   *
   * @return SequenceAssembleeExt
   */
  public function setCollecteFk(\App\Entity\Collecte $collecteFk = null) {
    $this->collecteFk = $collecteFk;

    return $this;
  }

  /**
   * Get collecteFk
   *
   * @return \App\Entity\Collecte
   */
  public function getCollecteFk() {
    return $this->collecteFk;
  }

  /**
   * Set statutSqcAssVocFk
   *
   * @param \App\Entity\Voc $statutSqcAssVocFk
   *
   * @return SequenceAssembleeExt
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
   * Add sqcExtEstRealisePar
   *
   * @param \App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
   *
   * @return SequenceAssembleeExt
   */
  public function addSqcExtEstRealisePar(\App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar) {
    $sqcExtEstRealisePar->setSequenceAssembleeExtFk($this);
    $this->sqcExtEstRealisePars[] = $sqcExtEstRealisePar;

    return $this;
  }

  /**
   * Remove sqcExtEstRealisePar
   *
   * @param \App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
   */
  public function removeSqcExtEstRealisePar(\App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar) {
    $this->sqcExtEstRealisePars->removeElement($sqcExtEstRealisePar);
  }

  /**
   * Get sqcExtEstRealisePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSqcExtEstRealisePars() {
    return $this->sqcExtEstRealisePars;
  }

  /**
   * Add externalSequencePublication
   *
   * @param \App\Entity\ExternalSequencePublication $externalSequencePublication
   *
   * @return SequenceAssembleeExt
   */
  public function addExternalSequencePublication(\App\Entity\ExternalSequencePublication $externalSequencePublication) {
    $externalSequencePublication->setSequenceAssembleeExtFk($this);
    $this->externalSequencePublications[] = $externalSequencePublication;

    return $this;
  }

  /**
   * Remove externalSequencePublication
   *
   * @param \App\Entity\ExternalSequencePublication $externalSequencePublication
   */
  public function removeExternalSequencePublication(\App\Entity\ExternalSequencePublication $externalSequencePublication) {
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
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   *
   * @return SequenceAssembleeExt
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setSequenceAssembleeExtFk($this);
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
}
