<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ExternalLot
 *
 * @ORM\Table(name="external_biological_material",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_external_biological_material__external_biological_material_c", columns={"external_biological_material_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_EEFA43F3662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F382ACDC4", columns={"number_of_specimens_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3B0B56B73", columns={"pigmentation_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3A897CC9E", columns={"eyes_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeLotMaterielExt"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalLot extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="external_biological_material_code", type="string", length=255, nullable=false, unique=true)
   */
  private $codeLotMaterielExt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="external_biological_material_creation_date", type="date", nullable=true)
   */
  private $dateCreationLotMaterielExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_biological_material_comments", type="text", nullable=true)
   */
  private $commentaireLotMaterielExt;

  /**
   * @var string
   *
   * @ORM\Column(name="number_of_specimens_comments", type="text", nullable=true)
   */
  private $specimenQuantityComment;

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
   *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="number_of_specimens_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $specimenQuantityVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $pigmentationVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $yeuxVocFk;

  /**
   * @ORM\OneToMany(targetEntity="ExternalLotProducer", mappedBy="externalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $producers;

  /**
   * @ORM\OneToMany(targetEntity="ExternalLotPublication", mappedBy="externalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
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
   * Set codeLotMaterielExt
   *
   * @param string $codeLotMaterielExt
   *
   * @return ExternalLot
   */
  public function setCodeLotMaterielExt($codeLotMaterielExt) {
    $this->codeLotMaterielExt = $codeLotMaterielExt;

    return $this;
  }

  /**
   * Get codeLotMaterielExt
   *
   * @return string
   */
  public function getCodeLotMaterielExt() {
    return $this->codeLotMaterielExt;
  }

  /**
   * Set dateCreationLotMaterielExt
   *
   * @param \DateTime $dateCreationLotMaterielExt
   *
   * @return ExternalLot
   */
  public function setDateCreationLotMaterielExt($dateCreationLotMaterielExt) {
    $this->dateCreationLotMaterielExt = $dateCreationLotMaterielExt;

    return $this;
  }

  /**
   * Get dateCreationLotMaterielExt
   *
   * @return \DateTime
   */
  public function getDateCreationLotMaterielExt() {
    return $this->dateCreationLotMaterielExt;
  }

  /**
   * Set commentaireLotMaterielExt
   *
   * @param string $commentaireLotMaterielExt
   *
   * @return ExternalLot
   */
  public function setCommentaireLotMaterielExt($commentaireLotMaterielExt) {
    $this->commentaireLotMaterielExt = $commentaireLotMaterielExt;

    return $this;
  }

  /**
   * Get commentaireLotMaterielExt
   *
   * @return string
   */
  public function getCommentaireLotMaterielExt() {
    return $this->commentaireLotMaterielExt;
  }

  /**
   * Set specimenQuantityComment
   *
   * @param string $specimenQuantityComment
   *
   * @return ExternalLot
   */
  public function setSpecimenQuantityComment($specimenQuantityComment) {
    $this->specimenQuantityComment = $specimenQuantityComment;

    return $this;
  }

  /**
   * Get specimenQuantityComment
   *
   * @return string
   */
  public function getSpecimenQuantityComment() {
    return $this->specimenQuantityComment;
  }

  /**
   * Set samplingFk
   *
   * @param \App\Entity\Sampling $samplingFk
   *
   * @return ExternalLot
   */
  public function setSamplingFk(\App\Entity\Sampling $samplingFk = null) {
    $this->samplingFk = $samplingFk;

    return $this;
  }

  /**
   * Get samplingFk
   *
   * @return \App\Entity\Sampling
   */
  public function getSamplingFk() {
    return $this->samplingFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return ExternalLot
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
   * Set specimenQuantityVocFk
   *
   * @param \App\Entity\Voc $specimenQuantityVocFk
   *
   * @return ExternalLot
   */
  public function setSpecimenQuantityVocFk(\App\Entity\Voc $specimenQuantityVocFk = null) {
    $this->specimenQuantityVocFk = $specimenQuantityVocFk;

    return $this;
  }

  /**
   * Get specimenQuantityVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getSpecimenQuantityVocFk() {
    return $this->specimenQuantityVocFk;
  }

  /**
   * Set pigmentationVocFk
   *
   * @param \App\Entity\Voc $pigmentationVocFk
   *
   * @return ExternalLot
   */
  public function setPigmentationVocFk(\App\Entity\Voc $pigmentationVocFk = null) {
    $this->pigmentationVocFk = $pigmentationVocFk;

    return $this;
  }

  /**
   * Get pigmentationVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPigmentationVocFk() {
    return $this->pigmentationVocFk;
  }

  /**
   * Set yeuxVocFk
   *
   * @param \App\Entity\Voc $yeuxVocFk
   *
   * @return ExternalLot
   */
  public function setYeuxVocFk(\App\Entity\Voc $yeuxVocFk = null) {
    $this->yeuxVocFk = $yeuxVocFk;

    return $this;
  }

  /**
   * Get yeuxVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getYeuxVocFk() {
    return $this->yeuxVocFk;
  }

  /**
   * Add producer
   *
   * @param \App\Entity\ExternalLotProducer $producer
   *
   * @return ExternalLot
   */
  public function addProducer(\App\Entity\ExternalLotProducer $producer) {
    $producer->setExternalLotFk($this);
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   *
   * @param \App\Entity\ExternalLotProducer $producer
   */
  public function removeProducer(\App\Entity\ExternalLotProducer $producer) {
    $this->producers->removeElement($producer);
  }

  /**
   * Get producers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProducers() {
    return $this->producers;
  }

  /**
   * Add publication
   *
   * @param \App\Entity\ExternalLotPublication $publication
   *
   * @return ExternalLot
   */
  public function addPublication(\App\Entity\ExternalLotPublication $publication) {
    $publication->setExternalLotFk($this);
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   *
   * @param \App\Entity\ExternalLotPublication $publication
   */
  public function removePublication(\App\Entity\ExternalLotPublication $publication) {
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
   * @return ExternalLot
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setExternalLotFk($this);
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
