<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Program
 *
 * @ORM\Table(name="program",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_program__program_code", columns={"program_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Program extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="program_id_seq", allocationSize=1, initialValue=1)
	 */
	private int $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="program_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="program_name", type="string", length=1024, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="coordinator_names", type="text", nullable=false)
	 */
	private $coordinators;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="funding_agency", type="string", length=1024, nullable=true)
	 */
	private $fundingAgency;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="starting_year", type="integer", nullable=true)
	 */
	private $startYear;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="ending_year", type="integer", nullable=true)
	 */
	private $endYear;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="program_comments", type="text", nullable=true)
	 */
	private $comment;

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
	 * @return Program
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
	 * @return Program
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
	 * Set coordinators
	 *
	 * @param string $coordinators
	 *
	 * @return Program
	 */
	public function setCoordinators($coordinators) {
		$this->coordinators = $coordinators;

		return $this;
	}

	/**
	 * Get coordinators
	 *
	 * @return string
	 */
	public function getCoordinators() {
		return $this->coordinators;
	}

	/**
	 * Set fundingAgency
	 *
	 * @param string $fundingAgency
	 *
	 * @return Program
	 */
	public function setFundingAgency($fundingAgency) {
		$this->fundingAgency = $fundingAgency;

		return $this;
	}

	/**
	 * Get fundingAgency
	 *
	 * @return string
	 */
	public function getFundingAgency() {
		return $this->fundingAgency;
	}

	/**
	 * Set startYear
	 *
	 * @param integer $startYear
	 *
	 * @return Program
	 */
	public function setStartYear($startYear) {
		$this->startYear = $startYear;

		return $this;
	}

	/**
	 * Get startYear
	 *
	 * @return integer
	 */
	public function getStartYear() {
		return $this->startYear;
	}

	/**
	 * Set endYear
	 *
	 * @param integer $endYear
	 *
	 * @return Program
	 */
	public function setEndYear($endYear) {
		$this->endYear = $endYear;

		return $this;
	}

	/**
	 * Get endYear
	 *
	 * @return integer
	 */
	public function getEndYear() {
		return $this->endYear;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Program
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
}
