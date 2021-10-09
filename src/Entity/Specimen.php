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
 * A specimen of a taxon
 */
// ORM
#[ORM\Entity]
#[ORM\Table(name: 'specimen')]
#[ORM\UniqueConstraint(
  name: 'uk_specimen__specimen_morphological_code',
  columns: ['specimen_morphological_code']
)]
#[ORM\UniqueConstraint(
  name: 'uk_specimen__specimen_molecular_code',
  columns: ['specimen_molecular_code']
)]
#[ORM\Index(name: 'IDX_5EE42FCE4236D33E', columns: ['specimen_type_voc_fk'])]
#[ORM\Index(name: 'IDX_5EE42FCE54DBBD4D', columns: ['internal_biological_material_fk'])]
// Validation
#[UniqueEntity(fields: ['molecularCode'], message: 'This code is already registered')]
#[UniqueEntity(fields: ['morphologicalCode'], message: 'This code is already registered')]
// API
#[ApiResource]
class Specimen extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: ['item'])]
  private int $id;

  #[ORM\Column(name: 'specimen_morphological_code', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(groups: ['item'])]
  private string $morphologicalCode;

  #[ORM\Column(name: 'specimen_molecular_code', type: 'string', length: 255, nullable: true, unique: true)]
  #[Groups(groups: ['item'])]
  private ?string $molecularCode = null;

  #[ORM\Column(name: 'tube_code', type: 'string', length: 255, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $tubeCode;

  #[ORM\Column(name: 'specimen_molecular_number', type: 'string', length: 255, nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $molecularNumber = null;

  #[ORM\Column(name: 'specimen_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'specimen_type_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $specimenType;

  #[ORM\ManyToOne(targetEntity: 'InternalLot')]
  #[ORM\JoinColumn(name: 'internal_biological_material_fk', referencedColumnName: 'id', nullable: false)]
  #[Groups(groups: ['specimen:list', 'specimen:item'])]
  private InternalLot $internalLot;

  #[ORM\OneToMany(targetEntity: 'TaxonIdentification', mappedBy: 'specimen', cascade: ['persist'])]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  #[Groups(groups: ['specimen:list', 'specimen:item'])]
  private array|Collection|ArrayCollection $taxonIdentifications;

  public function __construct() {
    $this->taxonIdentifications = new ArrayCollection();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setMolecularCode(?string $molecularCode): self {
    $this->molecularCode = $molecularCode;

    return $this;
  }

  public function getMolecularCode(): ?string {
    return $this->molecularCode;
  }

  public function setMorphologicalCode(string $morphologicalCode): self {
    $this->morphologicalCode = $morphologicalCode;

    return $this;
  }

  public function getMorphologicalCode(): string {
    return $this->morphologicalCode;
  }

  public function setTubeCode(string $tubeCode): self {
    $this->tubeCode = $tubeCode;

    return $this;
  }

  public function getTubeCode(): string {
    return $this->tubeCode;
  }

  public function setMolecularNumber(?string $molecularNumber): self {
    $this->molecularNumber = $molecularNumber;

    return $this;
  }

  public function getMolecularNumber(): ?string {
    return $this->molecularNumber;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setSpecimenType(Voc $specimenType): self {
    $this->specimenType = $specimenType;

    return $this;
  }

  public function getSpecimenType(): Voc {
    return $this->specimenType;
  }

  public function setInternalLot(InternalLot $internalLot): self {
    $this->internalLot = $internalLot;

    return $this;
  }

  public function getInternalLot(): InternalLot {
    return $this->internalLot;
  }

  public function addTaxonIdentification(TaxonIdentification $taxonIdentification): self {
    $taxonIdentification->setSpecimen($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  public function removeTaxonIdentification(TaxonIdentification $taxonIdentification): self {
    $this->taxonIdentifications->removeElement($taxonIdentification);

    return $this;
  }

  public function getTaxonIdentifications(): Collection {
    return $this->taxonIdentifications;
  }
}
