<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotuDelimiter
 *
 * @ORM\Table(name="motu_is_generated_by",
 *  indexes={
 *      @ORM\Index(name="IDX_17A90EA3503B4409", columns={"motu_fk"}),
 *      @ORM\Index(name="IDX_17A90EA3B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDelimiter extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="motu_is_generated_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \MotuDataset
   *
   * @ORM\ManyToOne(targetEntity="MotuDataset", inversedBy="motuDelimiters")
   * @ORM\JoinColumn(name="motu_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $motuDatasetFk;

  /**
   * @var \Person
   *
   * @ORM\ManyToOne(targetEntity="Person")
   * @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   */
  private $personFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set motuDatasetFk
   *
   * @param \App\Entity\MotuDataset $motuDatasetFk
   *
   * @return MotuDelimiter
   */
  public function setMotuDatasetFk(\App\Entity\MotuDataset $motuDatasetFk = null) {
    $this->motuDatasetFk = $motuDatasetFk;

    return $this;
  }

  /**
   * Get motuDatasetFk
   *
   * @return \App\Entity\MotuDataset
   */
  public function getMotuDatasetFk() {
    return $this->motuDatasetFk;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return MotuDelimiter
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
}
