<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A research program
 */
#[ORM\Entity]
#[ORM\Table(name: 'program')]
#[UniqueConstraint(name: 'uk_program__program_code', columns: ['program_code'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Program extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'program_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'program_name', type: 'string', length: 1024, nullable: false)]
  private string $name;

  #[ORM\Column(name: 'coordinator_names', type: 'text', nullable: false)]
  private string $coordinators;

  #[ORM\Column(name: 'funding_agency', type: 'string', length: 1024, nullable: true)]
  private ?string $fundingAgency = null;

  #[ORM\Column(name: 'starting_year', type: 'smallint', nullable: true)]
  private ?int $startYear = null;

  #[ORM\Column(name: 'ending_year', type: 'smallint', nullable: true)]
  private ?int $endYear = null;

  #[ORM\Column(name: 'program_comments', type: 'text', nullable: true)]
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

  public function setName(string $name): self {
    $this->name = $name;

    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setCoordinators(string $coordinators): self {
    $this->coordinators = $coordinators;

    return $this;
  }

  public function getCoordinators(): string {
    return $this->coordinators;
  }

  public function setFundingAgency(?string $fundingAgency): self {
    $this->fundingAgency = $fundingAgency;

    return $this;
  }

  public function getFundingAgency(): ?string {
    return $this->fundingAgency;
  }

  public function setStartYear(?int $startYear): self {
    $this->startYear = $startYear;

    return $this;
  }

  public function getStartYear(): ?int {
    return $this->startYear;
  }

  public function setEndYear(?int $endYear): self {
    $this->endYear = $endYear;

    return $this;
  }

  public function getEndYear(): ?int {
    return $this->endYear;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }
}
