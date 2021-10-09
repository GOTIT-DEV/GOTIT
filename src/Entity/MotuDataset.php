<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A dataset of MOTU delimitations
 */
#[ORM\Entity]
#[ORM\Table(name: 'motu')]
class MotuDataset extends AbstractTimestampedEntity {
  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'motu_is_generated_by')]
  #[ORM\JoinColumn(name: 'motu_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  protected Collection $motuDelimiters;

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: 'motu_dataset')]
  private int $id;

  #[ORM\Column(name: 'motu_title', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(groups: 'motu_dataset')]
  private string $title;

  #[ORM\Column(name: 'csv_file_name', type: 'string', length: 1024, nullable: false)]
  #[Groups(groups: 'motu_dataset')]
  private string $filename;

  #[ORM\Column(name: 'motu_date', type: 'date', nullable: false)]
  #[Groups(groups: 'motu_dataset')]
  private \DateTime $date;

  #[ORM\Column(name: 'motu_comments', type: 'text', nullable: true)]
  #[Groups(groups: 'motu_dataset')]
  private ?string $comment = null;

  public function __construct() {
    $this->motuDelimiters = new ArrayCollection();
  }

  public function getId(): int {
    return $this->id;
  }

  public function setFilename(string $filename): self {
    $this->filename = $filename;

    return $this;
  }

  public function getFilename(): string {
    return $this->filename;
  }

  public function setDate(\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDate(): \DateTime {
    return $this->date;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function addMotuDelimiter(Person $motuDelimiter): self {
    $this->motuDelimiters[] = $motuDelimiter;

    return $this;
  }

  public function removeMotuDelimiter(Person $motuDelimiter): self {
    $this->motuDelimiters->removeElement($motuDelimiter);

    return $this;
  }

  public function getMotuDelimiters(): Collection {
    return $this->motuDelimiters;
  }

  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

  public function getTitle(): string {
    return $this->title;
  }
}
