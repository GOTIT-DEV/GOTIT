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
   * @var Program
   *
   * @ORM\ManyToOne(targetEntity="Program")
   * @ORM\JoinColumn(name="program_fk", referencedColumnName="id", nullable=false)
   */
  private $programFk;

  /**
   * @var Sampling
   *
   * @ORM\ManyToOne(targetEntity="Sampling", inversedBy="samplingFundings")
   * @ORM\JoinColumn(
   *    name="sampling_fk",
   *    referencedColumnName="id",
   *    nullable=false,
   *    onDelete="CASCADE")
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
   * Set programFk
   *
   * @param Program $programFk
   * @return SamplingFunding
   */
  public function setProgramFk(Program $programFk = null) {
    $this->programFk = $programFk;
    return $this;
  }

  /**
   * Get programFk
   *
   * @return Program
   */
  public function getProgramFk() {
    return $this->programFk;
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
