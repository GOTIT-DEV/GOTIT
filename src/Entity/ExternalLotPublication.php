<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalLotPublication
 *
 * @ORM\Table(name="external_biological_material_is_published_in",
 *  indexes={
 *      @ORM\Index(name="IDX_D2338BB240D80ECD", columns={"external_biological_material_fk"}),
 *      @ORM\Index(name="IDX_D2338BB2821B1D3F", columns={"source_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalLotPublication extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_biological_material_is_published_in_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \LotMaterielExt
   *
   * @ORM\ManyToOne(targetEntity="LotMaterielExt", inversedBy="publications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $lotMaterielExtFk;

  /**
   * @var \Source
   *
   * @ORM\ManyToOne(targetEntity="Source")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
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
   * Set lotMaterielExtFk
   *
   * @param \App\Entity\LotMaterielExt $lotMaterielExtFk
   *
   * @return ExternalLotPublication
   */
  public function setLotMaterielExtFk(\App\Entity\LotMaterielExt $lotMaterielExtFk = null) {
    $this->lotMaterielExtFk = $lotMaterielExtFk;

    return $this;
  }

  /**
   * Get lotMaterielExtFk
   *
   * @return \App\Entity\LotMaterielExt
   */
  public function getLotMaterielExtFk() {
    return $this->lotMaterielExtFk;
  }

  /**
   * Set sourceFk
   *
   * @param \App\Entity\Source $sourceFk
   *
   * @return ExternalLotPublication
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
