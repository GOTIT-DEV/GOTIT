<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pays
 *
 * @ORM\Table(name="country",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_country__country_code", columns={"country_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codePays"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Pays extends AbstractTimestampedEntity {
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
   * @ORM\OneToMany(targetEntity="Commune", mappedBy="paysFk")
   * @ORM\OrderBy({"codeCommune" = "asc"})
   */
  private $communes;

  /**
   * @inheritdoc
   */
  public function __construct() {
    $this->communes = new ArrayCollection();
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
   * @return Pays
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
   * @return Pays
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

  public function getCommunes() {
    return $this->communes;
  }
}
