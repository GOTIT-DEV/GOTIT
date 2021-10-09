<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A sampling site
 */
#[ORM\Entity]
#[ORM\Table(name: 'site')]
#[ORM\UniqueConstraint(name: 'uk_site__site_code', columns: ['site_code'])]
#[ORM\Index(name: 'IDX_9F39F8B143D4E2C', columns: ['municipality_fk'])]
#[ORM\Index(name: 'IDX_9F39F8B1B1C3431A', columns: ['country_fk'])]
#[ORM\Index(name: 'IDX_9F39F8B14D50D031', columns: ['access_point_voc_fk'])]
#[ORM\Index(name: 'IDX_9F39F8B1C23046AE', columns: ['habitat_type_voc_fk'])]
#[ORM\Index(name: 'IDX_9F39F8B1E86DBD90', columns: ['coordinate_precision_voc_fk'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already registered')]
class Site extends AbstractTimestampedEntity {
  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  private int $id;

  #[ORM\Column(name: 'site_code', type: 'string', length: 255, nullable: false, unique: true)]
  private string $code;

  #[ORM\Column(name: 'site_name', type: 'string', length: 1024, nullable: false)]
  private string $name;

  #[ORM\Column(name: 'latitude', type: 'float', precision: 10, scale: 0, nullable: false)]
  private float $latDegDec;

  #[ORM\Column(name: 'longitude', type: 'float', precision: 10, scale: 0, nullable: false)]
  private float $longDegDec;

  #[ORM\Column(name: 'elevation', type: 'integer', nullable: true)]
  private ?int $altitudeM = null;

  #[ORM\Column(name: 'location_info', type: 'text', nullable: true)]
  private ?string $locationInfo = null;

  #[ORM\Column(name: 'site_description', type: 'text', nullable: true)]
  private ?string $description = null;

  #[ORM\Column(name: 'site_comments', type: 'text', nullable: true)]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Municipality', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'municipality_fk', referencedColumnName: 'id', nullable: false)]
  private Municipality $municipality;

  #[ORM\ManyToOne(targetEntity: 'Country', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'country_fk', referencedColumnName: 'id', nullable: false)]
  private Country $country;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'access_point_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $accessPoint;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'habitat_type_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $habitatType;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'coordinate_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $coordinatesPrecision;

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

  public function setLatDegDec(float $latDegDec): self {
    $this->latDegDec = $latDegDec;

    return $this;
  }

  public function getLatDegDec(): float {
    return $this->latDegDec;
  }

  public function setLongDegDec(float $longDegDec): self {
    $this->longDegDec = $longDegDec;

    return $this;
  }

  public function getLongDegDec(): float {
    return $this->longDegDec;
  }

  public function setAltitudeM(?int $altitudeM): self {
    $this->altitudeM = $altitudeM;

    return $this;
  }

  public function getAltitudeM(): ?int {
    return $this->altitudeM;
  }

  public function setLocationInfo(?string $locationInfo): self {
    $this->locationInfo = $locationInfo;

    return $this;
  }

  public function getLocationInfo(): ?string {
    return $this->locationInfo;
  }

  /**
   * Set description
   *
   * @param string $description
   */
  public function setDescription(?string $description): self {
    $this->description = $description;

    return $this;
  }

  public function getDescription(): ?string {
    return $this->description;
  }

  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setMunicipality(Municipality $municipality): self {
    $this->municipality = $municipality;

    return $this;
  }

  public function getMunicipality(): Municipality {
    return $this->municipality;
  }

  public function setCountry(Country $country): self {
    $this->country = $country;

    return $this;
  }

  public function getCountry(): Country {
    return $this->country;
  }

  public function setAccessPoint(Voc $accessPoint): self {
    $this->accessPoint = $accessPoint;

    return $this;
  }

  public function getAccessPoint(): Voc {
    return $this->accessPoint;
  }

  public function setHabitatType(Voc $habitatType): self {
    $this->habitatType = $habitatType;

    return $this;
  }

  public function getHabitatType(): Voc {
    return $this->habitatType;
  }

  public function setCoordinatesPrecision(Voc $coordinatesPrecision): self {
    $this->coordinatesPrecision = $coordinatesPrecision;

    return $this;
  }

  public function getCoordinatesPrecision(): Voc {
    return $this->coordinatesPrecision;
  }
}
