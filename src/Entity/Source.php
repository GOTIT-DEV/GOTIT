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
 * @UniqueEntity(fields={"codeSource"}, message="This code is already registered")
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
   * @ORM\Column(name="source_code", type="string", length=255, nullable=false)
   */
  private $codeSource;

  /**
   * @var integer
   *
   * @ORM\Column(name="source_year", type="bigint", nullable=true)
   */
  private $anneeSource;

  /**
   * @var string
   *
   * @ORM\Column(name="source_title", type="string", length=2048, nullable=false)
   */
  private $libelleSource;

  /**
   * @var string
   *
   * @ORM\Column(name="source_comments", type="text", nullable=true)
   */
  private $commentaireSource;

  /**
   * @ORM\OneToMany(targetEntity="SourceAEteIntegrePar", mappedBy="sourceFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sourceAEteIntegrePars;

  public function __construct() {
    $this->sourceAEteIntegrePars = new ArrayCollection();
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
   * Set codeSource
   *
   * @param string $codeSource
   *
   * @return Source
   */
  public function setCodeSource($codeSource) {
    $this->codeSource = $codeSource;

    return $this;
  }

  /**
   * Get codeSource
   *
   * @return string
   */
  public function getCodeSource() {
    return $this->codeSource;
  }

  /**
   * Set anneeSource
   *
   * @param integer $anneeSource
   *
   * @return Source
   */
  public function setAnneeSource($anneeSource) {
    $this->anneeSource = $anneeSource;

    return $this;
  }

  /**
   * Get anneeSource
   *
   * @return integer
   */
  public function getAnneeSource() {
    return $this->anneeSource;
  }

  /**
   * Set libelleSource
   *
   * @param string $libelleSource
   *
   * @return Source
   */
  public function setLibelleSource($libelleSource) {
    $this->libelleSource = $libelleSource;

    return $this;
  }

  /**
   * Get libelleSource
   *
   * @return string
   */
  public function getLibelleSource() {
    return $this->libelleSource;
  }

  /**
   * Set commentaireSource
   *
   * @param string $commentaireSource
   *
   * @return Source
   */
  public function setCommentaireSource($commentaireSource) {
    $this->commentaireSource = $commentaireSource;

    return $this;
  }

  /**
   * Get commentaireSource
   *
   * @return string
   */
  public function getCommentaireSource() {
    return $this->commentaireSource;
  }

  /**
   * Add sourceAEteIntegrePar
   *
   * @param \App\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar
   *
   * @return Source
   */
  public function addSourceAEteIntegrePar(\App\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar) {
    $sourceAEteIntegrePar->setSourceFk($this);
    $this->sourceAEteIntegrePars[] = $sourceAEteIntegrePar;

    return $this;
  }

  /**
   * Remove sourceAEteIntegrePar
   *
   * @param \App\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar
   */
  public function removeSourceAEteIntegrePar(\App\Entity\SourceAEteIntegrePar $sourceAEteIntegrePar) {
    $this->sourceAEteIntegrePars->removeElement($sourceAEteIntegrePar);
  }

  /**
   * Get sourceAEteIntegrePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSourceAEteIntegrePars() {
    return $this->sourceAEteIntegrePars;
  }
}
