<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TaxonIdentification
 *
 * @ORM\Table(name="identified_species",
 *  indexes={
 *      @ORM\Index(name="IDX_801C3911B669F53D", columns={"type_material_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DFB5F790", columns={"identification_criterion_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_49D19C8DCDD1F756", columns={"external_sequence_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D40D80ECD", columns={"external_biological_material_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D54DBBD4D", columns={"internal_biological_material_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D7B09E3BC", columns={"taxon_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D5F2C6176", columns={"specimen_fk"}),
 *      @ORM\Index(name="IDX_49D19C8D5BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class TaxonIdentification extends AbstractTimestampedEntity {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="bigint", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @ORM\SequenceGenerator(sequenceName="identified_species_id_seq", allocationSize=1, initialValue=1)
	 */
	private $id;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="identification_date", type="date", nullable=true)
	 */
	private $identificationDate;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="identified_species_comments", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="type_material_voc_fk", referencedColumnName="id", nullable=true)
	 */
	private $materialType;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="identification_criterion_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $identificationCriterion;

	/**
	 * @var Voc
	 *
	 * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
	 * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
	 */
	private $datePrecision;

	/**
	 * @var ExternalSequence
	 *
	 * @ORM\ManyToOne(targetEntity="ExternalSequence", inversedBy="taxonIdentifications")
	 * @ORM\JoinColumn(name="external_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $externalSequence;

	/**
	 * @var ExternalLot
	 *
	 * @ORM\ManyToOne(targetEntity="ExternalLot", inversedBy="taxonIdentifications")
	 * @ORM\JoinColumn(name="external_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $externalLot;

	/**
	 * @var InternalLot
	 *
	 * @ORM\ManyToOne(targetEntity="InternalLot", inversedBy="taxonIdentifications")
	 * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $internalLot;

	/**
	 * @var Taxon
	 *
	 * @ORM\ManyToOne(targetEntity="Taxon")
	 * @ORM\JoinColumn(name="taxon_fk", referencedColumnName="id", nullable=false)
	 */
	private $taxon;

	/**
	 * @var Specimen
	 *
	 * @ORM\ManyToOne(targetEntity="Specimen", inversedBy="taxonIdentifications")
	 * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $specimen;

	/**
	 * @var InternalSequence
	 *
	 * @ORM\ManyToOne(targetEntity="InternalSequence", inversedBy="taxonIdentifications")
	 * @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=true, onDelete="CASCADE")
	 */
	private $internalSequence;

	/**
	 * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
	 * @ORM\JoinTable(name="species_is_identified_by",
	 *  joinColumns={@ORM\JoinColumn(name="identified_species_fk", referencedColumnName="id")},
	 *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
	 * @ORM\OrderBy({"id" = "ASC"})
	 */
	protected $curators;

	public function __construct() {
		$this->curators = new ArrayCollection();
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
	 * Set identificationDate
	 *
	 * @param \DateTime $identificationDate
	 *
	 * @return TaxonIdentification
	 */
	public function setIdentificationDate($identificationDate) {
		$this->identificationDate = $identificationDate;

		return $this;
	}

	/**
	 * Get identificationDate
	 *
	 * @return \DateTime
	 */
	public function getIdentificationDate() {
		return $this->identificationDate;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return TaxonIdentification
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
	 * Set identificationCriterion
	 *
	 * @param Voc $identificationCriterion
	 *
	 * @return TaxonIdentification
	 */
	public function setIdentificationCriterion(
		Voc $identificationCriterion = null,
	) {
		$this->identificationCriterion = $identificationCriterion;

		return $this;
	}

	/**
	 * Get identificationCriterion
	 *
	 * @return Voc
	 */
	public function getIdentificationCriterion() {
		return $this->identificationCriterion;
	}

	/**
	 * Set datePrecision
	 *
	 * @param Voc $datePrecision
	 *
	 * @return TaxonIdentification
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
	 * Set externalSequence
	 *
	 * @param ExternalSequence $externalSequence
	 *
	 * @return TaxonIdentification
	 */
	public function setExternalSequence(
		ExternalSequence $externalSequence = null,
	) {
		$this->externalSequence = $externalSequence;

		return $this;
	}

	/**
	 * Get externalSequence
	 *
	 * @return ExternalSequence
	 */
	public function getExternalSequence() {
		return $this->externalSequence;
	}

	/**
	 * Set externalLot
	 *
	 * @param ExternalLot $externalLot
	 *
	 * @return TaxonIdentification
	 */
	public function setExternalLot(ExternalLot $externalLot = null) {
		$this->externalLot = $externalLot;

		return $this;
	}

	/**
	 * Get externalLot
	 *
	 * @return ExternalLot
	 */
	public function getExternalLot() {
		return $this->externalLot;
	}

	/**
	 * Set internalLot
	 *
	 * @param InternalLot $internalLot
	 *
	 * @return TaxonIdentification
	 */
	public function setInternalLot(InternalLot $internalLot = null) {
		$this->internalLot = $internalLot;

		return $this;
	}

	/**
	 * Get internalLot
	 *
	 * @return InternalLot
	 */
	public function getInternalLot() {
		return $this->internalLot;
	}

	/**
	 * Set taxon
	 *
	 * @param Taxon $taxon
	 *
	 * @return TaxonIdentification
	 */
	public function setTaxon(Taxon $taxon = null) {
		$this->taxon = $taxon;

		return $this;
	}

	/**
	 * Get taxon
	 *
	 * @return Taxon
	 */
	public function getTaxon() {
		return $this->taxon;
	}

	/**
	 * Set specimen
	 *
	 * @param Specimen $specimen
	 *
	 * @return TaxonIdentification
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
	 * Set internalSequence
	 *
	 * @param InternalSequence $internalSequence
	 *
	 * @return TaxonIdentification
	 */
	public function setInternalSequence(
		InternalSequence $internalSequence = null,
	) {
		$this->internalSequence = $internalSequence;

		return $this;
	}

	/**
	 * Get internalSequence
	 *
	 * @return InternalSequence
	 */
	public function getInternalSequence() {
		return $this->internalSequence;
	}

	/**
	 * Add curator
	 *
	 * @param Person $curator
	 *
	 * @return TaxonIdentification
	 */
	public function addCurator(Person $curator) {
		$this->curators[] = $curator;
		return $this;
	}

	/**
	 * Remove curator
	 *
	 * @param Person $curator
	 */
	public function removeCurator(Person $curator) {
		$this->curators->removeElement($curator);
	}

	/**
	 * Get curators
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCurators() {
		return $this->curators;
	}

	/**
	 * Set materialType
	 *
	 * @param Voc $materialType
	 *
	 * @return TaxonIdentification
	 */
	public function setMaterialType(Voc $materialType = null) {
		$this->materialType = $materialType;

		return $this;
	}

	/**
	 * Get materialType
	 *
	 * @return Voc
	 */
	public function getMaterialType() {
		return $this->materialType;
	}
}
