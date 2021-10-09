<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * The content of a lot of biological material
 */
#[ORM\Entity]
#[ORM\Table(name: 'composition_of_internal_biological_material')]
#[ORM\Index(name: 'IDX_10A697444236D33E', columns: ['specimen_type_voc_fk'])]
#[ORM\Index(name: 'IDX_10A6974454DBBD4D', columns: ['internal_biological_material_fk'])]
class InternalLotContent extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  private int $id;

  #[ORM\Column(name: 'number_of_specimens', type: 'integer', nullable: true)]
  private ?int $specimenCount = null;

  #[ORM\Column(name: 'internal_biological_material_composition_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'specimen_type_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $specimenType;

  #[ORM\ManyToOne(targetEntity: 'InternalLot', inversedBy: 'contents')]
  #[ORM\JoinColumn(name: 'internal_biological_material_fk', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
  private InternalLot $internalLot;

  public function getId(): int {
    return $this->id;
  }

  public function setSpecimenCount(?int $specimenCount): self {
    $this->specimenCount = $specimenCount;

    return $this;
  }

  public function getSpecimenCount(): ?int {
    return $this->specimenCount;
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
}
