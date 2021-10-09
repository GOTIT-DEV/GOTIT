<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
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
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDelimitation extends AbstractTimestampedEntity {
  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="motu_number_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="motu_number", type="integer", nullable=false)
   */
  private int $motuNumber;

  /**
   * @var ExternalSequence
   *
   * @ORM\ManyToOne(targetEntity="ExternalSequence")
   * @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true)
   */
  private $externalSequence;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="delimitation_method_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $method;

  /**
   * @var InternalSequence
   *
   * @ORM\ManyToOne(targetEntity="InternalSequence")
   * @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true)
   */
  private $internalSequence;

  /**
   * @var MotuDataset
   *
   * @ORM\ManyToOne(targetEntity="MotuDataset", fetch="EAGER")
   * @ORM\JoinColumn(name="motu_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   */
  private $motuDatasetFk;

  /**
   * Get id
   *
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set motuNumber
   *
   * @param int $motuNumber
   *
   * @return MotuDelimitation
   */
  public function setMotuNumber($motuNumber) {
    $this->motuNumber = $motuNumber;

    return $this;
  }

  /**
   * Get motuNumber
   *
   * @return int
   */
  public function getMotuNumber() {
    return $this->motuNumber;
  }

  /**
   * Set externalSequence
   *
   * @param ExternalSequence $externalSequence
   *
   * @return MotuDelimitation
   */
  public function setExternalSequence(
        ExternalSequence $externalSequence = null,
    ) {
    $this->externalSequence = $externalSequence;

    return $this;
  }

  /**
   * Get externalSequence
   *
   * @return ExternalSequence
   */
  public function getExternalSequence() {
    return $this->externalSequence;
  }

  /**
   * Set method
   *
   * @param Voc $method
   *
   * @return MotuDelimitation
   */
  public function setMethod(Voc $method = null) {
    $this->method = $method;

    return $this;
  }

  /**
   * Get method
   *
   * @return Voc
   */
  public function getMethod() {
    return $this->method;
  }

  /**
   * Set internalSequence
   *
   * @param InternalSequence $internalSequence
   *
   * @return MotuDelimitation
   */
  public function setInternalSequence(
        InternalSequence $internalSequence = null,
    ) {
    $this->internalSequence = $internalSequence;

    return $this;
  }

  /**
   * Get internalSequence
   *
   * @return InternalSequence
   */
  public function getInternalSequence() {
    return $this->internalSequence;
  }

  /**
   * Set motuDatasetFk
   *
   * @param MotuDataset $motuDatasetFk
   *
   * @return MotuDelimitation
   */
  public function setMotuDatasetFk(MotuDataset $motuDatasetFk = null) {
    $this->motuDatasetFk = $motuDatasetFk;

    return $this;
  }

  /**
   * Get motuDatasetFk
   *
   * @return MotuDataset
   */
  public function getMotuDatasetFk() {
    return $this->motuDatasetFk;
  }
}
