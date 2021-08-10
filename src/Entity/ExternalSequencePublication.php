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
   * @ORM\JoinColumn(name="source_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $sourceFk;

  /**
   * @var \ExternalSequence
   *
   * @ORM\ManyToOne(targetEntity="ExternalSequence", inversedBy="externalSequencePublications")
   * @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $externalSequenceFk;

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
   * Set externalSequenceFk
   *
   * @param \App\Entity\ExternalSequence $externalSequenceFk
   *
   * @return ExternalSequencePublication
   */
  public function setExternalSequenceFk(\App\Entity\ExternalSequence $externalSequenceFk = null) {
    $this->externalSequenceFk = $externalSequenceFk;

    return $this;
  }

  /**
   * Get externalSequenceFk
   *
   * @return \App\Entity\ExternalSequence
   */
  public function getExternalSequenceFk() {
    return $this->externalSequenceFk;
  }
}
