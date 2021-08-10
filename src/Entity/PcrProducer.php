<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PcrProducer
 *
 * @ORM\Table(name="pcr_is_done_by",
 *  indexes={
 *      @ORM\Index(name="IDX_1041853B2B63D494", columns={"pcr_fk"}),
 *      @ORM\Index(name="IDX_1041853BB53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class PcrProducer extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="pcr_is_done_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Pcr
   *
   * @ORM\ManyToOne(targetEntity="Pcr", inversedBy="pcrProducers")
   * @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $pcrFk;

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
   * Set pcrFk
   *
   * @param \App\Entity\Pcr $pcrFk
   *
   * @return PcrProducer
   */
  public function setPcrFk(\App\Entity\Pcr $pcrFk = null) {
    $this->pcrFk = $pcrFk;

    return $this;
  }

  /**
   * Get pcrFk
   *
   * @return \App\Entity\Pcr
   */
  public function getPcrFk() {
    return $this->pcrFk;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return PcrProducer
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
