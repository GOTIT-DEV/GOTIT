<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Any institution related to research, fundings, etc.
 */
#[ORM\Entity]
#[ORM\Table(name: 'institution')]
#[ORM\UniqueConstraint(name: 'uk_institution__institution_name', columns: ['institution_name'])]
#[UniqueEntity(fields: ['name'], message: 'This name already exists')]
#[ApiResource]
class Institution extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[Groups(groups: ['item'])]
  private int $id;

  #[ORM\Column(name: 'institution_name', type: 'string', length: 1024, nullable: false, unique: true)]
  #[Groups(groups: ['item'])]
  private string $name;

  #[ORM\Column(name: 'institution_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  public function getId(): int {
    return $this->id;
  }

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }
}
