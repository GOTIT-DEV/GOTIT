<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * LotMateriel
 *
 * @ORM\Table(name="internal_biological_material",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_internal_biological_material__internal_biological_material_c", columns={"internal_biological_material_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_BA1841A5A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5B0B56B73", columns={"pigmentation_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5A897CC9E", columns={"eyes_voc_fk"}),
 *      @ORM\Index(name="IDX_BA1841A5662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_BA1841A52B644673", columns={"storage_box_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeLotMateriel"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class LotMateriel extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_biological_material_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_code", type="string", length=255, nullable=false)
   */
  private $codeLotMateriel;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="internal_biological_material_date", type="date", nullable=true)
   */
  private $dateLotMateriel;

  /**
   * @var string
   *
   * @ORM\Column(name="sequencing_advice", type="text", nullable=true)
   */
  private $commentaireConseilSqc;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_biological_material_comments", type="text", nullable=true)
   */
  private $commentaireLotMateriel;

  /**
   * @var integer
   *
   * @ORM\Column(name="internal_biological_material_status", type="smallint", nullable=false)
   */
  private $aFaire;

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
   *   @ORM\JoinColumn(name="pigmentation_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $pigmentationVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="eyes_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $yeuxVocFk;

  /**
   * @var \Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $collecteFk;

  /**
   * @var \Boite
   *
   * @ORM\ManyToOne(targetEntity="Boite", inversedBy="lotMateriels")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $boiteFk;

  /**
   * @ORM\OneToMany(targetEntity="LotMaterielEstRealisePar", mappedBy="lotMaterielFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $lotMaterielEstRealisePars;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotPublication", mappedBy="lotMaterielFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="lotMaterielFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="CompositionLotMateriel", mappedBy="lotMaterielFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $compositionLotMateriels;

  public function __construct() {
    $this->lotMaterielEstRealisePars = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->compositionLotMateriels = new ArrayCollection();
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
   * Set codeLotMateriel
   *
   * @param string $codeLotMateriel
   *
   * @return LotMateriel
   */
  public function setCodeLotMateriel($codeLotMateriel) {
    $this->codeLotMateriel = $codeLotMateriel;

    return $this;
  }

  /**
   * Get codeLotMateriel
   *
   * @return string
   */
  public function getCodeLotMateriel() {
    return $this->codeLotMateriel;
  }

  /**
   * Set dateLotMateriel
   *
   * @param \DateTime $dateLotMateriel
   *
   * @return LotMateriel
   */
  public function setDateLotMateriel($dateLotMateriel) {
    $this->dateLotMateriel = $dateLotMateriel;

    return $this;
  }

  /**
   * Get dateLotMateriel
   *
   * @return \DateTime
   */
  public function getDateLotMateriel() {
    return $this->dateLotMateriel;
  }

  /**
   * Set commentaireConseilSqc
   *
   * @param string $commentaireConseilSqc
   *
   * @return LotMateriel
   */
  public function setCommentaireConseilSqc($commentaireConseilSqc) {
    $this->commentaireConseilSqc = $commentaireConseilSqc;

    return $this;
  }

  /**
   * Get commentaireConseilSqc
   *
   * @return string
   */
  public function getCommentaireConseilSqc() {
    return $this->commentaireConseilSqc;
  }

  /**
   * Set commentaireLotMateriel
   *
   * @param string $commentaireLotMateriel
   *
   * @return LotMateriel
   */
  public function setCommentaireLotMateriel($commentaireLotMateriel) {
    $this->commentaireLotMateriel = $commentaireLotMateriel;

    return $this;
  }

  /**
   * Get commentaireLotMateriel
   *
   * @return string
   */
  public function getCommentaireLotMateriel() {
    return $this->commentaireLotMateriel;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return LotMateriel
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
   * Set pigmentationVocFk
   *
   * @param \App\Entity\Voc $pigmentationVocFk
   *
   * @return LotMateriel
   */
  public function setPigmentationVocFk(\App\Entity\Voc $pigmentationVocFk = null) {
    $this->pigmentationVocFk = $pigmentationVocFk;

    return $this;
  }

  /**
   * Get pigmentationVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getPigmentationVocFk() {
    return $this->pigmentationVocFk;
  }

  /**
   * Set yeuxVocFk
   *
   * @param \App\Entity\Voc $yeuxVocFk
   *
   * @return LotMateriel
   */
  public function setYeuxVocFk(\App\Entity\Voc $yeuxVocFk = null) {
    $this->yeuxVocFk = $yeuxVocFk;

    return $this;
  }

  /**
   * Get yeuxVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getYeuxVocFk() {
    return $this->yeuxVocFk;
  }

  /**
   * Set collecteFk
   *
   * @param \App\Entity\Collecte $collecteFk
   *
   * @return LotMateriel
   */
  public function setCollecteFk(\App\Entity\Collecte $collecteFk = null) {
    $this->collecteFk = $collecteFk;

    return $this;
  }

  /**
   * Get collecteFk
   *
   * @return \App\Entity\Collecte
   */
  public function getCollecteFk() {
    return $this->collecteFk;
  }

  /**
   * Set boiteFk
   *
   * @param \App\Entity\Boite $boiteFk
   *
   * @return LotMateriel
   */
  public function setBoiteFk(\App\Entity\Boite $boiteFk = null) {
    $this->boiteFk = $boiteFk;

    return $this;
  }

  /**
   * Get boiteFk
   *
   * @return \App\Entity\Boite
   */
  public function getBoiteFk() {
    return $this->boiteFk;
  }

  /**
   * Add lotMaterielEstRealisePar
   *
   * @param \App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
   *
   * @return LotMateriel
   */
  public function addLotMaterielEstRealisePar(\App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar) {
    $lotMaterielEstRealisePar->setLotMaterielFk($this);
    $this->lotMaterielEstRealisePars[] = $lotMaterielEstRealisePar;

    return $this;
  }

  /**
   * Remove lotMaterielEstRealisePar
   *
   * @param \App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar
   */
  public function removeLotMaterielEstRealisePar(\App\Entity\LotMaterielEstRealisePar $lotMaterielEstRealisePar) {
    $this->lotMaterielEstRealisePars->removeElement($lotMaterielEstRealisePar);
  }

  /**
   * Get lotMaterielEstRealisePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getLotMaterielEstRealisePars() {
    return $this->lotMaterielEstRealisePars;
  }

  /**
   * Add publication
   *
   * @param \App\Entity\InternalLotPublication $publication
   *
   * @return LotMateriel
   */
  public function addPublication(\App\Entity\InternalLotPublication $publication) {

    $publication->setLotMaterielFk($this);
    $this->publications[] = $publication;

    return $this;
  }

  /**
   * Remove publication
   *
   * @param \App\Entity\InternalLotPublication $publication
   */
  public function removePublication(\App\Entity\InternalLotPublication $publication) {
    $this->publications->removeElement($publication);
  }

  /**
   * Get publications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPublications() {
    return $this->publications;
  }

  /**
   * Add taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   *
   * @return LotMateriel
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setLotMaterielFk($this);
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

  /**
   * Add compositionLotMateriel
   *
   * @param \App\Entity\CompositionLotMateriel $compositionLotMateriel
   *
   * @return LotMateriel
   */
  public function addCompositionLotMateriel(\App\Entity\CompositionLotMateriel $compositionLotMateriel) {
    $compositionLotMateriel->setLotMaterielFk($this);
    $this->compositionLotMateriels[] = $compositionLotMateriel;

    return $this;
  }

  /**
   * Remove compositionLotMateriel
   *
   * @param \App\Entity\CompositionLotMateriel $compositionLotMateriel
   */
  public function removeCompositionLotMateriel(\App\Entity\CompositionLotMateriel $compositionLotMateriel) {
    $this->compositionLotMateriels->removeElement($compositionLotMateriel);
  }

  /**
   * Get compositionLotMateriels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getCompositionLotMateriels() {
    return $this->compositionLotMateriels;
  }

  /**
   * Set aFaire
   *
   * @param integer $aFaire
   *
   * @return LotMateriel
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
}
