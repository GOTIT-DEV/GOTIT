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
   * @var \Motu
   *
   * @ORM\ManyToOne(targetEntity="Motu", inversedBy="motuDelimiters")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="motu_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $motuFk;

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
   * Set motuFk
   *
   * @param \App\Entity\Motu $motuFk
   *
   * @return MotuDelimiter
   */
  public function setMotuFk(\App\Entity\Motu $motuFk = null) {
    $this->motuFk = $motuFk;

    return $this;
  }

  /**
   * Get motuFk
   *
   * @return \App\Entity\Motu
   */
  public function getMotuFk() {
    return $this->motuFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return MotuDelimiter
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
