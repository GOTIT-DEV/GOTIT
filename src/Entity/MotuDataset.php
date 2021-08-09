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
   * @ORM\Column(name="motu_title", type="string", length=255, nullable=false)
   */
  private $libelleMotu;

  /**
   * @var string
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="csv_file_name", type="string", length=1024, nullable=false)
   */
  private $nomFichierCsv;

  /**
   * @var \DateTime
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_date", type="date", nullable=false)
   */
  private $dateMotu;

  /**
   * @var string
   *
   * @Groups("motu_dataset")
   * @ORM\Column(name="motu_comments", type="text", nullable=true)
   */
  private $commentaireMotu;

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
   * Set nomFichierCsv
   *
   * @param string $nomFichierCsv
   *
   * @return MotuDataset
   */
  public function setNomFichierCsv($nomFichierCsv) {
    $this->nomFichierCsv = $nomFichierCsv;

    return $this;
  }

  /**
   * Get nomFichierCsv
   *
   * @return string
   */
  public function getNomFichierCsv() {
    return $this->nomFichierCsv;
  }

  /**
   * Set dateMotu
   *
   * @param \DateTime $dateMotu
   *
   * @return MotuDataset
   */
  public function setDateMotu($dateMotu) {
    $this->dateMotu = $dateMotu;

    return $this;
  }

  /**
   * Get dateMotu
   *
   * @return \DateTime
   */
  public function getDateMotu() {
    return $this->dateMotu;
  }

  /**
   * Set commentaireMotu
   *
   * @param string $commentaireMotu
   *
   * @return MotuDataset
   */
  public function setCommentaireMotu($commentaireMotu) {
    $this->commentaireMotu = $commentaireMotu;

    return $this;
  }

  /**
   * Get commentaireMotu
   *
   * @return string
   */
  public function getCommentaireMotu() {
    return $this->commentaireMotu;
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
   * Set libelleMotu
   *
   * @param string $libelleMotu
   *
   * @return MotuDataset
   */
  public function setLibelleMotu($libelleMotu) {
    $this->libelleMotu = $libelleMotu;

    return $this;
  }

  /**
   * Get libelleMotu
   *
   * @return string
   */
  public function getLibelleMotu() {
    return $this->libelleMotu;
  }
}
