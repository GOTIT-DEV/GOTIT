<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Slide
 *
 * @ORM\Table(name="specimen_slide",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_specimen_slide__collection_slide_code", columns={"collection_slide_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_8DA827E2A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_8DA827E22B644673", columns={"storage_box_fk"}),
 *      @ORM\Index(name="IDX_8DA827E25F2C6176", columns={"specimen_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Slide extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="specimen_slide_id_seq", allocationSize=1, initialValue=1)
	 */
	private int $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="collection_slide_code", type="string", length=255, nullable=false, unique=true)
	 */
	private $code;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slide_title", type="string", length=1024, nullable=false)
	 */
	private $label;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="slide_date", type="date", nullable=true)
	 */
	private $date;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="photo_folder_name", type="string", length=1024, nullable=true)
	 */
	private $pictureFolder;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="slide_comments", type="text", nullable=true)
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
	 * @var Store
	 *
	 * @ORM\ManyToOne(targetEntity="Store", inversedBy="slides")
	 * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
	 */
	private $store;

	/**
	 * @var Specimen
	 *
	 * @ORM\ManyToOne(targetEntity="Specimen")
	 * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=false)
	 */
	private $specimen;

	/**
	 * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
	 * @ORM\JoinTable(name="slide_is_mounted_by",
	 *  joinColumns={@ORM\JoinColumn(name="specimen_slide_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $producers;

	public function __construct() {
		$this->producers = new ArrayCollection();
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
	 * @return Slide
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
	 * Set label
	 *
	 * @param string $label
	 *
	 * @return Slide
	 */
	public function setLabel($label) {
		$this->label = $label;

		return $this;
	}

	/**
	 * Get label
	 *
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 *
	 * @return Slide
	 */
	public function setDate($date) {
		$this->date = $date;

		return $this;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Set pictureFolder
	 *
	 * @param string $pictureFolder
	 *
	 * @return Slide
	 */
	public function setPictureFolder($pictureFolder) {
		$this->pictureFolder = $pictureFolder;

		return $this;
	}

	/**
	 * Get pictureFolder
	 *
	 * @return string
	 */
	public function getPictureFolder() {
		return $this->pictureFolder;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return Slide
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
	 * @param Voc $datePrecision
	 *
	 * @return Slide
	 */
	public function setDatePrecision(Voc $datePrecision = null) {
		$this->datePrecision = $datePrecision;

		return $this;
	}

	/**
	 * Get datePrecision
	 *
	 * @return Voc
	 */
	public function getDatePrecision() {
		return $this->datePrecision;
	}

	/**
	 * Set store
	 *
	 * @param Store $store
	 *
	 * @return Slide
	 */
	public function setStore(Store $store = null) {
		$this->store = $store;
		return $this;
	}

	/**
	 * Get store
	 *
	 * @return Store
	 */
	public function getStore() {
		return $this->store;
	}

	/**
	 * Set specimen
	 *
	 * @param Specimen $specimen
	 *
	 * @return Slide
	 */
	public function setSpecimen(Specimen $specimen = null) {
		$this->specimen = $specimen;

		return $this;
	}

	/**
	 * Get specimen
	 *
	 * @return Specimen
	 */
	public function getSpecimen() {
		return $this->specimen;
	}

	/**
	 * Add producer
	 *
	 * @param Person $producer
	 *
	 * @return Slide
	 */
	public function addProducer(Person $producer) {
		$this->producers[] = $producer;
		return $this;
	}

	/**
	 * Remove producer
	 *
	 * @param Person $producer
	 */
	public function removeProducer(Person $producer) {
		$this->producers->removeElement($producer);
	}

	/**
	 * Get producers
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getProducers() {
		return $this->producers;
	}
}
