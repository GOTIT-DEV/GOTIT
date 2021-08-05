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
   * @ORM\OneToMany(targetEntity="APourSamplingMethod", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $aPourSamplingMethods;

  /**
   * @ORM\OneToMany(targetEntity="APourFixateur", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $aPourFixateurs;

  /**
   * @ORM\OneToMany(targetEntity="EstFinancePar", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $estFinancePars;

  /**
   * @ORM\OneToMany(targetEntity="EstEffectuePar", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $estEffectuePars;

  /**
   * @ORM\OneToMany(targetEntity="TaxonSampling", mappedBy="collecteFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonSamplings;

  public function __construct() {
    $this->aPourSamplingMethods = new ArrayCollection();
    $this->aPourFixateurs = new ArrayCollection();
    $this->estFinancePars = new ArrayCollection();
    $this->estEffectuePars = new ArrayCollection();
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
   * Add aPourSamplingMethod
   *
   * @param \App\Entity\APourSamplingMethod $aPourSamplingMethod
   *
   * @return Collecte
   */
  public function addAPourSamplingMethod(\App\Entity\APourSamplingMethod $aPourSamplingMethod) {
    $aPourSamplingMethod->setCollecteFk($this);
    $this->aPourSamplingMethods[] = $aPourSamplingMethod;

    return $this;
  }

  /**
   * Remove aPourSamplingMethod
   *
   * @param \App\Entity\APourSamplingMethod $aPourSamplingMethod
   */
  public function removeAPourSamplingMethod(\App\Entity\APourSamplingMethod $aPourSamplingMethod) {
    $this->aPourSamplingMethods->removeElement($aPourSamplingMethod);
  }

  /**
   * Get aPourSamplingMethods
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAPourSamplingMethods() {
    return $this->aPourSamplingMethods;
  }

  /**
   * Add aPourFixateur
   *
   * @param \App\Entity\PourFixateur $aPourFixateur
   *
   * @return Collecte
   */
  public function addAPourFixateur(\App\Entity\APourFixateur $aPourFixateur) {
    $aPourFixateur->setCollecteFk($this);
    $this->aPourFixateurs[] = $aPourFixateur;

    return $this;
  }

  /**
   * Remove aPourFixateur
   *
   * @param \App\Entity\PourFixateur $aPourFixateur
   */
  public function removeAPourFixateur(\App\Entity\APourFixateur $aPourFixateur) {
    $this->aPourFixateurs->removeElement($aPourFixateur);
  }

  /**
   * Get aPourFixateurs
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAPourFixateurs() {
    return $this->aPourFixateurs;
  }

  /**
   * Add estFinancePar
   *
   * @param \App\Entity\EstFinancePar $estFinancePar
   *
   * @return Collecte
   */
  public function addEstFinancePar(\App\Entity\EstFinancePar $estFinancePar) {
    $estFinancePar->setCollecteFk($this);
    $this->estFinancePars[] = $estFinancePar;

    return $this;
  }

  /**
   * Remove estFinancePar
   *
   * @param \App\Entity\EstFinancePar $estFinancePar
   */
  public function removeEstFinancePar(\App\Entity\EstFinancePar $estFinancePar) {
    $this->estFinancePars->removeElement($estFinancePar);
  }

  /**
   * Get estFinancePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEstFinancePars() {
    return $this->estFinancePars;
  }

  /**
   * Add estEffectuePar
   *
   * @param \App\Entity\EstEffectuePar $estEffectuePar
   *
   * @return Collecte
   */
  public function addEstEffectuePar(\App\Entity\EstEffectuePar $estEffectuePar) {
    $estEffectuePar->setCollecteFk($this);
    $this->estEffectuePars[] = $estEffectuePar;

    return $this;
  }

  /**
   * Remove estEffectuePar
   *
   * @param \App\Entity\EstEffectuePar $estEffectuePar
   */
  public function removeEstEffectuePar(\App\Entity\EstEffectuePar $estEffectuePar) {
    $this->estEffectuePars->removeElement($estEffectuePar);
  }

  /**
   * Get estEffectuePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEstEffectuePars() {
    return $this->estEffectuePars;
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
