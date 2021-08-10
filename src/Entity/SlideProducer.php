<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlideProducer
 *
 * @ORM\Table(name="slide_is_mounted_by",
 *  indexes={
 *      @ORM\Index(name="IDX_88295540D9C85992", columns={"specimen_slide_fk"}),
 *      @ORM\Index(name="IDX_88295540B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SlideProducer extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="slide_is_mounted_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Slide
   *
   * @ORM\ManyToOne(targetEntity="Slide", inversedBy="producers")
   * @ORM\JoinColumn(name="specimen_slide_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $slideFk;

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
   * Set slideFk
   *
   * @param \App\Entity\Slide $slideFk
   *
   * @return SlideProducer
   */
  public function setSlideFk(\App\Entity\Slide $slideFk = null) {
    $this->slideFk = $slideFk;

    return $this;
  }

  /**
   * Get slideFk
   *
   * @return \App\Entity\Slide
   */
  public function getSlideFk() {
    return $this->slideFk;
  }

  /**
   * Set personFk
   *
   * @param \App\Entity\Person $personFk
   *
   * @return SlideProducer
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
