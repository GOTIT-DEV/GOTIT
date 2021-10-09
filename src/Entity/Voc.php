<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Vocabulary term
 */
#[ORM\Entity]
#[ORM\Table(name: 'vocabulary')]
#[ORM\UniqueConstraint(name: 'uk_vocabulary__parent__code', columns: ['code', 'parent'])]
#[UniqueEntity(
  fields: ['code', 'parent'],
  message: 'This code is already registered for the specified parent'
)]
#[ApiResource(
  collectionOperations: [
    'get' => ['normalization_context' => ['groups' => ['item']]],
  ],
  itemOperations: [
    'get' => ['normalization_context' => ['groups' => ['item']]],
  ],
  order: ['parent' => 'ASC', 'code' => 'ASC'],
  paginationEnabled: true
)]
class Voc extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: ['item'])]
  private int $id;

  #[ORM\Column(name: 'code', type: 'string', length: 255, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $code;

  #[ORM\Column(name: 'vocabulary_title', type: 'string', length: 1024, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $label;

  #[ORM\Column(name: 'parent', type: 'string', length: 255, nullable: false)]
  #[Groups(groups: ['item'])]
  private string $parent;

  #[ORM\Column(name: 'voc_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  public function getId(): int {
    return $this->id;
  }

  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  public function getCode(): string {
    return $this->code;
  }

  public function setLabel(string $label): self {
    $this->label = $label;

    return $this;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function setParent(string $parent): self {
    $this->parent = $parent;

    return $this;
  }

  public function getParent(): string {
    return $this->parent;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }
}
