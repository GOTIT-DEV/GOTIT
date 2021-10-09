<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A storage container
 */
#[ORM\Entity]
#[ORM\Table(name: 'storage_box')]
#[ORM\UniqueConstraint(name: 'uk_storage_box__box_code', columns: ['box_code'])]
#[ORM\Index(name: 'IDX_7718EDEF9E7B0E1F', columns: ['collection_type_voc_fk'])]
#[ORM\Index(name: 'IDX_7718EDEF41A72D48', columns: ['collection_code_voc_fk'])]
#[ORM\Index(name: 'IDX_7718EDEF57552D30', columns: ['box_type_voc_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
#[ApiResource]
class Store extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: ['item'])]
  private int $id;

  #[ORM\Column(name: 'box_code', type: 'string', length: 255, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $code;

  #[ORM\Column(name: 'box_title', type: 'string', length: 1024, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $label;

  #[ORM\Column(name: 'box_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'collection_type_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $collectionType;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'collection_code_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $collectionCode;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'box_type_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $storageType;

  #[ORM\OneToMany(targetEntity: 'InternalLot', mappedBy: 'store', cascade: ['persist'])]
  #[ORM\OrderBy(value: ['code' => 'ASC'])]
  #[Groups(groups: ['store_list', 'store_details'])]
  private array|Collection|ArrayCollection $internalLots;

  #[ORM\OneToMany(targetEntity: 'Dna', mappedBy: 'store', cascade: ['persist'])]
  #[ORM\OrderBy(value: ['code' => 'ASC'])]
  #[Groups(groups: ['store_list', 'store_details'])]
  private array|Collection|ArrayCollection $dnas;

  #[ORM\OneToMany(targetEntity: 'Slide', mappedBy: 'store', cascade: ['persist'])]
  #[ORM\OrderBy(value: ['code' => 'ASC'])]
  #[Groups(groups: ['store_list', 'store_details'])]
  private array|Collection|ArrayCollection $slides;

  public function __construct() {
    $this->internalLots = new ArrayCollection();
    $this->dnas = new ArrayCollection();
    $this->slides = new ArrayCollection();
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

  public function setComment(?string $comment): self {
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

  public function setCollectionType(Voc $collectionType): self {
    $this->collectionType = $collectionType;

    return $this;
  }

  public function getCollectionType(): Voc {
    return $this->collectionType;
  }

  public function setCollectionCode(Voc $collectionCode): self {
    $this->collectionCode = $collectionCode;

    return $this;
  }

  public function getCollectionCode(): Voc {
    return $this->collectionCode;
  }

  public function setStorageType(Voc $storageType): self {
    $this->storageType = $storageType;

    return $this;
  }

  public function getStorageType(): Voc {
    return $this->storageType;
  }

  public function addInternalLot(internalLot $internalLot): self {
    $internalLot->setStore($this);
    $this->internalLots[] = $internalLot;

    return $this;
  }

  public function getInternalLots(): Collection {
    return $this->internalLots;
  }

  public function addDna(Dna $dna): self {
    $dna->setStore($this);
    $this->dnas[] = $dna;

    return $this;
  }

  public function getDnas(): Collection {
    return $this->dnas;
  }

  public function addSlide(Slide $slide): self {
    $slide->setStore($this);
    $this->slides[] = $slide;

    return $this;
  }

  public function getSlides(): Collection {
    return $this->slides;
  }
}
