<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SourceAEteIntegrePar
 *
 * @ORM\Table(name="source_is_entered_by",
 *  indexes={
 *      @ORM\Index(name="IDX_16DC6005821B1D3F", columns={"source_fk"}),
 *      @ORM\Index(name="IDX_16DC6005B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SourceAEteIntegrePar extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="source_is_entered_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Source
   *
   * @ORM\ManyToOne(targetEntity="Source", inversedBy="sourceAEteIntegrePars")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sourceFk;

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
   * Set sourceFk
   *
   * @param \App\Entity\Source $sourceFk
   *
   * @return SourceAEteIntegrePar
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

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return SourceAEteIntegrePar
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
