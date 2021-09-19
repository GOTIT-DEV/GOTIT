<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * InternalLot
 *
 * @ORM\Table(name="internal_biological_material",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_internal_biological_material__internal_biological_material_c", columns={"internal_biological_material_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_BA1841A5A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5B0B56B73", columns={"pigmentation_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5A897CC9E", columns={"eyes_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_BA1841A52B644673", columns={"storage_box_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLot extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_code", type="string", length=255, nullable=false, unique=true)
   */
  private $code;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="internal_biological_material_date", type="date", nullable=true)
   */
  private $date;

  /**
   * @var string
   *
   * @ORM\Column(name="sequencing_advice", type="text", nullable=true)
   */
  private $sequencingAdvice;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var integer
   *
   * @ORM\Column(name="internal_biological_material_status", type="smallint", nullable=false)
   */
  private $status;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $pigmentationVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $eyesVocFk;

  /**
   * @var \Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private $samplingFk;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="internalLots")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   */
  private $storeFk;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotProducer", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $producers;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotPublication", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotContent", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $contents;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->contents = new ArrayCollection();
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
   * @return InternalLot
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
   * Set date
   *
   * @param \DateTime $date
   *
   * @return InternalLot
   */
  public function setDate($date) {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * Set sequencingAdvice
   *
   * @param string $sequencingAdvice
   *
   * @return InternalLot
   */
  public function setSequencingAdvice($sequencingAdvice) {
    $this->sequencingAdvice = $sequencingAdvice;

    return $this;
  }

  /**
   * Get sequencingAdvice
   *
   * @return string
   */
  public function getSequencingAdvice() {
    return $this->sequencingAdvice;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return InternalLot
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
   * @return InternalLot
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
   * Set pigmentationVocFk
   *
   * @param \App\Entity\Voc $pigmentationVocFk
   *
   * @return InternalLot
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
   * Set eyesVocFk
   *
   * @param \App\Entity\Voc $eyesVocFk
   *
   * @return InternalLot
   */
  public function setEyesVocFk(\App\Entity\Voc $eyesVocFk = null) {
    $this->eyesVocFk = $eyesVocFk;

    return $this;
  }

  /**
   * Get eyesVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getEyesVocFk() {
    return $this->eyesVocFk;
  }

  /**
   * Set samplingFk
   *
   * @param \App\Entity\Sampling $samplingFk
   *
   * @return InternalLot
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
   * Set storeFk
   *
   * @param \App\Entity\Store $storeFk
   *
   * @return InternalLot
   */
  public function setStoreFk(\App\Entity\Store $storeFk = null) {
    $this->storeFk = $storeFk;

    return $this;
  }

  /**
   * Get storeFk
   *
   * @return \App\Entity\Store
   */
  public function getStoreFk() {
    return $this->storeFk;
  }

  /**
   * Add producer
   *
   * @param \App\Entity\InternalLotProducer $producer
   *
   * @return InternalLot
   */
  public function addProducer(\App\Entity\InternalLotProducer $producer) {
    $producer->setInternalLotFk($this);
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   *
   * @param \App\Entity\InternalLotProducer $producer
   */
  public function removeProducer(\App\Entity\InternalLotProducer $producer) {
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
   * @param \App\Entity\InternalLotPublication $publication
   *
   * @return InternalLot
   */
  public function addPublication(\App\Entity\InternalLotPublication $publication) {

    $publication->setInternalLotFk($this);
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   *
   * @param \App\Entity\InternalLotPublication $publication
   */
  public function removePublication(\App\Entity\InternalLotPublication $publication) {
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
   * @return InternalLot
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setInternalLotFk($this);
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
   * Add content
   *
   * @param \App\Entity\InternalLotContent $content
   *
   * @return InternalLot
   */
  public function addContent(\App\Entity\InternalLotContent $content) {
    $content->setInternalLotFk($this);
    $this->contents[] = $content;

    return $this;
  }

  /**
   * Remove content
   *
   * @param \App\Entity\InternalLotContent $content
   */
  public function removeContent(\App\Entity\InternalLotContent $content) {
    $this->contents->removeElement($content);
  }

  /**
   * Get contents
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getContents() {
    return $this->contents;
  }

  /**
   * Set status
   *
   * @param integer $status
   *
   * @return InternalLot
   */
  public function setStatus($status) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return integer
   */
  public function getStatus() {
    return $this->status;
  }
}
