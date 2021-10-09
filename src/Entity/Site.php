<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
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
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="site_id_seq", allocationSize=1, initialValue=1)
	 */
	private int $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="site_code", type="string", length=255, nullable=false, unique=true)
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
	 * @ORM\Column(name="elevation", type="integer", nullable=true)
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
	 * @var Municipality
	 *
	 * @ORM\ManyToOne(targetEntity="Municipality", fetch="EAGER")
	 * @ORM\JoinColumn(name="municipality_fk", referencedColumnName="id", nullable=false)
	 */
	private $municipality;

	/**
	 * @var Country
	 *
	 * @ORM\ManyToOne(targetEntity="Country", fetch="EAGER")
	 * @ORM\JoinColumn(name="country_fk", referencedColumnName="id", nullable=false)
	 */
	private $country;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="access_point_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $accessPoint;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="habitat_type_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $habitatType;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="coordinate_precision_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $coordinatesPrecision;

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
	 * Set municipality
	 *
	 * @param Municipality $municipality
	 *
	 * @return Site
	 */
	public function setMunicipality(Municipality $municipality = null) {
		$this->municipality = $municipality;

		return $this;
	}

	/**
	 * Get municipality
	 *
	 * @return Municipality
	 */
	public function getMunicipality() {
		return $this->municipality;
	}

	/**
	 * Set country
	 *
	 * @param Country $country
	 *
	 * @return Site
	 */
	public function setCountry(Country $country = null) {
		$this->country = $country;

		return $this;
	}

	/**
	 * Get country
	 *
	 * @return Country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Set accessPoint
	 *
	 * @param Voc $accessPoint
	 *
	 * @return Site
	 */
	public function setAccessPoint(Voc $accessPoint = null) {
		$this->accessPoint = $accessPoint;

		return $this;
	}

	/**
	 * Get accessPoint
	 *
	 * @return Voc
	 */
	public function getAccessPoint() {
		return $this->accessPoint;
	}

	/**
	 * Set habitatType
	 *
	 * @param Voc $habitatType
	 *
	 * @return Site
	 */
	public function setHabitatType(Voc $habitatType = null) {
		$this->habitatType = $habitatType;

		return $this;
	}

	/**
	 * Get habitatType
	 *
	 * @return Voc
	 */
	public function getHabitatType() {
		return $this->habitatType;
	}

	/**
	 * Set coordinatesPrecision
	 *
	 * @param Voc $coordinatesPrecision
	 *
	 * @return Site
	 */
	public function setCoordinatesPrecision(Voc $coordinatesPrecision = null) {
		$this->coordinatesPrecision = $coordinatesPrecision;

		return $this;
	}

	/**
	 * Get coordinatesPrecision
	 *
	 * @return Voc
	 */
	public function getCoordinatesPrecision() {
		return $this->coordinatesPrecision;
	}
}
