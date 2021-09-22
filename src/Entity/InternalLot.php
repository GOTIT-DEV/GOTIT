<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
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
  private $datePrecision;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $pigmentation;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $eyes;

  /**
   * @var \Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private $sampling;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="internalLots")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   */
  private $store;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="internal_biological_material_is_treated_by",
   *  joinColumns={@ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $producers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   * @ORM\JoinTable(name="internal_biological_material_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalLot", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotContent", mappedBy="internalLot", cascade={"persist"})
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
   * Set datePrecision
   *
   * @param Voc $datePrecision
   *
   * @return InternalLot
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
   * Set pigmentation
   *
   * @param Voc $pigmentation
   *
   * @return InternalLot
   */
  public function setPigmentation(Voc $pigmentation = null) {
    $this->pigmentation = $pigmentation;

    return $this;
  }

  /**
   * Get pigmentation
   *
   * @return Voc
   */
  public function getPigmentation() {
    return $this->pigmentation;
  }

  /**
   * Set eyes
   *
   * @param Voc $eyes
   *
   * @return InternalLot
   */
  public function setEyes(Voc $eyes = null) {
    $this->eyes = $eyes;

    return $this;
  }

  /**
   * Get eyes
   *
   * @return Voc
   */
  public function getEyes() {
    return $this->eyes;
  }

  /**
   * Set sampling
   *
   * @param Sampling $sampling
   *
   * @return InternalLot
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
   * Set store
   *
   * @param Store $store
   *
   * @return InternalLot
   */
  public function setStore(Store $store = null) {
    $this->store = $store;

    return $this;
  }

  /**
   * Get store
   *
   * @return Store
   */
  public function getStore() {
    return $this->store;
  }

  /**
   * Add producer
   *
   * @param Person $producer
   *
   * @return InternalLot
   */
  public function addProducer(Person $producer) {
    $producer->setInternalLot($this);
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   *
   * @param Person $producer
   */
  public function removeProducer(Person $producer) {
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
   * @param Source $publication
   *
   * @return InternalLot
   */
  public function addPublication(Source $publication) {
    $this->publications[] = $publication;
    return $this;
  }

  /**
   * Remove publication
   *
   * @param Source $publication
   */
  public function removePublication(Source $publication) {
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
   * @param TaxonIdentification $taxonIdentification
   *
   * @return InternalLot
   */
  public function addTaxonIdentification(TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setInternalLot($this);
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

  /**
   * Add content
   *
   * @param InternalLotContent $content
   *
   * @return InternalLot
   */
  public function addContent(InternalLotContent $content) {
    $content->setInternalLot($this);
    $this->contents[] = $content;

    return $this;
  }

  /**
   * Remove content
   *
   * @param InternalLotContent $content
   */
  public function removeContent(InternalLotContent $content) {
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
