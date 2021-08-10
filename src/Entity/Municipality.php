<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Municipality
 *
 * @ORM\Table(name="municipality",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_municipality__municipality_code", columns={"municipality_code"})},
 *  indexes={@ORM\Index(name="IDX_E2E2D1EEB1C3431A", columns={"country_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Municipality extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @Groups("own")
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="municipality_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @Groups("own")
   * @ORM\Column(name="municipality_code", type="string", length=255, nullable=false)
   */
  private $code;

  /**
   * @var string
   *
   * @Groups("own")
   * @ORM\Column(name="municipality_name", type="string", length=1024, nullable=false)
   */
  private $name;

  /**
   * @var string
   *
   * @Groups("own")
   * @ORM\Column(name="region_name", type="string", length=1024, nullable=false)
   */
  private $region;

  /**
   * @var \Country
   *
   * @ORM\ManyToOne(targetEntity="Country", inversedBy="municipalities", fetch="EAGER")
   * @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
   */
  private $countryFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set code
   *
   * @param string $code
   *
   * @return Municipality
   */
  public function setCode($code) {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return Municipality
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set region
   *
   * @param string $region
   *
   * @return Municipality
   */
  public function setRegion($region) {
    $this->region = $region;

    return $this;
  }

  /**
   * Get region
   *
   * @return string
   */
  public function getRegion() {
    return $this->region;
  }

  /**
   * Set countryFk
   *
   * @param \App\Entity\Country $countryFk
   *
   * @return Municipality
   */
  public function setCountryFk(\App\Entity\Country $countryFk = null) {
    $this->countryFk = $countryFk;

    return $this;
  }

  /**
   * Get countryFk
   *
   * @return \App\Entity\Country
   */
  public function getCountryFk() {
    return $this->countryFk;
  }
}
