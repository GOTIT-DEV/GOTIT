<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTimestampedEntity;

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
   * @var Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="samplingFundings")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(
   *      name="sampling_fk",
   *      referencedColumnName="id",
   *      nullable=false,
   *      onDelete="CASCADE")
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
   * Set collecteFk
   *
   * @param Collecte $collecteFk
   * @return SamplingFunding
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
