<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndividuLameEstRealisePar
 *
 * @ORM\Table(name="slide_is_mounted_by",
 *  indexes={
 *      @ORM\Index(name="IDX_88295540D9C85992", columns={"specimen_slide_fk"}),
 *      @ORM\Index(name="IDX_88295540B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class IndividuLameEstRealisePar extends AbstractTimestampedEntity {
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
   * @var \IndividuLame
   *
   * @ORM\ManyToOne(targetEntity="IndividuLame", inversedBy="individuLameEstRealisePars")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="specimen_slide_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $individuLameFk;

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
   * Set individuLameFk
   *
   * @param \App\Entity\IndividuLame $individuLameFk
   *
   * @return IndividuLameEstRealisePar
   */
  public function setIndividuLameFk(\App\Entity\IndividuLame $individuLameFk = null) {
    $this->individuLameFk = $individuLameFk;

    return $this;
  }

  /**
   * Get individuLameFk
   *
   * @return \App\Entity\IndividuLame
   */
  public function getIndividuLameFk() {
    return $this->individuLameFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return IndividuLameEstRealisePar
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
