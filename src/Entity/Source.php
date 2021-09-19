<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Source
 *
 * @ORM\Table(name="source",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_source__source_code", columns={"source_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Source extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="source_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="source_code", type="string", length=255, nullable=false, unique=true)
   */
  private $code;

  /**
   * @var integer
   *
   * @ORM\Column(name="source_year", type="bigint", nullable=true)
   */
  private $year;

  /**
   * @var string
   *
   * @ORM\Column(name="source_title", type="string", length=2048, nullable=false)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="source_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @ORM\OneToMany(targetEntity="SourceProvider", mappedBy="sourceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sourceProviders;

  public function __construct() {
    $this->sourceProviders = new ArrayCollection();
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
   * Set code
   *
   * @param string $code
   *
   * @return Source
   */
  public function setCode($code) {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Set year
   *
   * @param integer $year
   *
   * @return Source
   */
  public function setYear($year) {
    $this->year = $year;

    return $this;
  }

  /**
   * Get year
   *
   * @return integer
   */
  public function getYear() {
    return $this->year;
  }

  /**
   * Set title
   *
   * @param string $title
   *
   * @return Source
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

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Source
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
   * Add sourceProvider
   *
   * @param \App\Entity\SourceProvider $sourceProvider
   *
   * @return Source
   */
  public function addSourceProvider(\App\Entity\SourceProvider $sourceProvider) {
    $sourceProvider->setSourceFk($this);
    $this->sourceProviders[] = $sourceProvider;

    return $this;
  }

  /**
   * Remove sourceProvider
   *
   * @param \App\Entity\SourceProvider $sourceProvider
   */
  public function removeSourceProvider(\App\Entity\SourceProvider $sourceProvider) {
    $this->sourceProviders->removeElement($sourceProvider);
  }

  /**
   * Get sourceProviders
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSourceProviders() {
    return $this->sourceProviders;
  }
}
