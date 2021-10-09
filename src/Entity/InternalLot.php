<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A lot of biological material where full traceability is available
 */
#[ORM\Entity]
#[ORM\Table(name: 'internal_biological_material')]
#[ORM\UniqueConstraint(
  name: 'uk_internal_biological_material__internal_biological_material_c',
  columns: ['internal_biological_material_code']
)]
#[ORM\Index(name: 'IDX_BA1841A5A30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_BA1841A5B0B56B73', columns: ['pigmentation_voc_fk'])]
#[ORM\Index(name: 'IDX_BA1841A5A897CC9E', columns: ['eyes_voc_fk'])]
#[ORM\Index(name: 'IDX_BA1841A5662D9B98', columns: ['sampling_fk'])]
#[ORM\Index(name: 'IDX_BA1841A52B644673', columns: ['storage_box_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]

class InternalLot extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  private int $id;

  #[ORM\Column(name: 'internal_biological_material_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'internal_biological_material_date', type: 'date', nullable: true)]
  private ?\DateTime $date = null;

  #[ORM\Column(name: 'sequencing_advice', type: 'text', nullable: true)]
  private ?string $sequencingAdvice = null;

  #[ORM\Column(name: 'internal_biological_material_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\Column(name: 'internal_biological_material_status', type: 'boolean', nullable: false)]
  private bool $status;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'pigmentation_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $pigmentation;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'eyes_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $eyes;

  #[ORM\ManyToOne(targetEntity: 'Sampling')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id', nullable: false)]
  private Sampling $sampling;

  #[ORM\ManyToOne(targetEntity: 'Store', inversedBy: 'internalLots')]
  #[ORM\JoinColumn(name: 'storage_box_fk', referencedColumnName: 'id', nullable: true)]
  private ?Store $store = null;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'internal_biological_material_is_treated_by')]
  #[ORM\JoinColumn(name: 'internal_biological_material_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $producers;

  #[ORM\ManyToMany(targetEntity: 'Source', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'internal_biological_material_is_published_in')]
  #[ORM\JoinColumn(name: 'internal_biological_material_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'source_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $publications;

  #[ORM\OneToMany(
    targetEntity: 'TaxonIdentification',
    mappedBy: 'internalLot',
    cascade: ['persist']
  )]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $taxonIdentifications;

  #[ORM\OneToMany(
    targetEntity: 'InternalLotContent',
    mappedBy: 'internalLot',
    cascade: ['persist'],
    orphanRemoval: true
  )]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private Collection $contents;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->contents = new ArrayCollection();
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

  public function setDate(?\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDate(): ?\DateTime {
    return $this->date;
  }

  public function setSequencingAdvice(?string $sequencingAdvice): self {
    $this->sequencingAdvice = $sequencingAdvice;

    return $this;
  }

  public function getSequencingAdvice(): ?string {
    return $this->sequencingAdvice;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
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

  public function setSampling(Sampling $sampling): self {
    $this->sampling = $sampling;

    return $this;
  }

  public function getSampling(): Sampling {
    return $this->sampling;
  }

  public function setStore(?Store $store): self {
    $this->store = $store;

    return $this;
  }

  public function getStore(): ?Store {
    return $this->store;
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
    $taxonId->setInternalLot($this);
    $this->taxonIdentifications[] = $taxonId;

    return $this;
  }

  public function removeTaxonIdentification(TaxonIdentification $taxonId): self {
    $taxonId->setInternalLot(null);
    $this->taxonIdentifications->removeElement($taxonId);

    return $this;
  }

  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }

  public function addContent(InternalLotContent $content): self {
    $content->setInternalLot($this);
    $this->contents[] = $content;

    return $this;
  }

  public function removeContent(InternalLotContent $content): self {
    $this->contents->removeElement($content);

    return $this;
  }

  public function getContents(): Collection {
    return $this->contents;
  }

  public function setStatus(bool $status): self {
    $this->status = $status;

    return $this;
  }

  public function getStatus(): bool {
    return $this->status;
  }
}
