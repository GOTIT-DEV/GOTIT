<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use App\Entity\TargetTaxon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Sampling
 *
 * @ORM\Table(name="sampling",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_sampling__sample_code", columns={"sample_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_55AE4A3DA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_55AE4A3D50BB334E", columns={"donation_voc_fk"}),
 *      @ORM\Index(name="IDX_55AE4A3D369AB36B", columns={"site_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Sampling extends AbstractTimestampedEntity {
	use CompositeCodeEntityTrait;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="sampling_id_seq", allocationSize=1, initialValue=1)
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sample_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="sampling_date", type="date", nullable=true)
	 */
	private $date;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="sampling_duration", type="bigint", nullable=true)
	 */
	private $durationMn;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="temperature", type="float", precision=10, scale=0, nullable=true)
	 */
	private $temperatureC;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="specific_conductance", type="float", precision=10, scale=0, nullable=true)
	 */
	private $conductanceMicroSieCm;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="sample_status", type="smallint", nullable=false)
	 */
	private $status;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="sampling_comments", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $datePrecision;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="donation_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $donation;

	/**
	 * @var Site
	 *
	 * @ORM\ManyToOne(targetEntity="Site", fetch="EAGER")
	 * @ORM\JoinColumn(name="site_fk", referencedColumnName="id", nullable=false)
	 */
	private $site;

	/**
	 * @ORM\ManyToMany(targetEntity="Voc", cascade={"persist"})
	 * @ORM\JoinTable(name="sampling_is_done_with_method",
	 *  joinColumns={@ORM\JoinColumn(name="sampling_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="sampling_method_voc_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $methods;

	/**
	 * @ORM\ManyToMany(targetEntity="Voc", cascade={"persist"})
	 * @ORM\JoinTable(name="sample_is_fixed_with",
	 *  joinColumns={@ORM\JoinColumn(name="sampling_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="fixative_voc_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $fixatives;

	/**
	 * @ORM\ManyToMany(targetEntity="Program", cascade={"persist"})
	 * @ORM\JoinTable(name="sampling_is_funded_by",
	 *  joinColumns={@ORM\JoinColumn(name="sampling_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="program_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $fundings;

	/**
	 * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
	 * @ORM\JoinTable(name="sampling_is_performed_by",
	 *  joinColumns={@ORM\JoinColumn(name="sampling_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $participants;

	/**
	 * @ORM\ManyToMany(targetEntity="Taxon", cascade={"persist"})
	 * @ORM\JoinTable(name="has_targeted_taxa",
	 *  joinColumns={@ORM\JoinColumn(name="sampling_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="taxon_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $targetTaxons;

	public function __construct() {
		$this->methods = new ArrayCollection();
		$this->fixatives = new ArrayCollection();
		$this->fundings = new ArrayCollection();
		$this->participants = new ArrayCollection();
		$this->targetTaxons = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId(): ?string {
		return $this->id;
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Sampling
	 */
	public function setCode($code): Sampling {
		$this->code = $code;
		return $this;
	}

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode(): ?string {
		return $this->code;
	}

	private function _generateCode() {
		$precision = $this->getDatePrecision()->getCode();
		$date = $this->getDate();
		$formats = [
			"A" => "Y00",
			"M" => "Ym",
			"J" => "Ym",
			"INC" => "000000",
		];
		return join("_", [
			$this->getSite()->getCode(),
			$date->format($formats[$precision]),
		]);
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 *
	 * @return Sampling
	 */
	public function setDate($date): Sampling {
		$this->date = $date;
		return $this;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate(): ?\DateTime {
		return $this->date;
	}

	/**
	 * Set durationMn
	 *
	 * @param integer $durationMn
	 *
	 * @return Sampling
	 */
	public function setDurationMn($durationMn) {
		$this->durationMn = $durationMn;
		return $this;
	}

	/**
	 * Get durationMn
	 *
	 * @return integer
	 */
	public function getDurationMn() {
		return $this->durationMn;
	}

	/**
	 * Set temperatureC
	 *
	 * @param float $temperatureC
	 *
	 * @return Sampling
	 */
	public function setTemperatureC($temperatureC) {
		$this->temperatureC = $temperatureC;
		return $this;
	}

	/**
	 * Get temperatureC
	 *
	 * @return float
	 */
	public function getTemperatureC() {
		return $this->temperatureC;
	}

	/**
	 * Set conductanceMicroSieCm
	 *
	 * @param float $conductanceMicroSieCm
	 *
	 * @return Sampling
	 */
	public function setConductanceMicroSieCm($conductanceMicroSieCm) {
		$this->conductanceMicroSieCm = $conductanceMicroSieCm;
		return $this;
	}

	/**
	 * Get conductanceMicroSieCm
	 *
	 * @return float
	 */
	public function getConductanceMicroSieCm() {
		return $this->conductanceMicroSieCm;
	}

	/**
	 * Set status
	 *
	 * @param integer $status
	 *
	 * @return Sampling
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	/**
	 * Get status
	 *
	 * @return integer
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Sampling
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
	 * Set datePrecision
	 *
	 * @param \App\Entity\Voc $datePrecision
	 *
	 * @return Sampling
	 */
	public function setDatePrecision(\App\Entity\Voc $datePrecision = null) {
		$this->datePrecision = $datePrecision;
		return $this;
	}

	/**
	 * Get datePrecision
	 *
	 * @return \App\Entity\Voc
	 */
	public function getDatePrecision() {
		return $this->datePrecision;
	}

	/**
	 * Set donation
	 *
	 * @param \App\Entity\Voc $donation
	 *
	 * @return Sampling
	 */
	public function setDonation(\App\Entity\Voc $donation = null) {
		$this->donation = $donation;
		return $this;
	}

	/**
	 * Get donation
	 *
	 * @return \App\Entity\Voc
	 */
	public function getDonation() {
		return $this->donation;
	}

	/**
	 * Set site
	 *
	 * @param \App\Entity\Site $site
	 *
	 * @return Sampling
	 */
	public function setSite(\App\Entity\Site $site = null) {
		$this->site = $site;
		return $this;
	}

	/**
	 * Get site
	 *
	 * @return \App\Entity\Site
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * Add method
	 *
	 * @param Voc $method
	 *
	 * @return Sampling
	 */
	public function addMethod(Voc $method) {
		$this->methods[] = $method;
		return $this;
	}

	/**
	 * Remove method
	 *
	 * @param Voc $method
	 */
	public function removeMethod(Voc $method) {
		$this->methods->removeElement($method);
	}

	/**
	 * Get methods
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getMethods() {
		return $this->methods;
	}

	/**
	 * Add fixative
	 *
	 * @param Voc $fixative
	 *
	 * @return Sampling
	 */
	public function addFixative(Voc $fixative) {
		$this->fixatives[] = $fixative;
		return $this;
	}

	/**
	 * Remove fixative
	 *
	 * @param Voc $fixative
	 */
	public function removeFixative(Voc $fixative) {
		$this->fixatives->removeElement($fixative);
	}

	/**
	 * Get fixatives
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getFixatives() {
		return $this->fixatives;
	}

	/**
	 * Add funding
	 *
	 * @param Program $funding
	 *
	 * @return Sampling
	 */
	public function addFunding(Program $funding) {
		$this->fundings[] = $funding;
		return $this;
	}

	/**
	 * Remove funding
	 *
	 * @param Program $funding
	 */
	public function removeFunding(Program $funding) {
		$this->fundings->removeElement($funding);
	}

	/**
	 * Get fundings
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getFundings() {
		return $this->fundings;
	}

	/**
	 * Add participant
	 *
	 * @param Person $participant
	 *
	 * @return Sampling
	 */
	public function addParticipant(Person $participant) {
		$this->participants[] = $participant;
		return $this;
	}

	/**
	 * Remove participant
	 *
	 * @param Person $participant
	 */
	public function removeParticipant(Person $participant) {
		$this->participants->removeElement($participant);
	}

	/**
	 * Get participants
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getParticipants() {
		return $this->participants;
	}

	/**
	 * Add taxon sampling
	 *
	 * @param Taxon$targetTaxon
	 *
	 * @return Sampling
	 */
	public function addTargetTaxon(Taxon $taxon) {
		$this->targetTaxons[] = $taxon;

		return $this;
	}

	/**
	 * Remove taxon sampling
	 *
	 * @param Taxon$targetTaxon
	 */
	public function removeTargetTaxon(Taxon $taxon) {
		$this->targetTaxons->removeElement($taxon);
	}

	/**
	 * Get targetTaxons
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTargetTaxons() {
		return $this->targetTaxons;
	}
}
