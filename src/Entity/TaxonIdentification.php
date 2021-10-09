<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Identification of taxon based on different criteria
 */
#[ORM\Entity]
#[ORM\Table(name: 'identified_species')]
#[ORM\Index(name: 'IDX_801C3911B669F53D', columns: ['type_material_voc_fk'])]
#[ORM\Index(name: 'IDX_49D19C8DFB5F790', columns: ['identification_criterion_voc_fk'])]
#[ORM\Index(name: 'IDX_49D19C8DA30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_49D19C8DCDD1F756', columns: ['external_sequence_fk'])]
#[ORM\Index(name: 'IDX_49D19C8D40D80ECD', columns: ['external_biological_material_fk'])]
#[ORM\Index(name: 'IDX_49D19C8D54DBBD4D', columns: ['internal_biological_material_fk'])]
#[ORM\Index(name: 'IDX_49D19C8D7B09E3BC', columns: ['taxon_fk'])]
#[ORM\Index(name: 'IDX_49D19C8D5F2C6176', columns: ['specimen_fk'])]
#[ORM\Index(name: 'IDX_49D19C8D5BE90E48', columns: ['internal_sequence_fk'])]
class TaxonIdentification extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\ManyToOne(targetEntity: 'Taxon')]
  #[ORM\JoinColumn(name: 'taxon_fk', referencedColumnName: 'id', nullable: false)]
  private Taxon $taxon;

  #[ORM\Column(name: 'identification_date', type: 'date', nullable: true)]
  private ?\DateTime $identificationDate = null;

  #[ORM\Column(name: 'identified_species_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'type_material_voc_fk', referencedColumnName: 'id', nullable: true)]
  private ?Voc $materialType = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'identification_criterion_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $identificationCriterion;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'ExternalSequence', inversedBy: 'taxonIdentifications')]
  #[ORM\JoinColumn(name: 'external_sequence_fk', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
  private ?ExternalSequence $externalSequence = null;

  #[ORM\ManyToOne(targetEntity: 'ExternalLot', inversedBy: 'taxonIdentifications')]
  #[ORM\JoinColumn(name: 'external_biological_material_fk', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
  private ?ExternalLot $externalLot = null;

  #[ORM\ManyToOne(targetEntity: 'InternalLot', inversedBy: 'taxonIdentifications')]
  #[ORM\JoinColumn(name: 'internal_biological_material_fk', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
  private ?InternalLot $internalLot = null;

  #[ORM\ManyToOne(targetEntity: 'Specimen', inversedBy: 'taxonIdentifications')]
  #[ORM\JoinColumn(name: 'specimen_fk', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
  private ?Specimen $specimen = null;

  #[ORM\ManyToOne(targetEntity: 'InternalSequence', inversedBy: 'taxonIdentifications')]
  #[ORM\JoinColumn(name: 'internal_sequence_fk', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
  private ?InternalSequence $internalSequence = null;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'species_is_identified_by')]
  #[ORM\JoinColumn(name: 'identified_species_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $curators;

  public function __construct() {
    $this->curators = new ArrayCollection();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setIdentificationDate(?\DateTime $identificationDate): self {
    $this->identificationDate = $identificationDate;

    return $this;
  }

  public function getIdentificationDate(): \DateTime {
    return $this->identificationDate;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setIdentificationCriterion(Voc $identificationCriterion): self {
    $this->identificationCriterion = $identificationCriterion;

    return $this;
  }

  public function getIdentificationCriterion(): Voc {
    return $this->identificationCriterion;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  public function setExternalSequence(?ExternalSequence $externalSequence): self {
    $this->externalSequence = $externalSequence;

    return $this;
  }

  public function getExternalSequence(): ?ExternalSequence {
    return $this->externalSequence;
  }

  public function setExternalLot(?ExternalLot $externalLot): self {
    $this->externalLot = $externalLot;

    return $this;
  }

  public function getExternalLot(): ?ExternalLot {
    return $this->externalLot;
  }

  public function setInternalLot(?InternalLot $internalLot): self {
    $this->internalLot = $internalLot;

    return $this;
  }

  public function getInternalLot(): ?InternalLot {
    return $this->internalLot;
  }

  public function setTaxon(Taxon $taxon): self {
    $this->taxon = $taxon;

    return $this;
  }

  public function getTaxon(): Taxon {
    return $this->taxon;
  }

  public function setSpecimen(?Specimen $specimen): self {
    $this->specimen = $specimen;

    return $this;
  }

  public function getSpecimen(): ?Specimen {
    return $this->specimen;
  }

  public function setInternalSequence(?InternalSequence $internalSequence): self {
    $this->internalSequence = $internalSequence;

    return $this;
  }

  public function getInternalSequence(): ?InternalSequence {
    return $this->internalSequence;
  }

  public function addCurator(Person $curator): self {
    $this->curators[] = $curator;

    return $this;
  }

  public function removeCurator(Person $curator): self {
    $this->curators->removeElement($curator);

    return $this;
  }

  public function getCurators(): Collection {
    return $this->curators;
  }

  public function setMaterialType(?Voc $materialType): self {
    $this->materialType = $materialType;

    return $this;
  }

  public function getMaterialType(): ?Voc {
    return $this->materialType;
  }
}
