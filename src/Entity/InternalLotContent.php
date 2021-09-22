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
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLotContent extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="composition_of_internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="number_of_specimens", type="bigint", nullable=true)
   */
  private $specimenCount;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_composition_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $specimenType;

  /**
   * @var \InternalLot
   *
   * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="contents")
   * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $internalLot;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set specimenCount
   *
   * @param integer $specimenCount
   *
   * @return InternalLotContent
   */
  public function setSpecimenCount($specimenCount) {
    $this->specimenCount = $specimenCount;

    return $this;
  }

  /**
   * Get specimenCount
   *
   * @return integer
   */
  public function getSpecimenCount() {
    return $this->specimenCount;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return InternalLotContent
   */
  public function setComment($comment) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Set specimenType
   *
   * @param \App\Entity\Voc $specimenType
   *
   * @return InternalLotContent
   */
  public function setSpecimenType(\App\Entity\Voc $specimenType = null) {
    $this->specimenType = $specimenType;

    return $this;
  }

  /**
   * Get specimenType
   *
   * @return \App\Entity\Voc
   */
  public function getSpecimenType() {
    return $this->specimenType;
  }

  /**
   * Set internalLot
   *
   * @param \App\Entity\InternalLot $internalLot
   *
   * @return InternalLotContent
   */
  public function setInternalLot(\App\Entity\InternalLot $internalLot = null) {
    $this->internalLot = $internalLot;

    return $this;
  }

  /**
   * Get internalLot
   *
   * @return \App\Entity\InternalLot
   */
  public function getInternalLot() {
    return $this->internalLot;
  }
}
