<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Country
 *
 * @ORM\Table(name="country",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_country__country_code", columns={"country_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codePays"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Country extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="country_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="country_code", type="string", length=255, nullable=false)
   */
  private $codePays;

  /**
   * @var string
   *
   * @ORM\Column(name="country_name", type="string", length=1024, nullable=false)
   */
  private $nomPays;

  /**
   * @ORM\OneToMany(targetEntity="Municipality", mappedBy="countryFk")
   * @ORM\OrderBy({"codeCommune" = "asc"})
   */
  private $municipalities;

  /**
   * @inheritdoc
   */
  public function __construct() {
    $this->municipalities = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set codePays
   *
   * @param string $codePays
   *
   * @return Country
   */
  public function setCodePays($codePays) {
    $this->codePays = $codePays;
    return $this;
  }

  /**
   * Get codePays
   *
   * @return string
   */
  public function getCodePays() {
    return $this->codePays;
  }

  /**
   * Set nomPays
   *
   * @param string $nomPays
   *
   * @return Country
   */
  public function setNomPays($nomPays) {
    $this->nomPays = $nomPays;
    return $this;
  }

  /**
   * Get nomPays
   *
   * @return string
   */
  public function getNomPays() {
    return $this->nomPays;
  }

  public function getMunicipalities() {
    return $this->municipalities;
  }
}
