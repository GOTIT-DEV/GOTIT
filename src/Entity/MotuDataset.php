<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * MotuDataset
 *
 * @ORM\Table(name="motu")
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDataset extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="motu_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_title", type="string", length=255, nullable=false, unique=true)
   */
  private $title;

  /**
   * @var string
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="csv_file_name", type="string", length=1024, nullable=false)
   */
  private $filename;

  /**
   * @var \DateTime
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_date", type="date", nullable=false)
   */
  private $date;

  /**
   * @var string
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @ORM\OneToMany(targetEntity="MotuDelimiter", mappedBy="motuDatasetFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $motuDelimiters;

  public function __construct() {
    $this->motuDelimiters = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set filename
   *
   * @param string $filename
   *
   * @return MotuDataset
   */
  public function setFilename($filename) {
    $this->filename = $filename;

    return $this;
  }

  /**
   * Get filename
   *
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   *
   * @return MotuDataset
   */
  public function setDate($date) {
    $this->date = $date;

    return $this;
  }

  /**
   * Get date
   *
   * @return \DateTime
   */
  public function getDate() {
    return $this->date;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return MotuDataset
   */
  public function setComment($comment) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Add motuDelimiter
   *
   * @param \App\Entity\MotuDelimiter $motuDelimiter
   *
   * @return MotuDataset
   */
  public function addMotuDelimiter(\App\Entity\MotuDelimiter $motuDelimiter) {
    $motuDelimiter->setMotuDatasetFk($this);
    $this->motuDelimiters[] = $motuDelimiter;

    return $this;
  }

  /**
   * Remove motuDelimiter
   *
   * @param \App\Entity\MotuDelimiter $motuDelimiter
   */
  public function removeMotuDelimiter(\App\Entity\MotuDelimiter $motuDelimiter) {
    $this->motuDelimiters->removeElement($motuDelimiter);
  }

  /**
   * Get motuDelimiters
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMotuDelimiters() {
    return $this->motuDelimiters;
  }

  /**
   * Set title
   *
   * @param string $title
   *
   * @return MotuDataset
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }
}
