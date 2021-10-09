<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Country
 */
#[ORM\Entity]
#[ORM\Table(name: 'country')]
#[ORM\UniqueConstraint(name: 'uk_country__country_code', columns: ['country_code'])]
#[UniqueEntity(fields: ['code'], message: 'Country code {{ value }} is already registered')]
class Country extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  private int $id;

  #[ORM\Column(name: 'country_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'country_name', type: 'string', length: 255, nullable: false)]
  private string $name;

  #[ORM\OneToMany(targetEntity: 'Municipality', mappedBy: 'country')]
  #[ORM\OrderBy(value: ['code' => 'asc'])]
  private Collection $municipalities;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->municipalities = new ArrayCollection();
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

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getMunicipalities(): Collection {
    return $this->municipalities;
  }

  private function _generateCode(): string {
    return str_replace(' ', '_', strtoupper($this->getName()));
  }
}
