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
 *
 * @ORM\Table(name="country",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_country__country_code", columns={"country_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="Country code {{ value }} is already registered")
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Country extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  /**
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="country_id_seq", allocationSize=1, initialValue=1)
   */
  private int $id;

  /**
   * @ORM\Column(name="country_code", type="string", length=255, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="country_name", type="string", length=255, nullable=false)
   */
  private string $name;

  /**
   * @ORM\OneToMany(targetEntity="Municipality", mappedBy="country")
   * @ORM\OrderBy({"code": "asc"})
   */
  private Collection $municipalities;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->municipalities = new ArrayCollection();
  }

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set code
   */
  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   */
  public function getCode(): string {
    return $this->code;
  }

  private function _generateCode(): string {
    return str_replace(' ', '_', strtoupper($this->getName()));
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

  public function getMunicipalities(): Collection {
    return $this->municipalities;
  }
}
