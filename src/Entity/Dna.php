<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Dna
 *
 * @ORM\Table(name="dna",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_dna__dna_code", columns={"dna_code"})},
 *  indexes={
 *      @ORM\Index(name="adn_code_adn", columns={"dna_code"}),
 *      @ORM\Index(name="idx_dna__date_precision_voc_fk", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="idx_dna__specimen_fk", columns={"specimen_fk"}),
 *      @ORM\Index(name="idx_dna__dna_extraction_method_voc_fk", columns={"dna_extraction_method_voc_fk"}),
 *      @ORM\Index(name="idx_dna__storage_box_fk", columns={"storage_box_fk"}),
 *      @ORM\Index(name="IDX_1DCF9AF9C53B46B", columns={"dna_quality_voc_fk"}) })
 * @ORM\Entity
 * @UniqueEntity(fields={"codeAdn"}, message="This code is already registered")
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
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_code", type="string", length=255, nullable=false)
   */
  private $codeAdn;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="dna_extraction_date", type="date", nullable=true)
   */
  private $dateAdn;

  /**
   * @var float
   *
   * @ORM\Column(name="dna_concentration", type="float", precision=10, scale=0, nullable=true)
   */
  private $concentrationNgMicrolitre;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_comments", type="text", nullable=true)
   */
  private $commentaireAdn;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_extraction_method_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $methodeExtractionAdnVocFk;

  /**
   * @var \Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $specimenFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_quality_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $qualiteAdnVocFk;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="adns")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $storeFk;

  /**
   * @ORM\OneToMany(targetEntity="DnaExtraction", mappedBy="adnFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $dnaExtractions;

  public function __construct() {
    $this->dnaExtractions = new ArrayCollection();
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
   * Set codeAdn
   *
   * @param string $codeAdn
   *
   * @return Dna
   */
  public function setCodeAdn($codeAdn) {
    $this->codeAdn = $codeAdn;

    return $this;
  }

  /**
   * Get codeAdn
   *
   * @return string
   */
  public function getCodeAdn() {
    return $this->codeAdn;
  }

  /**
   * Set dateAdn
   *
   * @param \DateTime $dateAdn
   *
   * @return Dna
   */
  public function setDateAdn($dateAdn) {
    $this->dateAdn = $dateAdn;

    return $this;
  }

  /**
   * Get dateAdn
   *
   * @return \DateTime
   */
  public function getDateAdn() {
    return $this->dateAdn;
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
   * Set commentaireAdn
   *
   * @param string $commentaireAdn
   *
   * @return Dna
   */
  public function setCommentaireAdn($commentaireAdn) {
    $this->commentaireAdn = $commentaireAdn;

    return $this;
  }

  /**
   * Get commentaireAdn
   *
   * @return string
   */
  public function getCommentaireAdn() {
    return $this->commentaireAdn;
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
   * Set methodeExtractionAdnVocFk
   *
   * @param \App\Entity\Voc $methodeExtractionAdnVocFk
   *
   * @return Dna
   */
  public function setMethodeExtractionAdnVocFk(\App\Entity\Voc $methodeExtractionAdnVocFk = null) {
    $this->methodeExtractionAdnVocFk = $methodeExtractionAdnVocFk;

    return $this;
  }

  /**
   * Get methodeExtractionAdnVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getMethodeExtractionAdnVocFk() {
    return $this->methodeExtractionAdnVocFk;
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
   * Add dnaExtraction
   *
   * @param \App\Entity\DnaExtraction $dnaExtraction
   *
   * @return Dna
   */
  public function addDnaExtraction(\App\Entity\DnaExtraction $dnaExtraction) {
    $dnaExtraction->setAdnFk($this);
    $this->dnaExtractions[] = $dnaExtraction;

    return $this;
  }

  /**
   * Remove dnaExtraction
   *
   * @param \App\Entity\DnaExtraction $dnaExtraction
   */
  public function removeDnaExtraction(\App\Entity\DnaExtraction $dnaExtraction) {
    $this->dnaExtractions->removeElement($dnaExtraction);
  }

  /**
   * Get dnaExtractions
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDnaExtractions() {
    return $this->dnaExtractions;
  }
}
