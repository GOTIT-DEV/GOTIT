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
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="institution_id_seq", allocationSize=1, initialValue=1)
   * @Groups({"item"})
   */
  private int $id;

  /**
   * @ORM\Column(name="institution_name", type="string", length=1024, nullable=false, unique=true)
   * @Groups({"item"})
   */
  private string $name;

  /**
   * @ORM\Column(name="institution_comments", type="text", nullable=true)
   * @Groups({"item"})
   */
  private ?string $comment;

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set name
   */
  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   */
  public function getName(): string {
    return $this->name;
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
   */
  public function getComment(): ?string {
    return $this->comment;
  }
}
