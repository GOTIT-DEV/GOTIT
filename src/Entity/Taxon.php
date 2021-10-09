<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Taxon
 */
#[ORM\Entity]
#[ORM\Table(name: 'taxon')]
#[ORM\UniqueConstraint(name: 'uk_taxon__taxon_name', columns: ['taxon_name'])]
#[ORM\UniqueConstraint(name: 'uk_taxon__taxon_code', columns: ['taxon_code'])]
#[UniqueEntity(fields: ['taxname'], message: 'This name already exists')]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Taxon extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'taxon_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'taxon_name', type: 'string', length: 255, nullable: false, unique: true)]
  private string $taxname;

  #[ORM\Column(name: 'taxon_full_name', type: 'string', length: 255, nullable: true)]
  private ?string $fullName = null;

  #[ORM\Column(name: 'taxon_rank', type: 'string', length: 255, nullable: false)]
  private string $rank;

  #[ORM\Column(name: 'subclass', type: 'string', length: 255, nullable: true)]
  private ?string $subclass = null;

  #[ORM\Column(name: 'taxon_order', type: 'string', length: 255, nullable: true)]
  private ?string $ordre = null;

  #[ORM\Column(name: 'family', type: 'string', length: 255, nullable: true)]
  private ?string $family = null;

  #[ORM\Column(name: 'genus', type: 'string', length: 255, nullable: true)]
  private ?string $genus = null;

  #[ORM\Column(name: 'species', type: 'string', length: 255, nullable: true)]
  private ?string $species = null;

  #[ORM\Column(name: 'subspecies', type: 'string', length: 255, nullable: true)]
  private ?string $subspecies = null;

  #[ORM\Column(name: 'taxon_validity', type: 'boolean', nullable: false)]
  private bool $validity;

  #[ORM\Column(name: 'taxon_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\Column(name: 'clade', type: 'string', length: 255, nullable: true)]
  private ?string $clade = null;

  #[ORM\Column(name: 'taxon_synonym', type: 'string', length: 255, nullable: true)]
  private ?string $alias = null;

  public function getId(): int {
    return $this->id;
  }

  public function setTaxname(string $taxname): self {
    $this->taxname = $taxname;

    return $this;
  }

  public function getTaxname(): string {
    return $this->taxname;
  }

  public function setRank(string $rank): self {
    $this->rank = $rank;

    return $this;
  }

  public function getRank(): string {
    return $this->rank;
  }

  public function setSubclass(?string $subclass): self {
    $this->subclass = $subclass;

    return $this;
  }

  public function getSubclass(): ?string {
    return $this->subclass;
  }

  public function setOrdre(?string $ordre): self {
    $this->ordre = $ordre;

    return $this;
  }

  public function getOrdre(): ?string {
    return $this->ordre;
  }

  public function setFamily(?string $family): self {
    $this->family = $family;

    return $this;
  }

  public function getFamily(): ?string {
    return $this->family;
  }

  public function setGenus(?string $genus): self {
    $this->genus = $genus;

    return $this;
  }

  public function getGenus(): ?string {
    return $this->genus;
  }

  public function setSpecies(?string $species): self {
    $this->species = $species;

    return $this;
  }

  public function getSpecies(): ?string {
    return $this->species;
  }

  public function setSubspecies(?string $subspecies): self {
    $this->subspecies = $subspecies;

    return $this;
  }

  public function getSubspecies(): ?string {
    return $this->subspecies;
  }

  public function setValidity(bool $validity): self {
    $this->validity = $validity;

    return $this;
  }

  public function getValidity(): bool {
    return $this->validity;
  }

  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  public function getCode(): string {
    return $this->code;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setClade(?string $clade): self {
    $this->clade = $clade;

    return $this;
  }

  public function getClade(): ?string {
    return $this->clade;
  }

  public function setAlias(?string $alias): self {
    $this->alias = $alias;

    return $this;
  }

  public function getAlias(): ?string {
    return $this->alias;
  }

  public function setFullName(?string $fullName): self {
    $this->fullName = $fullName;

    return $this;
  }

  public function getFullName(): ?string {
    return $this->fullName;
  }
}
