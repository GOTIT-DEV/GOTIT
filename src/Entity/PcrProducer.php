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
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $pcrFk;

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
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return PcrProducer
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
