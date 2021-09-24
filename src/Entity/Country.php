<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Country
 *
 * @ORM\Table(name="country",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_country__country_code", columns={"country_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="Country code {{ value }} is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Country extends AbstractTimestampedEntity {
	use CompositeCodeEntityTrait;

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
	 * @ORM\Column(name="country_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="country_name", type="string", length=1024, nullable=false)
	 */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity="Municipality", mappedBy="country")
	 * @ORM\OrderBy({"code" = "asc"})
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
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Country
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

	private function _generateCode(): string {
		return str_replace(" ", "_", strtoupper($this->getName()));
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Country
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

	public function getMunicipalities() {
		return $this->municipalities;
	}
}
