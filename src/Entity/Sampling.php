<?php

namespace App\Entity;

use App\Entity\TaxonSampling;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Sampling
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
class Sampling extends AbstractTimestampedEntity {
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
   * @var \Site
   *
   * @ORM\ManyToOne(targetEntity="Site")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="site_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $siteFk;

  /**
   * @ORM\OneToMany(targetEntity="SamplingMethod", mappedBy="samplingFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingMethods;

  /**
   * @ORM\OneToMany(targetEntity="SamplingFixative", mappedBy="samplingFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingFixatives;

  /**
   * @ORM\OneToMany(targetEntity="SamplingFunding", mappedBy="samplingFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingFundings;

  /**
   * @ORM\OneToMany(targetEntity="SamplingParticipant", mappedBy="samplingFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $samplingParticipants;

  /**
   * @ORM\OneToMany(targetEntity="TaxonSampling", mappedBy="samplingFk", cascade={"persist"})
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * @return Sampling
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
   * Set siteFk
   *
   * @param \App\Entity\Site $siteFk
   *
   * @return Sampling
   */
  public function setSiteFk(\App\Entity\Site $siteFk = null) {
    $this->siteFk = $siteFk;
    return $this;
  }

  /**
   * Get siteFk
   *
   * @return \App\Entity\Site
   */
  public function getSiteFk() {
    return $this->siteFk;
  }

  /**
   * Add samplingMethod
   *
   * @param \App\Entity\SamplingMethod $samplingMethod
   *
   * @return Sampling
   */
  public function addSamplingMethod(\App\Entity\SamplingMethod $samplingMethod) {
    $samplingMethod->setSamplingFk($this);
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
   * @return Sampling
   */
  public function addSamplingFixative(\App\Entity\SamplingFixative $samplingFixative) {
    $samplingFixative->setSamplingFk($this);
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
   * @return Sampling
   */
  public function addSamplingFunding(\App\Entity\SamplingFunding $samplingFunding) {
    $samplingFunding->setSamplingFk($this);
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
   * @return Sampling
   */
  public function addSamplingParticipant(\App\Entity\SamplingParticipant $samplingParticipant) {
    $samplingParticipant->setSamplingFk($this);
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
   * @return Sampling
   */
  public function addTaxonSampling(TaxonSampling $taxonSampling) {
    $taxonSampling->setSamplingFk($this);
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
