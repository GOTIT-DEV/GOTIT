<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * InternalLotContent
 *
 * @ORM\Table(name="composition_of_internal_biological_material",
 *  indexes={
 *      @ORM\Index(name="IDX_10A697444236D33E", columns={"specimen_type_voc_fk"}),
 *      @ORM\Index(name="IDX_10A6974454DBBD4D", columns={"internal_biological_material_fk"})})
 * @ORM\Entity
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLotContent extends AbstractTimestampedEntity {
  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="composition_of_internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="number_of_specimens", type="int", nullable=true)
   */
  private ?int $specimenCount;

  /**
   * @ORM\Column(name="internal_biological_material_composition_comments", type="text", nullable=true)
   */
  private ?string $comment;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $specimenType;

  /**
   * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="contents")
   * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private InternalLot $internalLot;

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set specimenCount
   */
  public function setSpecimenCount(?int $specimenCount): self {
    $this->specimenCount = $specimenCount;

    return $this;
  }

  /**
   * Get specimenCount
   */
  public function getSpecimenCount(): ?int {
    return $this->specimenCount;
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
   * Set specimenType
   */
  public function setSpecimenType(Voc $specimenType): self {
    $this->specimenType = $specimenType;

    return $this;
  }

  /**
   * Get specimenType
   */
  public function getSpecimenType(): Voc {
    return $this->specimenType;
  }

  /**
   * Set internalLot
   */
  public function setInternalLot(InternalLot $internalLot): self {
    $this->internalLot = $internalLot;

    return $this;
  }

  /**
   * Get internalLot
   */
  public function getInternalLot(): InternalLot {
    return $this->internalLot;
  }
}
