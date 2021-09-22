<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Voc
 *
 * @ORM\Table(name="vocabulary",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_vocabulary__parent__code", columns={"code", "parent"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code", "parent"}, message="This code is already registered for the specified parent")
 *
 * @ApiResource(
 *  collectionOperations={"get"={"normalization_context"={"groups"={"item"}}}},
 *  itemOperations={"get"={"normalization_context"={"groups"={"item"}}}},
 *  order={"parent"="ASC", "code"="ASC"},
 *  paginationEnabled=true
 * )
 *
 */
class Voc extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="vocabulary_id_seq", allocationSize=1, initialValue=1)
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="code", type="string", length=255, nullable=false)
   * @Groups({"item"})
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="vocabulary_title", type="string", length=1024, nullable=false)
   * @Groups({"item"})
   */
  private $label;

  /**
   * @var string
   *
   * @ORM\Column(name="parent", type="string", length=255, nullable=false)
   * @Groups({"item"})
   */
  private $parent;

  /**
   * @var string
   *
   * @ORM\Column(name="voc_comments", type="text", nullable=true)
   * @Groups({"item"})
   */
  private $comment;

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
   * Set label
   *
   * @param string $label
   *
   * @return Voc
   */
  public function setLabel($label) {
    $this->label = $label;

    return $this;
  }

  /**
   * Get label
   *
   * @return string
   */
  public function getLabel() {
    return $this->label;
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
   * Set comment
   *
   * @param string $comment
   *
   * @return Voc
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
}
