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
  private $dateIdentification;

  /**
   * @var string
   *
   * @ORM\Column(name="identified_species_comments", type="text", nullable=true)
   */
  private $commentaireEspId;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="type_material_voc_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $typeMaterielVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="identification_criterion_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $critereIdentificationVocFk;

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
   * @var \ExternalSequence
   *
   * @ORM\ManyToOne(targetEntity="ExternalSequence", inversedBy="taxonIdentifications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   * })
   */
  private $externalSequenceFk;

  /**
   * @var \ExternalLot
   *
   * @ORM\ManyToOne(targetEntity="ExternalLot", inversedBy="taxonIdentifications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   * })
   */
  private $externalLotFk;

  /**
   * @var \InternalLot
   *
   * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="taxonIdentifications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   * })
   */
  private $internalLotFk;

  /**
   * @var \ReferentielTaxon
   *
   * @ORM\ManyToOne(targetEntity="ReferentielTaxon")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $referentielTaxonFk;

  /**
   * @var \Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen", inversedBy="taxonIdentifications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   * })
   */
  private $specimenFk;

  /**
   * @var \InternalSequence
   *
   * @ORM\ManyToOne(targetEntity="InternalSequence", inversedBy="taxonIdentifications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
   * })
   */
  private $internalSequenceFk;

  /**
   * @ORM\OneToMany(targetEntity="PersonSpeciesId", mappedBy="taxonIdentificationFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $personSpeciesIds;

  public function __construct() {
    $this->personSpeciesIds = new ArrayCollection();
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
   * Set dateIdentification
   *
   * @param \DateTime $dateIdentification
   *
   * @return TaxonIdentification
   */
  public function setDateIdentification($dateIdentification) {
    $this->dateIdentification = $dateIdentification;

    return $this;
  }

  /**
   * Get dateIdentification
   *
   * @return \DateTime
   */
  public function getDateIdentification() {
    return $this->dateIdentification;
  }

  /**
   * Set commentaireEspId
   *
   * @param string $commentaireEspId
   *
   * @return TaxonIdentification
   */
  public function setCommentaireEspId($commentaireEspId) {
    $this->commentaireEspId = $commentaireEspId;

    return $this;
  }

  /**
   * Get commentaireEspId
   *
   * @return string
   */
  public function getCommentaireEspId() {
    return $this->commentaireEspId;
  }

  /**
   * Set critereIdentificationVocFk
   *
   * @param \App\Entity\Voc $critereIdentificationVocFk
   *
   * @return TaxonIdentification
   */
  public function setCritereIdentificationVocFk(\App\Entity\Voc $critereIdentificationVocFk = null) {
    $this->critereIdentificationVocFk = $critereIdentificationVocFk;

    return $this;
  }

  /**
   * Get critereIdentificationVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getCritereIdentificationVocFk() {
    return $this->critereIdentificationVocFk;
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
   * Set referentielTaxonFk
   *
   * @param \App\Entity\ReferentielTaxon $referentielTaxonFk
   *
   * @return TaxonIdentification
   */
  public function setReferentielTaxonFk(\App\Entity\ReferentielTaxon $referentielTaxonFk = null) {
    $this->referentielTaxonFk = $referentielTaxonFk;

    return $this;
  }

  /**
   * Get referentielTaxonFk
   *
   * @return \App\Entity\ReferentielTaxon
   */
  public function getReferentielTaxonFk() {
    return $this->referentielTaxonFk;
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
   * Add personSpeciesId
   *
   * @param \App\Entity\personSpeciesId $personSpeciesId
   *
   * @return TaxonIdentification
   */
  public function addPersonSpeciesId(\App\Entity\personSpeciesId $personSpeciesId) {
    $personSpeciesId->setTaxonIdentificationFk($this);
    $this->personSpeciesIds[] = $personSpeciesId;

    return $this;
  }

  /**
   * Remove personSpeciesId
   *
   * @param \App\Entity\personSpeciesId $personSpeciesId
   */
  public function removePersonSpeciesId(\App\Entity\personSpeciesId $personSpeciesId) {
    $this->personSpeciesIds->removeElement($personSpeciesId);
  }

  /**
   * Get personSpeciesIds
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPersonSpeciesIds() {
    return $this->personSpeciesIds;
  }

  /**
   * Set typeMaterielVocFk
   *
   * @param \App\Entity\Voc $typeMaterielVocFk
   *
   * @return TaxonIdentification
   */
  public function setTypeMaterielVocFk(\App\Entity\Voc $typeMaterielVocFk = null) {
    $this->typeMaterielVocFk = $typeMaterielVocFk;

    return $this;
  }

  /**
   * Get typeMaterielVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getTypeMaterielVocFk() {
    return $this->typeMaterielVocFk;
  }
}
