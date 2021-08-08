<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * InternalLot
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
class InternalLot extends AbstractTimestampedEntity {
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
   * @ORM\ManyToOne(targetEntity="Boite", inversedBy="internalLots")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $boiteFk;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotProducer", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $producers;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotPublication", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $publications;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  /**
   * @ORM\OneToMany(targetEntity="InternalLotContent", mappedBy="internalLotFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $contents;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->publications = new ArrayCollection();
    $this->taxonIdentifications = new ArrayCollection();
    $this->contents = new ArrayCollection();
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * @return InternalLot
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
   * Add producer
   *
   * @param \App\Entity\InternalLotProducer $producer
   *
   * @return InternalLot
   */
  public function addProducer(\App\Entity\InternalLotProducer $producer) {
    $producer->setInternalLotFk($this);
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   *
   * @param \App\Entity\InternalLotProducer $producer
   */
  public function removeProducer(\App\Entity\InternalLotProducer $producer) {
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

  /**
   * Add publication
   *
   * @param \App\Entity\InternalLotPublication $publication
   *
   * @return InternalLot
   */
  public function addPublication(\App\Entity\InternalLotPublication $publication) {

    $publication->setInternalLotFk($this);
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
   * @return InternalLot
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setInternalLotFk($this);
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
   * Add content
   *
   * @param \App\Entity\InternalLotContent $content
   *
   * @return InternalLot
   */
  public function addContent(\App\Entity\InternalLotContent $content) {
    $content->setInternalLotFk($this);
    $this->contents[] = $content;

    return $this;
  }

  /**
   * Remove content
   *
   * @param \App\Entity\InternalLotContent $content
   */
  public function removeContent(\App\Entity\InternalLotContent $content) {
    $this->contents->removeElement($content);
  }

  /**
   * Get contents
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getContents() {
    return $this->contents;
  }

  /**
   * Set aFaire
   *
   * @param integer $aFaire
   *
   * @return InternalLot
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
