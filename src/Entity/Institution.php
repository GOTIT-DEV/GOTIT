<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Institution
 *
 * @ORM\Table(name="institution",
 * uniqueConstraints={@ORM\UniqueConstraint(name="uk_institution__institution_name", columns={"institution_name"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"name"}, message="This name already exists")
 * @ApiResource
 */
class Institution extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="institution_id_seq", allocationSize=1, initialValue=1)
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_name", type="string", length=1024, nullable=false, unique=true)
   * @Groups({"item"})
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_comments", type="text", nullable=true)
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
   * Set name
   *
   * @param string $name
   *
   * @return Institution
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Institution
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
