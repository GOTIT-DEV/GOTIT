<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * A MOTU delimitation from a curated dataset
 */
#[ORM\Entity]
#[ORM\Table(name: 'motu_number')]
#[ORM\Index(name: 'IDX_4E79CB8DCDD1F756', columns: ['external_sequence_fk'])]
#[ORM\Index(name: 'IDX_4E79CB8D40E7E0B3', columns: ['delimitation_method_voc_fk'])]
#[ORM\Index(name: 'IDX_4E79CB8D5BE90E48', columns: ['internal_sequence_fk'])]
#[ORM\Index(name: 'IDX_4E79CB8D503B4409', columns: ['motu_fk'])]
class MotuDelimitation extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'motu_number', type: 'integer', nullable: false)]
  private int $motuNumber;

  #[ORM\ManyToOne(targetEntity: 'ExternalSequence')]
  #[ORM\JoinColumn(name: 'external_sequence_fk', referencedColumnName: 'id', nullable: true)]
  private ?ExternalSequence $externalSequence = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'delimitation_method_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $method;

  #[ORM\ManyToOne(targetEntity: 'InternalSequence')]
  #[ORM\JoinColumn(name: 'internal_sequence_fk', referencedColumnName: 'id', nullable: true)]
  private ?InternalSequence $internalSequence = null;

  #[ORM\ManyToOne(targetEntity: 'MotuDataset', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'motu_fk', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
  private MotuDataset $dataset;

  public function getId(): int {
    return $this->id;
  }

  public function setMotuNumber(int $motuNumber): self {
    $this->motuNumber = $motuNumber;

    return $this;
  }

  public function getMotuNumber(): int {
    return $this->motuNumber;
  }

  public function setExternalSequence(?ExternalSequence $externalSequence = null): self {
    $this->externalSequence = $externalSequence;

    return $this;
  }

  public function getExternalSequence(): ?ExternalSequence {
    return $this->externalSequence;
  }

  public function setMethod(Voc $method): self {
    $this->method = $method;

    return $this;
  }

  public function getMethod(): Voc {
    return $this->method;
  }

  public function setInternalSequence(?InternalSequence $internalSequence): self {
    $this->internalSequence = $internalSequence;

    return $this;
  }

  public function getInternalSequence(): ?InternalSequence {
    return $this->internalSequence;
  }

  public function setDataset(MotuDataset $dataset): self {
    $this->dataset = $dataset;

    return $this;
  }

  public function getDataset(): MotuDataset {
    return $this->dataset;
  }
}
