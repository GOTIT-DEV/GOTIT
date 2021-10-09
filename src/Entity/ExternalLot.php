<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
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
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalLot extends AbstractTimestampedEntity {
  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="external_biological_material_code", type="string", length=255, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="external_biological_material_creation_date", type="date", nullable=true)
   */
  private ?\DateTime $creationDate = null;

  /**
   * @ORM\Column(name="external_biological_material_comments", type="text", nullable=true)
   */
  private ?string $comment = null;

  /**
   * @ORM\Column(name="number_of_specimens_comments", type="text", nullable=true)
   */
  private ?string $specimenQuantityComment = null;

  /**
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private Sampling $sampling;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $datePrecision;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="number_of_specimens_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $specimenQuantity;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $pigmentation;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $eyes;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="external_biological_material_is_processed_by",
   *  joinColumns={@ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $producers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   * @ORM\JoinTable(name="external_biological_material_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalLot", cascade={"persist"})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $taxonIdentifications;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
  }

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set code
   */
  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   */
  public function getCode(): string {
    return $this->code;
  }

  /**
   * Set creationDate
   */
  public function setCreationDate(?\DateTime $date): self {
    $this->creationDate = $date;

    return $this;
  }

  /**
   * Get creationDate
   */
  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  /**
   * Set comment
   */
  public function setComment(string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set specimenQuantityComment
   */
  public function setSpecimenQuantityComment(?string $specimenQuantityComment): self {
    $this->specimenQuantityComment = $specimenQuantityComment;

    return $this;
  }

  /**
   * Get specimenQuantityComment
   */
  public function getSpecimenQuantityComment(): ?string {
    return $this->specimenQuantityComment;
  }

  /**
   * Set sampling
   */
  public function setSampling(Sampling $sampling): self {
    $this->sampling = $sampling;

    return $this;
  }

  /**
   * Get sampling
   */
  public function getSampling(): Sampling {
    return $this->sampling;
  }

  /**
   * Set datePrecision
   */
  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  /**
   * Get datePrecision
   */
  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  /**
   * Set specimenQuantity
   */
  public function setSpecimenQuantity(Voc $specQty): self {
    $this->specimenQuantity = $specQty;

    return $this;
  }

  /**
   * Get specimenQuantity
   */
  public function getSpecimenQuantity(): Voc {
    return $this->specimenQuantity;
  }

  /**
   * Set pigmentation
   */
  public function setPigmentation(Voc $pigmentation): self {
    $this->pigmentation = $pigmentation;

    return $this;
  }

  /**
   * Get pigmentation
   */
  public function getPigmentation(): Voc {
    return $this->pigmentation;
  }

  /**
   * Set eyes
   */
  public function setEyes(Voc $eyes): self {
    $this->eyes = $eyes;

    return $this;
  }

  /**
   * Get eyes
   */
  public function getEyes(): Voc {
    return $this->eyes;
  }

  /**
   * Add producer
   */
  public function addProducer(Person $producer): self {
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   */
  public function removeProducer(Person $producer): self {
    $this->producers->removeElement($producer);

    return $this;
  }

  /**
   * Get producers
   */
  public function getProducers(): Collection {
    return $this->producers;
  }

  /**
   * Add publication
   */
  public function addPublication(Source $publication): self {
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   */
  public function removePublication(Source $publication): self {
    $this->publications->removeElement($publication);

    return $this;
  }

  /**
   * Get publications
   */
  public function getPublications(): Collection {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   */
  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalLot($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  /**
   * Remove taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  /**
   * Get taxonIdentifications
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
