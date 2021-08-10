<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TaxonIdentification
 *
 * @ORM\Table(name="identified_species",
 *  indexes={
 *      @ORM\Index(name="IDX_801C3911B669F53D", columns={"type_material_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DFB5F790", columns={"identification_criterion_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DCDD1F756", columns={"external_sequence_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D40D80ECD", columns={"external_biological_material_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D54DBBD4D", columns={"internal_biological_material_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D7B09E3BC", columns={"taxon_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D5F2C6176", columns={"specimen_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D5BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class TaxonIdentification extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="identified_species_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="identification_date", type="date", nullable=true)
   */
  private $identificationDate;

  /**
   * @var string
   *
   * @ORM\Column(name="identified_species_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="type_material_voc_fk", referencedColumnName="id", nullable=true)
   */
  private $materialTypeVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="identification_criterion_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $identificationCriterionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecisionVocFk;

  /**
   * @var \ExternalSequence
   *
   * @ORM\ManyToOne(targetEntity="ExternalSequence", inversedBy="taxonIdentifications")
   * @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   */
  private $externalSequenceFk;

  /**
   * @var \ExternalLot
   *
   * @ORM\ManyToOne(targetEntity="ExternalLot", inversedBy="taxonIdentifications")
   * @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   */
  private $externalLotFk;

  /**
   * @var \InternalLot
   *
   * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="taxonIdentifications")
   * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   */
  private $internalLotFk;

  /**
   * @var \Taxon
   *
   * @ORM\ManyToOne(targetEntity="Taxon")
   * @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
   */
  private $taxonFk;

  /**
   * @var \Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen", inversedBy="taxonIdentifications")
   * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   */
  private $specimenFk;

  /**
   * @var \InternalSequence
   *
   * @ORM\ManyToOne(targetEntity="InternalSequence", inversedBy="taxonIdentifications")
   * @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   */
  private $internalSequenceFk;

  /**
   * @ORM\OneToMany(targetEntity="TaxonCurator", mappedBy="taxonIdentificationFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonCurators;

  public function __construct() {
    $this->taxonCurators = new ArrayCollection();
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
   * Set identificationDate
   *
   * @param \DateTime $identificationDate
   *
   * @return TaxonIdentification
   */
  public function setIdentificationDate($identificationDate) {
    $this->identificationDate = $identificationDate;

    return $this;
  }

  /**
   * Get identificationDate
   *
   * @return \DateTime
   */
  public function getIdentificationDate() {
    return $this->identificationDate;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return TaxonIdentification
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
   * Set identificationCriterionVocFk
   *
   * @param \App\Entity\Voc $identificationCriterionVocFk
   *
   * @return TaxonIdentification
   */
  public function setIdentificationCriterionVocFk(\App\Entity\Voc $identificationCriterionVocFk = null) {
    $this->identificationCriterionVocFk = $identificationCriterionVocFk;

    return $this;
  }

  /**
   * Get identificationCriterionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getIdentificationCriterionVocFk() {
    return $this->identificationCriterionVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return TaxonIdentification
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
   * Set externalSequenceFk
   *
   * @param \App\Entity\ExternalSequence $externalSequenceFk
   *
   * @return TaxonIdentification
   */
  public function setExternalSequenceFk(\App\Entity\ExternalSequence $externalSequenceFk = null) {
    $this->externalSequenceFk = $externalSequenceFk;

    return $this;
  }

  /**
   * Get externalSequenceFk
   *
   * @return \App\Entity\ExternalSequence
   */
  public function getExternalSequenceFk() {
    return $this->externalSequenceFk;
  }

  /**
   * Set externalLotFk
   *
   * @param \App\Entity\ExternalLot $externalLotFk
   *
   * @return TaxonIdentification
   */
  public function setExternalLotFk(\App\Entity\ExternalLot $externalLotFk = null) {
    $this->externalLotFk = $externalLotFk;

    return $this;
  }

  /**
   * Get externalLotFk
   *
   * @return \App\Entity\ExternalLot
   */
  public function getExternalLotFk() {
    return $this->externalLotFk;
  }

  /**
   * Set internalLotFk
   *
   * @param \App\Entity\InternalLot $internalLotFk
   *
   * @return TaxonIdentification
   */
  public function setInternalLotFk(\App\Entity\InternalLot $internalLotFk = null) {
    $this->internalLotFk = $internalLotFk;

    return $this;
  }

  /**
   * Get internalLotFk
   *
   * @return \App\Entity\InternalLot
   */
  public function getInternalLotFk() {
    return $this->internalLotFk;
  }

  /**
   * Set taxonFk
   *
   * @param \App\Entity\Taxon $taxonFk
   *
   * @return TaxonIdentification
   */
  public function setTaxonFk(\App\Entity\Taxon $taxonFk = null) {
    $this->taxonFk = $taxonFk;

    return $this;
  }

  /**
   * Get taxonFk
   *
   * @return \App\Entity\Taxon
   */
  public function getTaxonFk() {
    return $this->taxonFk;
  }

  /**
   * Set specimenFk
   *
   * @param \App\Entity\Specimen $specimenFk
   *
   * @return TaxonIdentification
   */
  public function setSpecimenFk(\App\Entity\Specimen $specimenFk = null) {
    $this->specimenFk = $specimenFk;

    return $this;
  }

  /**
   * Get specimenFk
   *
   * @return \App\Entity\Specimen
   */
  public function getSpecimenFk() {
    return $this->specimenFk;
  }

  /**
   * Set internalSequenceFk
   *
   * @param \App\Entity\InternalSequence $internalSequenceFk
   *
   * @return TaxonIdentification
   */
  public function setInternalSequenceFk(\App\Entity\InternalSequence $internalSequenceFk = null) {
    $this->internalSequenceFk = $internalSequenceFk;

    return $this;
  }

  /**
   * Get internalSequenceFk
   *
   * @return \App\Entity\InternalSequence
   */
  public function getInternalSequenceFk() {
    return $this->internalSequenceFk;
  }

  /**
   * Add taxonCurator
   *
   * @param \App\Entity\taxonCurator $taxonCurator
   *
   * @return TaxonIdentification
   */
  public function addTaxonCurator(\App\Entity\taxonCurator $taxonCurator) {
    $taxonCurator->setTaxonIdentificationFk($this);
    $this->taxonCurators[] = $taxonCurator;

    return $this;
  }

  /**
   * Remove taxonCurator
   *
   * @param \App\Entity\taxonCurator $taxonCurator
   */
  public function removeTaxonCurator(\App\Entity\taxonCurator $taxonCurator) {
    $this->taxonCurators->removeElement($taxonCurator);
  }

  /**
   * Get taxonCurators
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonCurators() {
    return $this->taxonCurators;
  }

  /**
   * Set materialTypeVocFk
   *
   * @param \App\Entity\Voc $materialTypeVocFk
   *
   * @return TaxonIdentification
   */
  public function setMaterialTypeVocFk(\App\Entity\Voc $materialTypeVocFk = null) {
    $this->materialTypeVocFk = $materialTypeVocFk;

    return $this;
  }

  /**
   * Get materialTypeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getMaterialTypeVocFk() {
    return $this->materialTypeVocFk;
  }
}
