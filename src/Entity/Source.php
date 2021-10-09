<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A bibliographical source
 */
#[ORM\Entity]
#[ORM\Table(name: 'source')]
#[ORM\UniqueConstraint(name: 'uk_source__source_code', columns: ['source_code'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Source extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'source_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'source_title', type: 'string', length: 2048, nullable: false)]
  private string $title;

  #[ORM\Column(name: 'source_year', type: 'smallint', nullable: true)]
  private ?int $year = null;

  #[ORM\Column(name: 'source_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'source_is_entered_by')]
  #[ORM\JoinColumn(name: 'source_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $providers;

  public function __construct() {
    $this->providers = new ArrayCollection();
  }

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

  public function setYear(?int $year): self {
    $this->year = $year;

    return $this;
  }

  public function getYear(): ?int {
    return $this->year;
  }

  public function setTitle(string $title): self {
    $this->title = $title;

    return $this;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function addProvider(Person $provider): self {
    $this->providers[] = $provider;

    return $this;
  }

  public function removeProvider(Person $provider): self {
    $this->providers->removeElement($provider);

    return $this;
  }

  public function getProviders(): Collection {
    return $this->providers;
  }
}
