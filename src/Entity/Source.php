<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Source
 *
 * @ORM\Table(name="source",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_source__source_code", columns={"source_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Source extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="source_id_seq", allocationSize=1, initialValue=1)
	 */
	private int $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="source_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="source_year", type="integer", nullable=true)
	 */
	private $year;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="source_title", type="string", length=2048, nullable=false)
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="source_comments", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
	 * @ORM\JoinTable(name="source_is_entered_by",
	 *  joinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $providers;

	public function __construct() {
		$this->providers = new ArrayCollection();
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
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Source
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
	 * Set year
	 *
	 * @param integer $year
	 *
	 * @return Source
	 */
	public function setYear($year) {
		$this->year = $year;

		return $this;
	}

	/**
	 * Get year
	 *
	 * @return integer
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Source
	 */
	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Source
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
	 * Add provider
	 *
	 * @param Person $provider
	 *
	 * @return Source
	 */
	public function addProvider(Person $provider) {
		$this->providers[] = $provider;
		return $this;
	}

	/**
	 * Remove provider
	 *
	 * @param Person $provider
	 */
	public function removeProvider(Person $provider) {
		$this->providers->removeElement($provider);
	}

	/**
	 * Get providers
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getProviders() {
		return $this->providers;
	}
}
