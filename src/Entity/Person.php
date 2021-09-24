<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Person
 *
 * @ORM\Table(name="person",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_person__person_name", columns={"person_name"})},
 *  indexes={@ORM\Index(name="IDX_FCEC9EFE8441376", columns={"institution_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"name"}, message="A person with this name is already registered")
 * @ApiResource
 */
class Person extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="person_id_seq", allocationSize=1, initialValue=1)
	 * @Groups({"item"})
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="person_name", type="string", length=255, nullable=false, unique=true)
	 * @Groups({"item"})
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="person_full_name", type="string", length=1024, nullable=true)
	 * @Groups({"item"})
	 */
	private $fullName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="person_name_bis", type="string", length=255, nullable=true)
	 * @Groups({"item"})
	 */
	private $alias;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="person_comments", type="text", nullable=true)
	 * @Groups({"item"})
	 */
	private $comment;

	/**
	 * @var Institution
	 *
	 * @ORM\ManyToOne(targetEntity="Institution")
	 * @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=true)
	 */
	private $institution;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Person
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
	 * Set fullName
	 *
	 * @param string $fullName
	 *
	 * @return Person
	 */
	public function setFullName($fullName) {
		$this->fullName = $fullName;

		return $this;
	}

	/**
	 * Get fullName
	 *
	 * @return string
	 */
	public function getFullName() {
		return $this->fullName;
	}

	/**
	 * Set alias
	 *
	 * @param string $alias
	 *
	 * @return Person
	 */
	public function setAlias($alias) {
		$this->alias = $alias;

		return $this;
	}

	/**
	 * Get alias
	 *
	 * @return string
	 */
	public function getAlias() {
		return $this->alias;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Person
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
	 * Set institution
	 *
	 * @param \App\Entity\Institution $institution
	 *
	 * @return Person
	 */
	public function setInstitution(\App\Entity\Institution $institution = null) {
		$this->institution = $institution;

		return $this;
	}

	/**
	 * Get institution
	 *
	 * @return \App\Entity\Institution
	 */
	public function getInstitution() {
		return $this->institution;
	}
}
