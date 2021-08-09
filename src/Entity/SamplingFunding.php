<?php

namespace App\Entity;

use App\Entity\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * SamplingFunding
 *
 * @ORM\Table(name="sampling_is_funded_by",
 *  indexes={
 *      @ORM\Index(name="IDX_18FCBB8F759C7BB0", columns={"program_fk"}),
 *      @ORM\Index(name="IDX_18FCBB8F662D9B98", columns={"sampling_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SamplingFunding extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="sampling_is_funded_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var Programme
   *
   * @ORM\ManyToOne(targetEntity="Programme")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="program_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $programmeFk;

  /**
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling", inversedBy="samplingFundings")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(
   *      name="sampling_fk",
   *      referencedColumnName="id",
   *      nullable=false,
   *      onDelete="CASCADE")
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
   * Set programmeFk
   *
   * @param Programme $programmeFk
   * @return SamplingFunding
   */
  public function setProgrammeFk(Programme $programmeFk = null) {
    $this->programmeFk = $programmeFk;
    return $this;
  }

  /**
   * Get programmeFk
   *
   * @return Programme
   */
  public function getProgrammeFk() {
    return $this->programmeFk;
  }

  /**
   * Set samplingFk
   *
   * @param Sampling $samplingFk
   * @return SamplingFunding
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
