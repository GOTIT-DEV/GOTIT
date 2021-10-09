<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A lot of biological material with partial sampling information available
 */
#[ORM\Entity]
#[ORM\Table(name: 'external_biological_material')]
#[ORM\UniqueConstraint(
  name: 'uk_external_biological_material__external_biological_material_c',
  columns: ['external_biological_material_code']
)]
#[ORM\Index(name: 'IDX_EEFA43F3662D9B98', columns: ['sampling_fk'])]
#[ORM\Index(name: 'IDX_EEFA43F3A30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_EEFA43F382ACDC4', columns: ['number_of_specimens_voc_fk'])]
#[ORM\Index(name: 'IDX_EEFA43F3B0B56B73', columns: ['pigmentation_voc_fk'])]
#[ORM\Index(name: 'IDX_EEFA43F3A897CC9E', columns: ['eyes_voc_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class ExternalLot extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  private int $id;

  #[ORM\Column(name: 'external_biological_material_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'external_biological_material_creation_date', type: 'date', nullable: true)]
  private ?\DateTime $creationDate = null;

  #[ORM\Column(name: 'external_biological_material_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\Column(name: 'number_of_specimens_comments', type: 'text', nullable: true)]
  private ?string $specimenQuantityComment = null;

  #[ORM\ManyToOne(targetEntity: 'Sampling')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id', nullable: false)]
  private Sampling $sampling;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'number_of_specimens_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $specimenQuantity;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'pigmentation_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $pigmentation;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'eyes_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $eyes;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'external_biological_material_is_processed_by')]
  #[ORM\JoinColumn(name: 'external_biological_material_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $producers;

  #[ORM\ManyToMany(targetEntity: 'Source', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'external_biological_material_is_published_in')]
  #[ORM\JoinColumn(name: 'external_biological_material_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'source_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $publications;

  #[ORM\OneToMany(targetEntity: 'TaxonIdentification', mappedBy: 'externalLot', cascade: [
    'persist',
  ])]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $taxonIdentifications;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  public function getCode(): string {
    return $this->code;
  }

  public function setCreationDate(?\DateTime $date): self {
    $this->creationDate = $date;

    return $this;
  }

  public function getCreationDate(): ?\DateTime {
    return $this->creationDate;
  }

  public function setComment(string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setSpecimenQuantityComment(?string $specimenQuantityComment): self {
    $this->specimenQuantityComment = $specimenQuantityComment;

    return $this;
  }

  public function getSpecimenQuantityComment(): ?string {
    return $this->specimenQuantityComment;
  }

  public function setSampling(Sampling $sampling): self {
    $this->sampling = $sampling;

    return $this;
  }

  public function getSampling(): Sampling {
    return $this->sampling;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  public function setSpecimenQuantity(Voc $specQty): self {
    $this->specimenQuantity = $specQty;

    return $this;
  }

  public function getSpecimenQuantity(): Voc {
    return $this->specimenQuantity;
  }

  public function setPigmentation(Voc $pigmentation): self {
    $this->pigmentation = $pigmentation;

    return $this;
  }

  public function getPigmentation(): Voc {
    return $this->pigmentation;
  }

  public function setEyes(Voc $eyes): self {
    $this->eyes = $eyes;

    return $this;
  }

  public function getEyes(): Voc {
    return $this->eyes;
  }

  public function addProducer(Person $producer): self {
    $this->producers[] = $producer;

    return $this;
  }

  public function removeProducer(Person $producer): self {
    $this->producers->removeElement($producer);

    return $this;
  }

  public function getProducers(): Collection {
    return $this->producers;
  }

  public function addPublication(Source $publication): self {
    $this->publications[] = $publication;

    return $this;
  }

  public function removePublication(Source $publication): self {
    $this->publications->removeElement($publication);

    return $this;
  }

  public function getPublications(): Collection {
    return $this->publications;
  }

  public function addTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setExternalLot($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
