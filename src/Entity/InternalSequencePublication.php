<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InternalSequencePublication
 *
 * @ORM\Table(name="internal_sequence_is_published_in",
 *  indexes={
 *      @ORM\Index(name="IDX_BA97B9C4821B1D3F", columns={"source_fk"}),
 *      @ORM\Index(name="IDX_BA97B9C45BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequencePublication extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_sequence_is_published_in_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var \Source
   *
   * @ORM\ManyToOne(targetEntity="Source")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sourceFk;

  /**
   * @var \SequenceAssemblee
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="publications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sequenceAssembleeFk;

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
   * @return InternalSequencePublication
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
   * Set sequenceAssembleeFk
   *
   * @param \App\Entity\SequenceAssemblee $sequenceAssembleeFk
   *
   * @return InternalSequencePublication
   */
  public function setSequenceAssembleeFk(\App\Entity\SequenceAssemblee $sequenceAssembleeFk = null) {
    $this->sequenceAssembleeFk = $sequenceAssembleeFk;

    return $this;
  }

  /**
   * Get sequenceAssembleeFk
   *
   * @return \App\Entity\SequenceAssemblee
   */
  public function getSequenceAssembleeFk() {
    return $this->sequenceAssembleeFk;
  }
}
