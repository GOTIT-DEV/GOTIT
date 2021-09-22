<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Specimen
 *
 * @ORM\Table(name="specimen",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_specimen__specimen_morphological_code", columns={"specimen_morphological_code"}),
 *      @ORM\UniqueConstraint(name="uk_specimen__specimen_molecular_code", columns={"specimen_molecular_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_5EE42FCE4236D33E", columns={"specimen_type_voc_fk"}),
 *      @ORM\Index(name="IDX_5EE42FCE54DBBD4D", columns={"internal_biological_material_fk"})
 * })
 * @ORM\Entity
 * @UniqueEntity(fields={"molecularCode"}, message="This code is already registered")
 * @UniqueEntity(fields={"morphologicalCode"}, message="This code is already registered")
 *
 * @ApiResource
 *
 */
class Specimen extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="specimen_id_seq ", allocationSize=1, initialValue=1)
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_molecular_code", type="string", length=255, nullable=true, unique=true)
   * @Groups({"item"})
   */
  private $molecularCode;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_morphological_code", type="string", length=255, nullable=false, unique=true)
   * @Groups({"item"})
   */
  private $morphologicalCode;

  /**
   * @var string
   *
   * @ORM\Column(name="tube_code", type="string", length=255, nullable=false)
   * @Groups({"item"})
   */
  private $tubeCode;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_molecular_number", type="string", length=255, nullable=true)
   * @Groups({"item"})
   */
  private $molecularNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_comments", type="text", nullable=true)
   * @Groups({"item"})
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $specimenTypeVocFk;

  /**
   * @var \InternalLot
   *
   * @ORM\ManyToOne(targetEntity="InternalLot")
   * @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false)
   * @Groups({"specimen:list", "specimen:item"})
   */
  private $internalLotFk;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="specimenFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   * @Groups({"specimen:list", "specimen:item"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->taxonIdentifications = new ArrayCollection();
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
   * Set molecularCode
   *
   * @param string $molecularCode
   *
   * @return Specimen
   */
  public function setMolecularCode($molecularCode) {
    $this->molecularCode = $molecularCode;

    return $this;
  }

  /**
   * Get molecularCode
   *
   * @return string
   */
  public function getMolecularCode() {
    return $this->molecularCode;
  }

  /**
   * Set morphologicalCode
   *
   * @param string $morphologicalCode
   *
   * @return Specimen
   */
  public function setMorphologicalCode($morphologicalCode) {
    $this->morphologicalCode = $morphologicalCode;

    return $this;
  }

  /**
   * Get morphologicalCode
   *
   * @return string
   */
  public function getMorphologicalCode() {
    return $this->morphologicalCode;
  }

  /**
   * Set tubeCode
   *
   * @param string $tubeCode
   *
   * @return Specimen
   */
  public function setTubeCode($tubeCode) {
    $this->tubeCode = $tubeCode;

    return $this;
  }

  /**
   * Get tubeCode
   *
   * @return string
   */
  public function getTubeCode() {
    return $this->tubeCode;
  }

  /**
   * Set molecularNumber
   *
   * @param string $molecularNumber
   *
   * @return Specimen
   */
  public function setMolecularNumber($molecularNumber) {
    $this->molecularNumber = $molecularNumber;

    return $this;
  }

  /**
   * Get molecularNumber
   *
   * @return string
   */
  public function getMolecularNumber() {
    return $this->molecularNumber;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Specimen
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
   * Set specimenTypeVocFk
   *
   * @param \App\Entity\Voc $specimenTypeVocFk
   *
   * @return Specimen
   */
  public function setSpecimenTypeVocFk(\App\Entity\Voc $specimenTypeVocFk = null) {
    $this->specimenTypeVocFk = $specimenTypeVocFk;

    return $this;
  }

  /**
   * Get specimenTypeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getSpecimenTypeVocFk() {
    return $this->specimenTypeVocFk;
  }

  /**
   * Set internalLotFk
   *
   * @param \App\Entity\InternalLot $internalLotFk
   *
   * @return Specimen
   */
  public function setInternalLotFk(\App\Entity\InternalLot $internalLotFk = null) {
    $this->internalLotFk = $internalLotFk;

    return $this;
  }

  /**
   * Get internalLotFk
   *
   * @return \App\Entity\InternalLot
   */
  public function getInternalLotFk() {
    return $this->internalLotFk;
  }

  /**
   * Add taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   *
   * @return Specimen
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setSpecimenFk($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   */
  public function removeTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $this->taxonIdentifications->removeElement($taxonIdentification);
  }

  /**
   * Get taxonIdentifications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonIdentifications() {
    return $this->taxonIdentifications;
  }
}
