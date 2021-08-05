<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\TaxonSampling;

/**
 * Collecte
 *
 * @ORM\Table(name="sampling",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_sampling__sample_code", columns={"sample_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_55AE4A3DA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_55AE4A3D50BB334E", columns={"donation_voc_fk"}),
 *      @ORM\Index(name="IDX_55AE4A3D369AB36B", columns={"site_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeCollecte"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Collecte extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="sampling_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="sample_code", type="string", length=255, nullable=false)
   */
  private $codeCollecte;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="sampling_date", type="date", nullable=true)
   */
  private $dateCollecte;

  /**
   * @var integer
   *
   * @ORM\Column(name="sampling_duration", type="bigint", nullable=true)
   */
  private $dureeEchantillonnageMn;

  /**
   * @var float
   *
   * @ORM\Column(name="temperature", type="float", precision=10, scale=0, nullable=true)
   */
  private $temperatureC;

  /**
   * @var float
   *
   * @ORM\Column(name="specific_conductance", type="float", precision=10, scale=0, nullable=true)
   */
  private $conductiviteMicroSieCm;

  /**
   * @var integer
   *
   * @ORM\Column(name="sample_status", type="smallint", nullable=false)
   */
  private $aFaire;

  /**
   * @var string
   *
   * @ORM\Column(name="sampling_comments", type="text", nullable=true)
   */
  private $commentaireCollecte;

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
   *   @ORM\JoinColumn(name="donation_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $legVocFk;

  /**
   * @var \Station
   *
   * @ORM\ManyToOne(targetEntity="Station")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="site_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $stationFk;

  /**
   * @ORM\OneToMany(targetEntity="SamplingMethod", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingMethods;

  /**
   * @ORM\OneToMany(targetEntity="SamplingFixative", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingFixatives;

  /**
   * @ORM\OneToMany(targetEntity="SamplingFunding", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingFundings;

  /**
   * @ORM\OneToMany(targetEntity="SamplingParticipant", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingParticipants;

  /**
   * @ORM\OneToMany(targetEntity="TaxonSampling", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonSamplings;

  public function __construct() {
    $this->samplingMethods = new ArrayCollection();
    $this->samplingFixatives = new ArrayCollection();
    $this->samplingFundings = new ArrayCollection();
    $this->samplingParticipants = new ArrayCollection();
    $this->taxonSamplings = new ArrayCollection();
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
   * Set codeCollecte
   *
   * @param string $codeCollecte
   *
   * @return Collecte
   */
  public function setCodeCollecte($codeCollecte) {
    $this->codeCollecte = $codeCollecte;
    return $this;
  }

  /**
   * Get codeCollecte
   *
   * @return string
   */
  public function getCodeCollecte() {
    return $this->codeCollecte;
  }

  /**
   * Set dateCollecte
   *
   * @param \DateTime $dateCollecte
   *
   * @return Collecte
   */
  public function setDateCollecte($dateCollecte) {
    $this->dateCollecte = $dateCollecte;
    return $this;
  }

  /**
   * Get dateCollecte
   *
   * @return \DateTime
   */
  public function getDateCollecte() {
    return $this->dateCollecte;
  }

  /**
   * Set dureeEchantillonnageMn
   *
   * @param integer $dureeEchantillonnageMn
   *
   * @return Collecte
   */
  public function setDureeEchantillonnageMn($dureeEchantillonnageMn) {
    $this->dureeEchantillonnageMn = $dureeEchantillonnageMn;
    return $this;
  }

  /**
   * Get dureeEchantillonnageMn
   *
   * @return integer
   */
  public function getDureeEchantillonnageMn() {
    return $this->dureeEchantillonnageMn;
  }

  /**
   * Set temperatureC
   *
   * @param float $temperatureC
   *
   * @return Collecte
   */
  public function setTemperatureC($temperatureC) {
    $this->temperatureC = $temperatureC;
    return $this;
  }

  /**
   * Get temperatureC
   *
   * @return float
   */
  public function getTemperatureC() {
    return $this->temperatureC;
  }

  /**
   * Set conductiviteMicroSieCm
   *
   * @param float $conductiviteMicroSieCm
   *
   * @return Collecte
   */
  public function setConductiviteMicroSieCm($conductiviteMicroSieCm) {
    $this->conductiviteMicroSieCm = $conductiviteMicroSieCm;
    return $this;
  }

  /**
   * Get conductiviteMicroSieCm
   *
   * @return float
   */
  public function getConductiviteMicroSieCm() {
    return $this->conductiviteMicroSieCm;
  }

  /**
   * Set aFaire
   *
   * @param integer $aFaire
   *
   * @return Collecte
   */
  public function setAFaire($aFaire) {
    $this->aFaire = $aFaire;
    return $this;
  }

  /**
   * Get aFaire
   *
   * @return integer
   */
  public function getAFaire() {
    return $this->aFaire;
  }

  /**
   * Set commentaireCollecte
   *
   * @param string $commentaireCollecte
   *
   * @return Collecte
   */
  public function setCommentaireCollecte($commentaireCollecte) {
    $this->commentaireCollecte = $commentaireCollecte;
    return $this;
  }

  /**
   * Get commentaireCollecte
   *
   * @return string
   */
  public function getCommentaireCollecte() {
    return $this->commentaireCollecte;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return Collecte
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
   * Set legVocFk
   *
   * @param \App\Entity\Voc $legVocFk
   *
   * @return Collecte
   */
  public function setLegVocFk(\App\Entity\Voc $legVocFk = null) {
    $this->legVocFk = $legVocFk;
    return $this;
  }

  /**
   * Get legVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getLegVocFk() {
    return $this->legVocFk;
  }

  /**
   * Set stationFk
   *
   * @param \App\Entity\Station $stationFk
   *
   * @return Collecte
   */
  public function setStationFk(\App\Entity\Station $stationFk = null) {
    $this->stationFk = $stationFk;
    return $this;
  }

  /**
   * Get stationFk
   *
   * @return \App\Entity\Station
   */
  public function getStationFk() {
    return $this->stationFk;
  }

  /**
   * Add samplingMethod
   *
   * @param \App\Entity\SamplingMethod $samplingMethod
   *
   * @return Collecte
   */
  public function addSamplingMethod(\App\Entity\SamplingMethod $samplingMethod) {
    $samplingMethod->setCollecteFk($this);
    $this->samplingMethods[] = $samplingMethod;
    return $this;
  }

  /**
   * Remove samplingMethod
   *
   * @param \App\Entity\SamplingMethod $samplingMethod
   */
  public function removeSamplingMethod(\App\Entity\SamplingMethod $samplingMethod) {
    $this->samplingMethods->removeElement($samplingMethod);
  }

  /**
   * Get samplingMethods
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSamplingMethods() {
    return $this->samplingMethods;
  }

  /**
   * Add sampling fixative
   *
   * @param \App\Entity\SamplingFixative $fixative
   *
   * @return Collecte
   */
  public function addSamplingFixative(\App\Entity\SamplingFixative $samplingFixative) {
    $samplingFixative->setCollecteFk($this);
    $this->samplingFixatives[] = $samplingFixative;
    return $this;
  }

  /**
   * Remove sampling fixative
   *
   * @param \App\Entity\SamplingFixative $samplingFixative
   */
  public function removeSamplingFixative(\App\Entity\SamplingFixative $samplingFixative) {
    $this->samplingFixatives->removeElement($samplingFixative);
  }

  /**
   * Get sampling fixatives
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSamplingFixatives() {
    return $this->samplingFixatives;
  }

  /**
   * Add samplingFunding
   *
   * @param \App\Entity\SamplingFunding $samplingFunding
   *
   * @return Collecte
   */
  public function addSamplingFunding(\App\Entity\SamplingFunding $samplingFunding) {
    $samplingFunding->setCollecteFk($this);
    $this->samplingFundings[] = $samplingFunding;
    return $this;
  }

  /**
   * Remove samplingFunding
   *
   * @param \App\Entity\SamplingFunding $samplingFunding
   */
  public function removeSamplingFunding(\App\Entity\SamplingFunding $samplingFunding) {
    $this->samplingFundings->removeElement($samplingFunding);
  }

  /**
   * Get samplingFundings
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSamplingFundings() {
    return $this->samplingFundings;
  }

  /**
   * Add samplingParticipant
   *
   * @param \App\Entity\SamplingParticipant $samplingParticipant
   *
   * @return Collecte
   */
  public function addSamplingParticipant(\App\Entity\SamplingParticipant $samplingParticipant) {
    $samplingParticipant->setCollecteFk($this);
    $this->samplingParticipants[] = $samplingParticipant;
    return $this;
  }

  /**
   * Remove samplingParticipant
   *
   * @param \App\Entity\SamplingParticipant $samplingParticipant
   */
  public function removeSamplingParticipant(\App\Entity\SamplingParticipant $samplingParticipant) {
    $this->samplingParticipants->removeElement($samplingParticipant);
  }

  /**
   * Get samplingParticipants
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSamplingParticipants() {
    return $this->samplingParticipants;
  }

  /**
   * Add taxon sampling
   *
   * @param TaxonSampling $taxonSampling
   *
   * @return Collecte
   */
  public function addTaxonSampling(TaxonSampling $taxonSampling) {
    $taxonSampling->setCollecteFk($this);
    $this->taxonSamplings[] = $taxonSampling;

    return $this;
  }

  /**
   * Remove taxon sampling
   *
   * @param TaxonSampling $taxonSampling
   */
  public function removeTaxonSampling(TaxonSampling $taxonSampling) {
    $this->taxonSamplings->removeElement($taxonSampling);
  }

  /**
   * Get taxonSamplings
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonSamplings() {
    return $this->taxonSamplings;
  }
}
