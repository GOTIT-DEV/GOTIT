<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InternalLotProducer
 *
 * @ORM\Table(name="internal_biological_material_is_treated_by",
 *  indexes={
 *      @ORM\Index(name="IDX_69C58AFF54DBBD4D", columns={"internal_biological_material_fk"}),
 *      @ORM\Index(name="IDX_69C58AFFB53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLotProducer extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="Internal_biological_material_is_treated_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \LotMateriel
   *
   * @ORM\ManyToOne(targetEntity="LotMateriel", inversedBy="producers")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $lotMaterielFk;

  /**
   * @var \Personne
   *
   * @ORM\ManyToOne(targetEntity="Personne")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personneFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set lotMaterielFk
   *
   * @param \App\Entity\LotMateriel $lotMaterielFk
   *
   * @return InternalLotProducer
   */
  public function setLotMaterielFk(\App\Entity\LotMateriel $lotMaterielFk = null) {
    $this->lotMaterielFk = $lotMaterielFk;

    return $this;
  }

  /**
   * Get lotMaterielFk
   *
   * @return \App\Entity\LotMateriel
   */
  public function getLotMaterielFk() {
    return $this->lotMaterielFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return InternalLotProducer
   */
  public function setPersonneFk(\App\Entity\Personne $personneFk = null) {
    $this->personneFk = $personneFk;

    return $this;
  }

  /**
   * Get personneFk
   *
   * @return \App\Entity\Personne
   */
  public function getPersonneFk() {
    return $this->personneFk;
  }
}
