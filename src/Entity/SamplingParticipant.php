<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SamplingParticipant
 *
 * @ORM\Table(name="sampling_is_performed_by",
 *  indexes={
 *      @ORM\Index(name="IDX_EE2A88C9B53CD04C", columns={"person_fk"}),
 *      @ORM\Index(name="IDX_EE2A88C9662D9B98", columns={"sampling_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SamplingParticipant extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="sampling_is_performed_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var Personne
   *
   * @ORM\ManyToOne(targetEntity="Personne")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personneFk;

  /**
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling", inversedBy="samplingParticipants")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $samplingFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set personneFk
   *
   * @param Personne $personneFk
   * @return SamplingParticipant
   */
  public function setPersonneFk(Personne $personneFk = null) {
    $this->personneFk = $personneFk;

    return $this;
  }

  /**
   * Get personneFk
   *
   * @return Personne
   */
  public function getPersonneFk() {
    return $this->personneFk;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   * @return SamplingParticipant
   */
  public function setSamplingFk(Sampling $samplingFk = null) {
    $this->samplingFk = $samplingFk;

    return $this;
  }

  /**
   * Get samplingFk
   *
   * @return Sampling
   */
  public function getSamplingFk() {
    return $this->samplingFk;
  }
}
