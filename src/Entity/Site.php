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
 * @UniqueEntity(fields={"codeStation"}, message="This code is already registered")
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
  private $codeStation;

  /**
   * @var string
   *
   * @ORM\Column(name="site_name", type="string", length=1024, nullable=false)
   */
  private $nomStation;

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
  private $infoLocalisation;

  /**
   * @var string
   *
   * @ORM\Column(name="site_description", type="text", nullable=true)
   */
  private $infoDescription;

  /**
   * @var string
   *
   * @ORM\Column(name="site_comments", type="text", nullable=true)
   */
  private $commentaireStation;

  /**
   * @var \Municipality
   *
   * @ORM\ManyToOne(targetEntity="Municipality")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="municipality_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $municipalityFk;

  /**
   * @var \Country
   *
   * @ORM\ManyToOne(targetEntity="Country")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $countryFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="access_point_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $pointAccesVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="habitat_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $habitatTypeVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="coordinate_precision_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $precisionLatLongVocFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set codeStation
   *
   * @param string $codeStation
   *
   * @return Site
   */
  public function setCodeStation($codeStation) {
    $this->codeStation = $codeStation;

    return $this;
  }

  /**
   * Get codeStation
   *
   * @return string
   */
  public function getCodeStation() {
    return $this->codeStation;
  }

  /**
   * Set nomStation
   *
   * @param string $nomStation
   *
   * @return Site
   */
  public function setNomStation($nomStation) {
    $this->nomStation = $nomStation;

    return $this;
  }

  /**
   * Get nomStation
   *
   * @return string
   */
  public function getNomStation() {
    return $this->nomStation;
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
   * Set infoLocalisation
   *
   * @param string $infoLocalisation
   *
   * @return Site
   */
  public function setInfoLocalisation($infoLocalisation) {
    $this->infoLocalisation = $infoLocalisation;

    return $this;
  }

  /**
   * Get infoLocalisation
   *
   * @return string
   */
  public function getInfoLocalisation() {
    return $this->infoLocalisation;
  }

  /**
   * Set infoDescription
   *
   * @param string $infoDescription
   *
   * @return Site
   */
  public function setInfoDescription($infoDescription) {
    $this->infoDescription = $infoDescription;

    return $this;
  }

  /**
   * Get infoDescription
   *
   * @return string
   */
  public function getInfoDescription() {
    return $this->infoDescription;
  }

  /**
   * Set commentaireStation
   *
   * @param string $commentaireStation
   *
   * @return Site
   */
  public function setCommentaireStation($commentaireStation) {
    $this->commentaireStation = $commentaireStation;

    return $this;
  }

  /**
   * Get commentaireStation
   *
   * @return string
   */
  public function getCommentaireStation() {
    return $this->commentaireStation;
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
   * Set pointAccesVocFk
   *
   * @param \App\Entity\Voc $pointAccesVocFk
   *
   * @return Site
   */
  public function setPointAccesVocFk(\App\Entity\Voc $pointAccesVocFk = null) {
    $this->pointAccesVocFk = $pointAccesVocFk;

    return $this;
  }

  /**
   * Get pointAccesVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPointAccesVocFk() {
    return $this->pointAccesVocFk;
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
   * Set precisionLatLongVocFk
   *
   * @param \App\Entity\Voc $precisionLatLongVocFk
   *
   * @return Site
   */
  public function setPrecisionLatLongVocFk(\App\Entity\Voc $precisionLatLongVocFk = null) {
    $this->precisionLatLongVocFk = $precisionLatLongVocFk;

    return $this;
  }

  /**
   * Get precisionLatLongVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPrecisionLatLongVocFk() {
    return $this->precisionLatLongVocFk;
  }
}