<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Voc
 *
 * @ORM\Table(name="vocabulary",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_vocabulary__parent__code", columns={"code", "parent"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code", "parent"}, message="This code is already registered for the specified parent")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Voc extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="vocabulary_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="code", type="string", length=255, nullable=false)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="vocabulary_title", type="string", length=1024, nullable=false)
   */
  private $libelle;

  /**
   * @var string
   *
   * @ORM\Column(name="parent", type="string", length=255, nullable=false)
   */
  private $parent;

  /**
   * @var string
   *
   * @ORM\Column(name="voc_comments", type="text", nullable=true)
   */
  private $commentaire;

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
   * @return Voc
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
   * Set libelle
   *
   * @param string $libelle
   *
   * @return Voc
   */
  public function setLibelle($libelle) {
    $this->libelle = $libelle;

    return $this;
  }

  /**
   * Get libelle
   *
   * @return string
   */
  public function getLibelle() {
    return $this->libelle;
  }

  /**
   * Set parent
   *
   * @param string $parent
   *
   * @return Voc
   */
  public function setParent($parent) {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Get parent
   *
   * @return string
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * Set commentaire
   *
   * @param string $commentaire
   *
   * @return Voc
   */
  public function setCommentaire($commentaire) {
    $this->commentaire = $commentaire;

    return $this;
  }

  /**
   * Get commentaire
   *
   * @return string
   */
  public function getCommentaire() {
    return $this->commentaire;
  }
}
