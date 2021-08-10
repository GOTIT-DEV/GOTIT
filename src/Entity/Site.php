<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Site
 *
 * @ORM\Table(name="site",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_site__site_code", columns={"site_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_9F39F8B143D4E2C", columns={"municipality_fk"}),
 *      @ORM\Index(name="IDX_9F39F8B1B1C3431A", columns={"country_fk"}),
 *      @ORM\Index(name="IDX_9F39F8B14D50D031", columns={"access_point_voc_fk"}),
 *      @ORM\Index(name="IDX_9F39F8B1C23046AE", columns={"habitat_type_voc_fk"}),
 *      @ORM\Index(name="IDX_9F39F8B1E86DBD90", columns={"coordinate_precision_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Site extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="site_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="site_code", type="string", length=255, nullable=false)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="site_name", type="string", length=1024, nullable=false)
   */
  private $name;

  /**
   * @var float
   *
   * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=false)
   */
  private $latDegDec;

  /**
   * @var float
   *
   * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=false)
   */
  private $longDegDec;

  /**
   * @var integer
   *
   * @ORM\Column(name="elevation", type="bigint", nullable=true)
   */
  private $altitudeM;

  /**
   * @var string
   *
   * @ORM\Column(name="location_info", type="text", nullable=true)
   */
  private $locationInfo;

  /**
   * @var string
   *
   * @ORM\Column(name="site_description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var string
   *
   * @ORM\Column(name="site_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Municipality
   *
   * @ORM\ManyToOne(targetEntity="Municipality", fetch="EAGER")
   * @ORM\JoinColumn(name="municipality_fk", referencedColumnName="id", nullable=false)
   */
  private $municipalityFk;

  /**
   * @var \Country
   *
   * @ORM\ManyToOne(targetEntity="Country", fetch="EAGER")
   * @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
   */
  private $countryFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="access_point_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $accessPointVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="habitat_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $habitatTypeVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="coordinate_precision_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $coordinatesPrecisionVocFk;

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
   * @return Site
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
   * @return Site
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
   * Set latDegDec
   *
   * @param float $latDegDec
   *
   * @return Site
   */
  public function setLatDegDec($latDegDec) {
    $this->latDegDec = $latDegDec;

    return $this;
  }

  /**
   * Get latDegDec
   *
   * @return float
   */
  public function getLatDegDec() {
    return $this->latDegDec;
  }

  /**
   * Set longDegDec
   *
   * @param float $longDegDec
   *
   * @return Site
   */
  public function setLongDegDec($longDegDec) {
    $this->longDegDec = $longDegDec;

    return $this;
  }

  /**
   * Get longDegDec
   *
   * @return float
   */
  public function getLongDegDec() {
    return $this->longDegDec;
  }

  /**
   * Set altitudeM
   *
   * @param integer $altitudeM
   *
   * @return Site
   */
  public function setAltitudeM($altitudeM) {
    $this->altitudeM = $altitudeM;

    return $this;
  }

  /**
   * Get altitudeM
   *
   * @return integer
   */
  public function getAltitudeM() {
    return $this->altitudeM;
  }

  /**
   * Set locationInfo
   *
   * @param string $locationInfo
   *
   * @return Site
   */
  public function setLocationInfo($locationInfo) {
    $this->locationInfo = $locationInfo;

    return $this;
  }

  /**
   * Get locationInfo
   *
   * @return string
   */
  public function getLocationInfo() {
    return $this->locationInfo;
  }

  /**
   * Set description
   *
   * @param string $description
   *
   * @return Site
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Site
   */
  public function setComment($comment) {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Set municipalityFk
   *
   * @param \App\Entity\Municipality $municipalityFk
   *
   * @return Site
   */
  public function setMunicipalityFk(\App\Entity\Municipality $municipalityFk = null) {
    $this->municipalityFk = $municipalityFk;

    return $this;
  }

  /**
   * Get municipalityFk
   *
   * @return \App\Entity\Municipality
   */
  public function getMunicipalityFk() {
    return $this->municipalityFk;
  }

  /**
   * Set countryFk
   *
   * @param \App\Entity\Country $countryFk
   *
   * @return Site
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

  /**
   * Set accessPointVocFk
   *
   * @param \App\Entity\Voc $accessPointVocFk
   *
   * @return Site
   */
  public function setAccessPointVocFk(\App\Entity\Voc $accessPointVocFk = null) {
    $this->accessPointVocFk = $accessPointVocFk;

    return $this;
  }

  /**
   * Get accessPointVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getAccessPointVocFk() {
    return $this->accessPointVocFk;
  }

  /**
   * Set habitatTypeVocFk
   *
   * @param \App\Entity\Voc $habitatTypeVocFk
   *
   * @return Site
   */
  public function setHabitatTypeVocFk(\App\Entity\Voc $habitatTypeVocFk = null) {
    $this->habitatTypeVocFk = $habitatTypeVocFk;

    return $this;
  }

  /**
   * Get habitatTypeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getHabitatTypeVocFk() {
    return $this->habitatTypeVocFk;
  }

  /**
   * Set coordinatesPrecisionVocFk
   *
   * @param \App\Entity\Voc $coordinatesPrecisionVocFk
   *
   * @return Site
   */
  public function setCoordinatesPrecisionVocFk(\App\Entity\Voc $coordinatesPrecisionVocFk = null) {
    $this->coordinatesPrecisionVocFk = $coordinatesPrecisionVocFk;

    return $this;
  }

  /**
   * Get coordinatesPrecisionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getCoordinatesPrecisionVocFk() {
    return $this->coordinatesPrecisionVocFk;
  }
}
