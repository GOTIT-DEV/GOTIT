<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
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
  private $code;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="external_biological_material_creation_date", type="date", nullable=true)
   */
  private $creationDate;

  /**
   * @var string
   *
   * @ORM\Column(name="external_biological_material_comments", type="text", nullable=true)
   */
  private $comment;

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
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private $samplingFk;

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
   * @ORM\JoinColumn(name="number_of_specimens_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $specimenQuantityVocFk;

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
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * Set code
   *
   * @param string $code
   *
   * @return ExternalLot
   */
  public function setCode($code): ExternalLot {
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
   * Set creationDate
   *
   * @param \DateTime $creationDate
   *
   * @return ExternalLot
   */
  public function setCreationDate($date): ExternalLot {
    $this->creationDate = $date;
    return $this;
  }

  /**
   * Get creationDate
   *
   * @return \DateTime
   */
  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return ExternalLot
   */
  public function setComment($comment): ExternalLot {
    $this->comment = $comment;
    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set specimenQuantityComment
   *
   * @param string $specimenQuantityComment
   *
   * @return ExternalLot
   */
  public function setSpecimenQuantityComment($specimenQuantityComment): ExternalLot {
    $this->specimenQuantityComment = $specimenQuantityComment;
    return $this;
  }

  /**
   * Get specimenQuantityComment
   *
   * @return string
   */
  public function getSpecimenQuantityComment(): ?string {
    return $this->specimenQuantityComment;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   *
   * @return ExternalLot
   */
  public function setSamplingFk(Sampling $samplingFk = null): ExternalLot {
    $this->samplingFk = $samplingFk;
    return $this;
  }

  /**
   * Get samplingFk
   *
   * @return Sampling
   */
  public function getSamplingFk(): ?Sampling {
    return $this->samplingFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param Voc $datePrecisionVocFk
   *
   * @return ExternalLot
   */
  public function setDatePrecisionVocFk(Voc $datePrecVocFk = null): ExternalLot {
    $this->datePrecisionVocFk = $datePrecVocFk;
    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return Voc
   */
  public function getDatePrecisionVocFk(): ?Voc {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set specimenQuantityVocFk
   *
   * @param Voc $specimenQuantityVocFk
   *
   * @return ExternalLot
   */
  public function setSpecimenQuantityVocFk(Voc $specQtyVocFk = null): ExternalLot {
    $this->specimenQuantityVocFk = $specQtyVocFk;
    return $this;
  }

  /**
   * Get specimenQuantityVocFk
   *
   * @return Voc
   */
  public function getSpecimenQuantityVocFk(): ?Voc {
    return $this->specimenQuantityVocFk;
  }

  /**
   * Set pigmentationVocFk
   *
   * @param Voc $pigmentationVocFk
   *
   * @return ExternalLot
   */
  public function setPigmentationVocFk(Voc $pigmVocFk = null): ExternalLot {
    $this->pigmentationVocFk = $pigmVocFk;
    return $this;
  }

  /**
   * Get pigmentationVocFk
   *
   * @return Voc
   */
  public function getPigmentationVocFk(): ?Voc {
    return $this->pigmentationVocFk;
  }

  /**
   * Set eyesVocFk
   *
   * @param Voc $eyesVocFk
   *
   * @return ExternalLot
   */
  public function setEyesVocFk(Voc $eyesVocFk = null): ExternalLot {
    $this->eyesVocFk = $eyesVocFk;
    return $this;
  }

  /**
   * Get eyesVocFk
   *
   * @return Voc
   */
  public function getEyesVocFk(): ?Voc {
    return $this->eyesVocFk;
  }

  /**
   * Add producer
   *
   * @param ExternalLotProducer $producer
   *
   * @return ExternalLot
   */
  public function addProducer(ExternalLotProducer $producer): ExternalLot {
    $producer->setExternalLotFk($this);
    $this->producers[] = $producer;
    return $this;
  }

  /**
   * Remove producer
   *
   * @param ExternalLotProducer $producer
   */
  public function removeProducer(ExternalLotProducer $producer): ExternalLot {
    $this->producers->removeElement($producer);
    return $this;
  }

  /**
   * Get producers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProducers(): Collection {
    return $this->producers;
  }

  /**
   * Add publication
   *
   * @param ExternalLotPublication $publication
   *
   * @return ExternalLot
   */
  public function addPublication(ExternalLotPublication $pub): ExternalLot {
    $pub->setExternalLotFk($this);
    $this->publications[] = $pub;
    return $this;
  }

  /**
   * Remove publication
   *
   * @param ExternalLotPublication $publication
   */
  public function removePublication(ExternalLotPublication $pub): ExternalLot {
    $this->publications->removeElement($pub);
    return $this;
  }

  /**
   * Get publications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPublications(): Collection {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   *
   * @param TaxonIdentification $taxonIdentification
   *
   * @return ExternalLot
   */
  public function addTaxonIdentification(TaxonIdentification $taxonId): ExternalLot {
    $taxonId->setExternalLotFk($this);
    $this->taxonIdentifications[] = $taxonId;
    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param TaxonIdentification $taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): ExternalLot {
    $this->taxonIdentifications->removeElement($taxonId);
    return $this;
  }

  /**
   * Get taxonIdentifications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
