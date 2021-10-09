<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * An observation slide for a specimen
 */
#[ORM\Entity]
#[ORM\Table(name: 'specimen_slide')]
#[ORM\UniqueConstraint(
  name: 'uk_specimen_slide__collection_slide_code',
  columns: ['collection_slide_code']
)]
#[ORM\Index(name: 'IDX_8DA827E2A30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_8DA827E22B644673', columns: ['storage_box_fk'])]
#[ORM\Index(name: 'IDX_8DA827E25F2C6176', columns: ['specimen_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Slide extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'collection_slide_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'slide_title', type: 'string', length: 1024, nullable: false)]
  private string $label;

  #[ORM\Column(name: 'slide_date', type: 'date', nullable: true)]
  private ?\DateTime $date = null;

  #[ORM\Column(name: 'photo_folder_name', type: 'string', length: 1024, nullable: true)]
  private ?string $pictureFolder = null;

  #[ORM\Column(name: 'slide_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Store', inversedBy: 'slides')]
  #[ORM\JoinColumn(name: 'storage_box_fk', referencedColumnName: 'id', nullable: true)]
  private ?Store $store = null;

  #[ORM\ManyToOne(targetEntity: 'Specimen')]
  #[ORM\JoinColumn(name: 'specimen_fk', referencedColumnName: 'id', nullable: false)]
  private Specimen $specimen;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'slide_is_mounted_by')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\JoinColumn(name: 'specimen_slide_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $producers;

  public function __construct() {
    $this->producers = new ArrayCollection();
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

  public function setLabel(string $label): self {
    $this->label = $label;

    return $this;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function setDate(?\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDate(): ?\DateTime {
    return $this->date;
  }

  public function setPictureFolder(?string $pictureFolder): self {
    $this->pictureFolder = $pictureFolder;

    return $this;
  }

  public function getPictureFolder(): ?string {
    return $this->pictureFolder;
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

  public function setStore(?Store $store): self {
    $this->store = $store;

    return $this;
  }

  public function getStore(): ?Store {
    return $this->store;
  }

  public function setSpecimen(Specimen $specimen): self {
    $this->specimen = $specimen;

    return $this;
  }

  public function getSpecimen(): Specimen {
    return $this->specimen;
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
}
