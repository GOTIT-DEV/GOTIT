<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\AbstractTimestampedEntity;

/**
 * SamplingFixative
 *
 * @ORM\Table(name="sample_is_fixed_with",
 *  indexes={
 *      @ORM\Index(name="IDX_60129A315FD841AC", columns={"fixative_voc_fk"}),
 *      @ORM\Index(name="IDX_60129A31662D9B98", columns={"sampling_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SamplingFixative extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="sample_is_fixed_with_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="fixative_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $fixateurVocFk;

  /**
   * @var Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte", inversedBy="samplingFixatives")
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
   * Set fixateurVocFk
   *
   * @param Voc $fixateurVocFk
   *
   * @return SamplingFixative
   */
  public function setFixateurVocFk(Voc $fixateurVocFk = null) {
    $this->fixateurVocFk = $fixateurVocFk;

    return $this;
  }

  /**
   * Get fixateurVocFk
   *
   * @return Voc
   */
  public function getFixateurVocFk() {
    return $this->fixateurVocFk;
  }

  /**
   * Set collecteFk
   *
   * @param Collecte $collecteFk
   *
   * @return SamplingFixative
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
