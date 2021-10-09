<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A person involved in any of the lab activities
 */
#[ORM\Entity]
#[ORM\Table(name: 'person')]
#[ORM\UniqueConstraint(name: 'uk_person__person_name', columns: ['person_name'])]
#[ORM\Index(name: 'IDX_FCEC9EFE8441376', columns: ['institution_fk'])]
#[UniqueEntity(fields: ['name'], message: 'A person with this name is already registered')]
#[ApiResource]
class Person extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: ['item'])]
  private int $id;

  #[ORM\Column(name: 'person_name', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(groups: ['item'])]
  private string $name;

  #[ORM\Column(name: 'person_full_name', type: 'string', length: 1024, nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $fullName = null;

  #[ORM\Column(name: 'person_name_bis', type: 'string', length: 255, nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $alias = null;

  #[ORM\Column(name: 'person_comments', type: 'text', nullable: true)]
  #[Groups(groups: ['item'])]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Institution')]
  #[ORM\JoinColumn(name: 'institution_fk', referencedColumnName: 'id', nullable: true)]
  private ?Institution $institution = null;

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

  public function setFullName(?string $fullName): self {
    $this->fullName = $fullName;

    return $this;
  }

  public function getFullName(): ?string {
    return $this->fullName;
  }

  public function setAlias(?string $alias): self {
    $this->alias = $alias;

    return $this;
  }

  public function getAlias(): ?string {
    return $this->alias;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setInstitution(?Institution $institution): self {
    $this->institution = $institution;

    return $this;
  }

  public function getInstitution(): ?Institution {
    return $this->institution;
  }
}
