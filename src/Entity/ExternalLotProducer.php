<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalLotProducer
 *
 * @ORM\Table(name="external_biological_material_is_processed_by",
 *  indexes={
 *      @ORM\Index(name="IDX_7D78636FB53CD04C", columns={"person_fk"}),
 *      @ORM\Index(name="IDX_7D78636F40D80ECD", columns={"external_biological_material_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalLotProducer extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_biological_material_is_processed_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Person
   *
   * @ORM\ManyToOne(targetEntity="Person")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personFk;

  /**
   * @var \ExternalLot
   *
   * @ORM\ManyToOne(targetEntity="ExternalLot", inversedBy="producers")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $externalLotFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return ExternalLotProducer
   */
  public function setPersonFk(\App\Entity\Person $personFk = null) {
    $this->personFk = $personFk;

    return $this;
  }

  /**
   * Get personFk
   *
   * @return \App\Entity\Person
   */
  public function getPersonFk() {
    return $this->personFk;
  }

  /**
   * Set externalLotFk
   *
   * @param \App\Entity\ExternalLot $externalLotFk
   *
   * @return ExternalLotProducer
   */
  public function setExternalLotFk(\App\Entity\ExternalLot $externalLotFk = null) {
    $this->externalLotFk = $externalLotFk;

    return $this;
  }

  /**
   * Get externalLotFk
   *
   * @return \App\Entity\ExternalLot
   */
  public function getExternalLotFk() {
    return $this->externalLotFk;
  }
}
