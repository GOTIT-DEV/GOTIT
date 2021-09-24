<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ExternalLot
 *
 * @ORM\Table(name="external_biological_material",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_external_biological_material__external_biological_material_c", columns={"external_biological_material_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_EEFA43F3662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F382ACDC4", columns={"number_of_specimens_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3B0B56B73", columns={"pigmentation_voc_fk"}),
 *      @ORM\Index(name="IDX_EEFA43F3A897CC9E", columns={"eyes_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalLot extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="external_biological_material_id_seq", allocationSize=1, initialValue=1)
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="external_biological_material_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="external_biological_material_creation_date", type="date", nullable=true)
	 */
	private $creationDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="external_biological_material_comments", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="number_of_specimens_comments", type="text", nullable=true)
	 */
	private $specimenQuantityComment;

	/**
	 * @var Sampling
	 *
	 * @ORM\ManyToOne(targetEntity="Sampling")
	 * @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
	 */
	private $sampling;

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
	 * @ORM\JoinColumn(name="number_of_specimens_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $specimenQuantity;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $pigmentation;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $eyes;

	/**
	 * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
	 * @ORM\JoinTable(name="external_biological_material_is_processed_by",
	 *  joinColumns={@ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $producers;

	/**
	 * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
	 * @ORM\JoinTable(name="external_biological_material_is_published_in",
	 *  joinColumns={@ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="source_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $publications;

	/**
	 * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="externalLot", cascade={"persist"})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $taxonIdentifications;

	public function __construct() {
		$this->producers = new ArrayCollection();
		$this->publications = new ArrayCollection();
		$this->taxonIdentifications = new ArrayCollection();
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
	 * @return ExternalLot
	 */
	public function setCode($code): ExternalLot {
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

	/**
	 * Set creationDate
	 *
	 * @param \DateTime $creationDate
	 *
	 * @return ExternalLot
	 */
	public function setCreationDate($date): ExternalLot {
		$this->creationDate = $date;
		return $this;
	}

	/**
	 * Get creationDate
	 *
	 * @return \DateTime
	 */
	public function getCreationDate(): ?\DateTime {
		return $this->creationDate;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return ExternalLot
	 */
	public function setComment($comment): ExternalLot {
		$this->comment = $comment;
		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment(): ?string {
		return $this->comment;
	}

	/**
	 * Set specimenQuantityComment
	 *
	 * @param string $specimenQuantityComment
	 *
	 * @return ExternalLot
	 */
	public function setSpecimenQuantityComment(
		$specimenQuantityComment,
	): ExternalLot {
		$this->specimenQuantityComment = $specimenQuantityComment;
		return $this;
	}

	/**
	 * Get specimenQuantityComment
	 *
	 * @return string
	 */
	public function getSpecimenQuantityComment(): ?string {
		return $this->specimenQuantityComment;
	}

	/**
	 * Set sampling
	 *
	 * @param Sampling $sampling
	 *
	 * @return ExternalLot
	 */
	public function setSampling(Sampling $sampling = null): ExternalLot {
		$this->sampling = $sampling;
		return $this;
	}

	/**
	 * Get sampling
	 *
	 * @return Sampling
	 */
	public function getSampling(): ?Sampling {
		return $this->sampling;
	}

	/**
	 * Set datePrecision
	 *
	 * @param Voc $datePrecision
	 *
	 * @return ExternalLot
	 */
	public function setDatePrecision(Voc $datePrecVocFk = null): ExternalLot {
		$this->datePrecision = $datePrecVocFk;
		return $this;
	}

	/**
	 * Get datePrecision
	 *
	 * @return Voc
	 */
	public function getDatePrecision(): ?Voc {
		return $this->datePrecision;
	}

	/**
	 * Set specimenQuantity
	 *
	 * @param Voc $specimenQuantity
	 *
	 * @return ExternalLot
	 */
	public function setSpecimenQuantity(Voc $specQtyVocFk = null): ExternalLot {
		$this->specimenQuantity = $specQtyVocFk;
		return $this;
	}

	/**
	 * Get specimenQuantity
	 *
	 * @return Voc
	 */
	public function getSpecimenQuantity(): ?Voc {
		return $this->specimenQuantity;
	}

	/**
	 * Set pigmentation
	 *
	 * @param Voc $pigmentation
	 *
	 * @return ExternalLot
	 */
	public function setPigmentation(Voc $pigmVocFk = null): ExternalLot {
		$this->pigmentation = $pigmVocFk;
		return $this;
	}

	/**
	 * Get pigmentation
	 *
	 * @return Voc
	 */
	public function getPigmentation(): ?Voc {
		return $this->pigmentation;
	}

	/**
	 * Set eyes
	 *
	 * @param Voc $eyes
	 *
	 * @return ExternalLot
	 */
	public function setEyes(Voc $eyes = null): ExternalLot {
		$this->eyes = $eyes;
		return $this;
	}

	/**
	 * Get eyes
	 *
	 * @return Voc
	 */
	public function getEyes(): ?Voc {
		return $this->eyes;
	}

	/**
	 * Add producer
	 *
	 * @param Person $producer
	 *
	 * @return ExternalLot
	 */
	public function addProducer(Person $producer): ExternalLot {
		$this->producers[] = $producer;
		return $this;
	}

	/**
	 * Remove producer
	 *
	 * @param Person $producer
	 */
	public function removeProducer(Person $producer): ExternalLot {
		$this->producers->removeElement($producer);
		return $this;
	}

	/**
	 * Get producers
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getProducers(): Collection {
		return $this->producers;
	}

	/**
	 * Add publication
	 *
	 * @param Source $publication
	 *
	 * @return ExternalLot
	 */
	public function addPublication(Source $pub): ExternalLot {
		$this->publications[] = $pub;
		return $this;
	}

	/**
	 * Remove publication
	 *
	 * @param Source $publication
	 */
	public function removePublication(Source $pub): ExternalLot {
		$this->publications->removeElement($pub);
		return $this;
	}

	/**
	 * Get publications
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getPublications(): Collection {
		return $this->publications;
	}

	/**
	 * Add taxonIdentification
	 *
	 * @param TaxonIdentification $taxonIdentification
	 *
	 * @return ExternalLot
	 */
	public function addTaxonIdentification(
		TaxonIdentification $taxonId,
	): ExternalLot {
		$taxonId->setExternalLot($this);
		$this->taxonIdentifications[] = $taxonId;
		return $this;
	}

	/**
	 * Remove taxonIdentification
	 *
	 * @param TaxonIdentification $taxonIdentification
	 */
	public function removeTaxonIdentification(
		TaxonIdentification $taxonId,
	): ExternalLot {
		$this->taxonIdentifications->removeElement($taxonId);
		return $this;
	}

	/**
	 * Get taxonIdentifications
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTaxonIdentifications(): Collection {
		return $this->taxonIdentifications;
	}
}
