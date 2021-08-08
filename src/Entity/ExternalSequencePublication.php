<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalSequencePublication
 *
 * @ORM\Table(name="external_sequence_is_published_in",
 *  indexes={
 *      @ORM\Index(name="IDX_8D0E8D6A821B1D3F", columns={"source_fk"}),
 *      @ORM\Index(name="IDX_8D0E8D6ACDD1F756", columns={"external_sequence_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalSequencePublication extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_sequence_is_published_in_id_seq", allocationSize=1, initialValue=1)
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
   * @var \SequenceAssembleeExt
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssembleeExt", inversedBy="externalSequencePublications")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sequenceAssembleeExtFk;

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
   * @return ExternalSequencePublication
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
   * Set sequenceAssembleeExtFk
   *
   * @param \App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk
   *
   * @return ExternalSequencePublication
   */
  public function setSequenceAssembleeExtFk(\App\Entity\SequenceAssembleeExt $sequenceAssembleeExtFk = null) {
    $this->sequenceAssembleeExtFk = $sequenceAssembleeExtFk;

    return $this;
  }

  /**
   * Get sequenceAssembleeExtFk
   *
   * @return \App\Entity\SequenceAssembleeExt
   */
  public function getSequenceAssembleeExtFk() {
    return $this->sequenceAssembleeExtFk;
  }
}
