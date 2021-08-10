<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InternalLotPublication
 *
 * @ORM\Table(name="internal_biological_material_is_published_in",
 *  indexes={
 *      @ORM\Index(name="IDX_EA07BFA754DBBD4D", columns={"internal_biological_material_fk"}),
 *      @ORM\Index(name="IDX_EA07BFA7821B1D3F", columns={"source_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLotPublication extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_biological_material_is_published_in_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \InternalLot
   *
   * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="publications")
   * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $internalLotFk;

  /**
   * @var \Source
   *
   * @ORM\ManyToOne(targetEntity="Source")
   * @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $sourceFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set internalLotFk
   *
   * @param \App\Entity\InternalLot $internalLotFk
   *
   * @return InternalLotPublication
   */
  public function setInternalLotFk(\App\Entity\InternalLot $internalLotFk = null) {
    $this->internalLotFk = $internalLotFk;

    return $this;
  }

  /**
   * Get internalLotFk
   *
   * @return \App\Entity\InternalLot
   */
  public function getInternalLotFk() {
    return $this->internalLotFk;
  }

  /**
   * Set sourceFk
   *
   * @param \App\Entity\Source $sourceFk
   *
   * @return InternalLotPublication
   */
  public function setSourceFk(\App\Entity\Source $sourceFk = null) {
    $this->sourceFk = $sourceFk;

    return $this;
  }

  /**
   * Get sourceFk
   *
   * @return \App\Entity\Source
   */
  public function getSourceFk() {
    return $this->sourceFk;
  }
}
