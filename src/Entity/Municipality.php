<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Municipality
 */
#[ORM\Entity]
#[ORM\Table(name: 'municipality')]
#[ORM\Index(name: 'IDX_E2E2D1EEB1C3431A', columns: ['country_fk'])]
#[ORM\UniqueConstraint(name: 'uk_municipality__municipality_code', columns: ['municipality_code'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Municipality extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(groups: 'item')]
  private int $id;

  #[ORM\Column(name: 'municipality_code', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(groups: 'item')]
  private string $code;

  #[ORM\Column(name: 'municipality_name', type: 'string', length: 1024, nullable: false)]
  #[Groups(groups: 'item')]
  private string $name;

  #[ORM\Column(name: 'region_name', type: 'string', length: 1024, nullable: false)]
  #[Groups(groups: 'item')]
  private string $region;

  #[ORM\ManyToOne(targetEntity: 'Country', inversedBy: 'municipalities', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'country_fk', referencedColumnName: 'id', nullable: false)]
  private Country $country;

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

  public function setRegion(string $region): self {
    $this->region = $region;

    return $this;
  }

  public function getRegion(): string {
    return $this->region;
  }

  public function setCountry(Country $country): self {
    $this->country = $country;

    return $this;
  }

  public function getCountry(): Country {
    return $this->country;
  }

  private function _generateCode(): string {
    $code = join('|', [$this->getName(), $this->getRegion(), $this->getCountry()->getName()]);

    return str_replace(' ', '_', strtoupper($code));
  }
}
