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
   * @var Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="samplingParticipants")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $collecteFk;

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
   * Set collecteFk
   *
   * @param Collecte $collecteFk
   * @return SamplingParticipant
   */
  public function setCollecteFk(Collecte $collecteFk = null) {
    $this->collecteFk = $collecteFk;

    return $this;
  }

  /**
   * Get collecteFk
   *
   * @return Collecte
   */
  public function getCollecteFk() {
    return $this->collecteFk;
  }
}
