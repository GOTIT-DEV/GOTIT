<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commune
 *
 * @ORM\Table(name="municipality",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_municipality__municipality_code", columns={"municipality_code"})},
 *  indexes={@ORM\Index(name="IDX_E2E2D1EEB1C3431A", columns={"country_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeCommune"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Commune extends AbstractTimestampedEntity {
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
  private $codeCommune;

  /**
   * @var string
   *
   * @Groups("own")
   * @ORM\Column(name="municipality_name", type="string", length=1024, nullable=false)
   */
  private $nomCommune;

  /**
   * @var string
   *
   * @Groups("own")
   * @ORM\Column(name="region_name", type="string", length=1024, nullable=false)
   */
  private $nomRegion;

  /**
   * @var \Pays
   *
   * @ORM\ManyToOne(targetEntity="Pays", inversedBy="communes")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $paysFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set codeCommune
   *
   * @param string $codeCommune
   *
   * @return Commune
   */
  public function setCodeCommune($codeCommune) {
    $this->codeCommune = $codeCommune;

    return $this;
  }

  /**
   * Get codeCommune
   *
   * @return string
   */
  public function getCodeCommune() {
    return $this->codeCommune;
  }

  /**
   * Set nomCommune
   *
   * @param string $nomCommune
   *
   * @return Commune
   */
  public function setNomCommune($nomCommune) {
    $this->nomCommune = $nomCommune;

    return $this;
  }

  /**
   * Get nomCommune
   *
   * @return string
   */
  public function getNomCommune() {
    return $this->nomCommune;
  }

  /**
   * Set nomRegion
   *
   * @param string $nomRegion
   *
   * @return Commune
   */
  public function setNomRegion($nomRegion) {
    $this->nomRegion = $nomRegion;

    return $this;
  }

  /**
   * Get nomRegion
   *
   * @return string
   */
  public function getNomRegion() {
    return $this->nomRegion;
  }

  /**
   * Set paysFk
   *
   * @param \App\Entity\Pays $paysFk
   *
   * @return Commune
   */
  public function setPaysFk(\App\Entity\Pays $paysFk = null) {
    $this->paysFk = $paysFk;

    return $this;
  }

  /**
   * Get paysFk
   *
   * @return \App\Entity\Pays
   */
  public function getPaysFk() {
    return $this->paysFk;
  }
}
