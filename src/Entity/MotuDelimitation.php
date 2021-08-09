<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MotuDelimitation
 *
 * @ORM\Table(name="motu_number",
 *  indexes={
 *      @ORM\Index(name="IDX_4E79CB8DCDD1F756", columns={"external_sequence_fk"}),
 *      @ORM\Index(name="IDX_4E79CB8D40E7E0B3", columns={"delimitation_method_voc_fk"}),
 *      @ORM\Index(name="IDX_4E79CB8D5BE90E48", columns={"internal_sequence_fk"}),
 *      @ORM\Index(name="IDX_4E79CB8D503B4409", columns={"motu_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDelimitation extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="motu_number_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var integer
   *
   * @ORM\Column(name="motu_number", type="bigint", nullable=false)
   */
  private $numMotu;

  /**
   * @var \ExternalSequence
   *
   * @ORM\ManyToOne(targetEntity="ExternalSequence")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $externalSequenceFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="delimitation_method_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $methodeMotuVocFk;

  /**
   * @var \SequenceAssemblee
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssemblee")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $sequenceAssembleeFk;

  /**
   * @var \Motu
   *
   * @ORM\ManyToOne(targetEntity="Motu")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="motu_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $motuFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set numMotu
   *
   * @param integer $numMotu
   *
   * @return MotuDelimitation
   */
  public function setNumMotu($numMotu) {
    $this->numMotu = $numMotu;

    return $this;
  }

  /**
   * Get numMotu
   *
   * @return integer
   */
  public function getNumMotu() {
    return $this->numMotu;
  }

  /**
   * Set externalSequenceFk
   *
   * @param \App\Entity\ExternalSequence $externalSequenceFk
   *
   * @return MotuDelimitation
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

  /**
   * Set methodeMotuVocFk
   *
   * @param \App\Entity\Voc $methodeMotuVocFk
   *
   * @return MotuDelimitation
   */
  public function setMethodeMotuVocFk(\App\Entity\Voc $methodeMotuVocFk = null) {
    $this->methodeMotuVocFk = $methodeMotuVocFk;

    return $this;
  }

  /**
   * Get methodeMotuVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getMethodeMotuVocFk() {
    return $this->methodeMotuVocFk;
  }

  /**
   * Set sequenceAssembleeFk
   *
   * @param \App\Entity\SequenceAssemblee $sequenceAssembleeFk
   *
   * @return MotuDelimitation
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

  /**
   * Set motuFk
   *
   * @param \App\Entity\Motu $motuFk
   *
   * @return MotuDelimitation
   */
  public function setMotuFk(\App\Entity\Motu $motuFk = null) {
    $this->motuFk = $motuFk;

    return $this;
  }

  /**
   * Get motuFk
   *
   * @return \App\Entity\Motu
   */
  public function getMotuFk() {
    return $this->motuFk;
  }
}
