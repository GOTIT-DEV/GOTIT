<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A biological material sampling operation
 */
#[ORM\Entity]
#[ORM\Table(name: 'sampling')]
#[ORM\UniqueConstraint(name: 'uk_sampling__sample_code', columns: ['sample_code'])]
#[ORM\Index(name: 'IDX_55AE4A3DA30C442F', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'IDX_55AE4A3D50BB334E', columns: ['donation_voc_fk'])]
#[ORM\Index(name: 'IDX_55AE4A3D369AB36B', columns: ['site_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Sampling extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'sample_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'sampling_date', type: 'date', nullable: true)]
  private ?\DateTime $date = null;

  #[ORM\Column(name: 'sampling_duration', type: 'integer', nullable: true)]
  private ?int $durationMn = null;

  #[ORM\Column(name: 'temperature', type: 'float', precision: 10, scale: 0, nullable: true)]
  private ?float $temperatureC = null;

  #[ORM\Column(name: 'specific_conductance', type: 'float', precision: 10, scale: 0, nullable: true)]
  private ?float $conductanceMicroSieCm = null;

  #[ORM\Column(name: 'sample_status', type: 'boolean', nullable: false)]
  private bool $status;

  #[ORM\Column(name: 'sampling_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $datePrecision;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'donation_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $donation;

  #[ORM\ManyToOne(targetEntity: 'Site', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'site_fk', referencedColumnName: 'id', nullable: false)]
  private Site $site;

  #[ORM\ManyToMany(targetEntity: 'Voc', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'sampling_is_done_with_method')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'sampling_method_voc_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $methods;

  #[ORM\ManyToMany(targetEntity: 'Voc', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'sample_is_fixed_with')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'fixative_voc_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $fixatives;

  #[ORM\ManyToMany(targetEntity: 'Program', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'sampling_is_funded_by')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'program_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $fundings;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'sampling_is_performed_by')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $participants;

  #[ORM\ManyToMany(targetEntity: 'Taxon', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'has_targeted_taxa')]
  #[ORM\JoinColumn(name: 'sampling_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'taxon_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  private array|Collection|ArrayCollection $targetTaxons;

  public function __construct() {
    $this->methods = new ArrayCollection();
    $this->fixatives = new ArrayCollection();
    $this->fundings = new ArrayCollection();
    $this->participants = new ArrayCollection();
    $this->targetTaxons = new ArrayCollection();
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

  public function setDate(?\DateTime $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDate(): ?\DateTime {
    return $this->date;
  }

  public function setDurationMn(?int $durationMn): self {
    $this->durationMn = $durationMn;

    return $this;
  }

  public function getDurationMn(): ?int {
    return $this->durationMn;
  }

  public function setTemperatureC(?float $temperatureC): self {
    $this->temperatureC = $temperatureC;

    return $this;
  }

  public function getTemperatureC(): ?float {
    return $this->temperatureC;
  }

  public function setConductanceMicroSieCm(?float $conductanceMicroSieCm): self {
    $this->conductanceMicroSieCm = $conductanceMicroSieCm;

    return $this;
  }

  public function getConductanceMicroSieCm(): ?float {
    return $this->conductanceMicroSieCm;
  }

  public function setStatus(bool $status): self {
    $this->status = $status;

    return $this;
  }

  public function getStatus(): bool {
    return $this->status;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setDatePrecision(Voc $datePrecision): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): Voc {
    return $this->datePrecision;
  }

  public function setDonation(Voc $donation): self {
    $this->donation = $donation;

    return $this;
  }

  public function getDonation(): Voc {
    return $this->donation;
  }

  public function setSite(Site $site): self {
    $this->site = $site;

    return $this;
  }

  public function getSite(): Site {
    return $this->site;
  }

  public function addMethod(Voc $method): self {
    $this->methods[] = $method;

    return $this;
  }

  public function removeMethod(Voc $method): self {
    $this->methods->removeElement($method);

    return $this;
  }

  public function getMethods(): Collection {
    return $this->methods;
  }

  public function addFixative(Voc $fixative): self {
    $this->fixatives[] = $fixative;

    return $this;
  }

  public function removeFixative(Voc $fixative): self {
    $this->fixatives->removeElement($fixative);

    return $this;
  }

  public function getFixatives(): Collection {
    return $this->fixatives;
  }

  public function addFunding(Program $funding): self {
    $this->fundings[] = $funding;

    return $this;
  }

  public function removeFunding(Program $funding): self {
    $this->fundings->removeElement($funding);

    return $this;
  }

  public function getFundings(): Collection {
    return $this->fundings;
  }

  public function addParticipant(Person $participant): self {
    $this->participants[] = $participant;

    return $this;
  }

  public function removeParticipant(Person $participant): self {
    $this->participants->removeElement($participant);

    return $this;
  }

  public function getParticipants(): Collection {
    return $this->participants;
  }

  public function addTargetTaxon(Taxon $taxon): self {
    $this->targetTaxons[] = $taxon;

    return $this;
  }

  public function removeTargetTaxon(Taxon $taxon): self {
    $this->targetTaxons->removeElement($taxon);

    return $this;
  }

  public function getTargetTaxons(): Collection {
    return $this->targetTaxons;
  }

  private function _generateCode() {
    $precision = $this->getDatePrecision()
      ->getCode();
    $date = $this->getDate();
    $formats = [
      'A' => 'Y00',
      'M' => 'Ym',
      'J' => 'Ym',
      'INC' => '000000',
    ];

    return join('_', [$this->getSite()->getCode(), $date->format($formats[$precision])]);
  }
}
