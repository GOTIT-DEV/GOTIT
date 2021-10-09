<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLot extends AbstractTimestampedEntity {
  /**
   * @ORM\Id
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="internal_biological_material_code", type="string", length=255, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="internal_biological_material_date", type="date", nullable=true)
   */
  private ?\DateTime $date;

  /**
   * @ORM\Column(name="sequencing_advice", type="text", nullable=true)
   */
  private ?string $sequencingAdvice;

  /**
   * @ORM\Column(name="internal_biological_material_comments", type="text", nullable=true)
   */
  private ?string $comment;

  /**
   * @ORM\Column(name="internal_biological_material_status", type="boolean", nullable=false)
   */
  private bool $status;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $datePrecision;

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
   * @ORM\ManyToOne(targetEntity="Sampling")
   * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   */
  private Sampling $sampling;

  /**
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="internalLots")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   */
  private ?Store $store;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="internal_biological_material_is_treated_by",
   *  joinColumns={@ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $producers;

  /**
   * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
   * @ORM\JoinTable(name="internal_biological_material_is_published_in",
   *  joinColumns={@ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalLot", cascade={"persist"})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotContent", mappedBy="internalLot", cascade={"persist"}, orphanRemoval=true)
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $contents;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->contents = new ArrayCollection();
  }

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set code
   *
   * @return InternalLot
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
   * Set date
   */
  public function setDate(?\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   */
  public function getDate(): ?\DateTime {
    return $this->date;
  }

  /**
   * Set sequencingAdvice
   */
  public function setSequencingAdvice(?string $sequencingAdvice): self {
    $this->sequencingAdvice = $sequencingAdvice;

    return $this;
  }

  /**
   * Get sequencingAdvice
   */
  public function getSequencingAdvice(): ?string {
    return $this->sequencingAdvice;
  }

  /**
   * Set comment
   */
  public function setComment(?string $comment): self {
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
   * Set store
   */
  public function setStore(?Store $store): self {
    $this->store = $store;

    return $this;
  }

  /**
   * Get store
   */
  public function getStore(): Store {
    return $this->store;
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
    $taxonId->setInternalLot($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  /**
   * Remove taxonIdentification
   */
  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalLot(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  /**
   * Get taxonIdentifications
   */
  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }

  /**
   * Add content
   */
  public function addContent(InternalLotContent $content): self {
    $content->setInternalLot($this);
    $this->contents[] = $content;

    return $this;
  }

  /**
   * Remove content
   */
  public function removeContent(InternalLotContent $content): self {
    $this->contents->removeElement($content);

    return $this;
  }

  /**
   * Get contents
   */
  public function getContents(): Collection {
    return $this->contents;
  }

  /**
   * Set status
   */
  public function setStatus(bool $status): self {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   */
  public function getStatus(): bool {
    return $this->status;
  }
}
