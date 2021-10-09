<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * MotuDataset
 *
 * @ORM\Table(name="motu")
 * @ORM\Entity
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDataset extends AbstractTimestampedEntity {
  /**
   * @ORM\Id
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="motu_id_seq", allocationSize=1, initialValue=1)
   * @Groups("motu_dataset")
   */
  private int $id;

  /**
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_title", type="string", length=255, nullable=false, unique=true)
   */
  private string $title;

  /**
   * @Groups("motu_dataset")
   * @ORM\Column(name="csv_file_name", type="string", length=1024, nullable=false)
   */
  private string $filename;

  /**
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_date", type="date", nullable=false)
   */
  private \DateTime $date;

  /**
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_comments", type="text", nullable=true)
   */
  private ?string $comment;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="motu_is_generated_by",
   *  joinColumns={@ORM\JoinColumn(name="motu_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   */
  protected Collection $motuDelimiters;

  public function __construct() {
    $this->motuDelimiters = new ArrayCollection();
  }

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set filename
   *
   * @return MotuDataset
   */
  public function setFilename(string $filename): self {
    $this->filename = $filename;

    return $this;
  }

  /**
   * Get filename
   */
  public function getFilename(): string {
    return $this->filename;
  }

  /**
   * Set date
   */
  public function setDate(\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   */
  public function getDate(): \DateTime {
    return $this->date;
  }

  /**
   * Set comment
   */
  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Add motuDelimiter
   */
  public function addMotuDelimiter(Person $motuDelimiter): self {
    $this->motuDelimiters[] = $motuDelimiter;

    return $this;
  }

  /**
   * Remove motuDelimiter
   */
  public function removeMotuDelimiter(Person $motuDelimiter): self {
    $this->motuDelimiters->removeElement($motuDelimiter);

    return $this;
  }

  /**
   * Get motuDelimiters
   */
  public function getMotuDelimiters(): Collection {
    return $this->motuDelimiters;
  }

  /**
   * Set title
   */
  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   */
  public function getTitle(): string {
    return $this->title;
  }
}
