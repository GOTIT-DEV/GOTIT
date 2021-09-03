<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DNA
 *
 * @ORM\Table(name="dna",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_dna__dna_code", columns={"dna_code"})},
 *  indexes={
 *      @ORM\Index(name="dna_code_adn", columns={"dna_code"}),
 *      @ORM\Index(name="idx_dna__date_precision_voc_fk", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="idx_dna__specimen_fk", columns={"specimen_fk"}),
 *      @ORM\Index(name="idx_dna__dna_extraction_method_voc_fk", columns={"dna_extraction_method_voc_fk"}),
 *      @ORM\Index(name="idx_dna__storage_box_fk", columns={"storage_box_fk"}),
 *      @ORM\Index(name="IDX_1DCF9AF9C53B46B", columns={"dna_quality_voc_fk"}) })
 * @ORM\Entity(repositoryClass="App\Repository\DnaRepository")
 * @UniqueEntity(fields={"code"}, message="Code {{ value }} is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Dna extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="dna_id_seq", allocationSize=1, initialValue=1)
   * @Groups({"field"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_code", type="string", length=255, nullable=false)
   * @Groups({"field"})
   * @Assert\Regex(
   *  pattern="/^[\w]+$/",
   *  message="Code {{ value }} contains invalid special characters"
   * )
   */
  private $code;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="dna_extraction_date", type="date", nullable=true)
   * @Groups({"field"})
   */
  private $date;

  /**
   * @var float
   *
   * @ORM\Column(name="dna_concentration", type="float", precision=10, scale=0, nullable=true)
   * @Groups({"field"})
   * @Assert\PositiveOrZero
   */
  private $concentrationNgMicrolitre;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * @Groups({"dna_list", "dna_details"})
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_extraction_method_voc_fk", referencedColumnName="id", nullable=false))
   * @Groups({"dna_list", "dna_details"})
   */
  private $extractionMethodVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_quality_voc_fk", referencedColumnName="id", nullable=false)
   * @Groups({"dna_list", "dna_details"})
   */
  private $qualiteAdnVocFk;

  /**
   * @var \Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=false)
   * @ORM\OrderBy({"molecularCode" = "ASC"})
   * @Groups({"dna_list", "dna_details"})
   */
  private $specimenFk;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="dnas", fetch="EAGER")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * @Groups({"dna_list", "dna_details"})
   */
  private $storeFk;

  /**
   * @ORM\OneToMany(targetEntity="DnaProducer", mappedBy="dnaFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   * @Groups({"dna_list", "dna_details"})
   */
  protected $dnaProducers;

  /**
   * @var Pcr
   * @ORM\OneToMany(targetEntity="Pcr", mappedBy="dnaFk", fetch="EXTRA_LAZY")
   * @Groups({"dna_list", "dna_details"})
   */
  protected $pcrs;

  public function __construct() {
    $this->dnaProducers = new ArrayCollection();
  }

  /**
   * @VirtualProperty()
   * @SerializedName("_meta")
   * @Groups({"dna_list", "dna_details"})
   *
   * @return array
   */
  public function getMetadata() {
    return parent::getMetadata();
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
   * @return Dna
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
   * Set date
   *
   * @param \DateTime $date
   *
   * @return Dna
   */
  public function setDate($date) {
    if (is_string($date)) {
      $date = new DateTime($date);
    }

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
   * Set concentrationNgMicrolitre
   *
   * @param float $concentrationNgMicrolitre
   *
   * @return Dna
   */
  public function setConcentrationNgMicrolitre($concentrationNgMicrolitre) {
    $this->concentrationNgMicrolitre = $concentrationNgMicrolitre;

    return $this;
  }

  /**
   * Get concentrationNgMicrolitre
   *
   * @return float
   */
  public function getConcentrationNgMicrolitre() {
    return $this->concentrationNgMicrolitre;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Dna
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
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return Dna
   */
  public function setDatePrecisionVocFk(\App\Entity\Voc $datePrecisionVocFk = null) {
    $this->datePrecisionVocFk = $datePrecisionVocFk;

    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getDatePrecisionVocFk() {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set extractionMethodVocFk
   *
   * @param \App\Entity\Voc $extractionMethodVocFk
   *
   * @return Dna
   */
  public function setExtractionMethodVocFk(\App\Entity\Voc $extractionMethodVocFk = null) {
    $this->extractionMethodVocFk = $extractionMethodVocFk;

    return $this;
  }

  /**
   * Get extractionMethodVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getExtractionMethodVocFk() {
    return $this->extractionMethodVocFk;
  }

  /**
   * Set specimenFk
   *
   * @param \App\Entity\Specimen $specimenFk
   *
   * @return Dna
   */
  public function setSpecimenFk(\App\Entity\Specimen $specimenFk = null) {
    $this->specimenFk = $specimenFk;

    return $this;
  }

  /**
   * Get specimenFk
   *
   * @return \App\Entity\Specimen
   */
  public function getSpecimenFk() {
    return $this->specimenFk;
  }

  /**
   * Set qualiteAdnVocFk
   *
   * @param \App\Entity\Voc $qualiteAdnVocFk
   *
   * @return Dna
   */
  public function setQualiteAdnVocFk(\App\Entity\Voc $qualiteAdnVocFk = null) {
    $this->qualiteAdnVocFk = $qualiteAdnVocFk;

    return $this;
  }

  /**
   * Get qualiteAdnVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getQualiteAdnVocFk() {
    return $this->qualiteAdnVocFk;
  }

  /**
   * Set storeFk
   *
   * @param \App\Entity\Store $storeFk
   *
   * @return Dna
   */
  public function setStoreFk(\App\Entity\Store $storeFk = null) {
    $this->storeFk = $storeFk;

    return $this;
  }

  /**
   * Get storeFk
   *
   * @return \App\Entity\Store
   */
  public function getStoreFk() {
    return $this->storeFk;
  }

  /**
   * Add dnaProducer
   *
   * @param \App\Entity\DnaProducer $dnaProducer
   *
   * @return Dna
   */
  public function addDnaProducer(\App\Entity\DnaProducer $dnaProducer) {
    $dnaProducer->setDnaFk($this);
    $this->dnaProducers[] = $dnaProducer;

    return $this;
  }

  /**
   * Remove dnaProducer
   *
   * @param \App\Entity\DnaProducer $dnaProducer
   */
  public function removeDnaProducer(\App\Entity\DnaProducer $dnaProducer) {
    $this->dnaProducers->removeElement($dnaProducer);
  }

  /**
   * Get dnaProducers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDnaProducers() {
    return $this->dnaProducers;
  }
}
