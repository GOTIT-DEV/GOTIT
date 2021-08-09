<?php

namespace App\Entity;

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
 * @UniqueEntity(fields={"codeLameColl"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Slide extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="specimen_slide_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="collection_slide_code", type="string", length=255, nullable=false)
   */
  private $codeLameColl;

  /**
   * @var string
   *
   * @ORM\Column(name="slide_title", type="string", length=1024, nullable=false)
   */
  private $libelleLame;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="slide_date", type="date", nullable=true)
   */
  private $dateLame;

  /**
   * @var string
   *
   * @ORM\Column(name="photo_folder_name", type="string", length=1024, nullable=true)
   */
  private $nomDossierPhotos;

  /**
   * @var string
   *
   * @ORM\Column(name="slide_comments", type="text", nullable=true)
   */
  private $comment;

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
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="slides")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $storeFk;

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
   * @ORM\OneToMany(targetEntity="SlideProducer", mappedBy="slideFk", cascade={"persist"})
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
   * Set codeLameColl
   *
   * @param string $codeLameColl
   *
   * @return Slide
   */
  public function setCodeLameColl($codeLameColl) {
    $this->codeLameColl = $codeLameColl;

    return $this;
  }

  /**
   * Get codeLameColl
   *
   * @return string
   */
  public function getCodeLameColl() {
    return $this->codeLameColl;
  }

  /**
   * Set libelleLame
   *
   * @param string $libelleLame
   *
   * @return Slide
   */
  public function setLibelleLame($libelleLame) {
    $this->libelleLame = $libelleLame;

    return $this;
  }

  /**
   * Get libelleLame
   *
   * @return string
   */
  public function getLibelleLame() {
    return $this->libelleLame;
  }

  /**
   * Set dateLame
   *
   * @param \DateTime $dateLame
   *
   * @return Slide
   */
  public function setDateLame($dateLame) {
    $this->dateLame = $dateLame;

    return $this;
  }

  /**
   * Get dateLame
   *
   * @return \DateTime
   */
  public function getDateLame() {
    return $this->dateLame;
  }

  /**
   * Set nomDossierPhotos
   *
   * @param string $nomDossierPhotos
   *
   * @return Slide
   */
  public function setNomDossierPhotos($nomDossierPhotos) {
    $this->nomDossierPhotos = $nomDossierPhotos;

    return $this;
  }

  /**
   * Get nomDossierPhotos
   *
   * @return string
   */
  public function getNomDossierPhotos() {
    return $this->nomDossierPhotos;
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
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return Slide
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
   * Set storeFk
   *
   * @param \App\Entity\Store $storeFk
   *
   * @return Slide
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
   * Set specimenFk
   *
   * @param \App\Entity\Specimen $specimenFk
   *
   * @return Slide
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
   * Add producer
   *
   * @param \App\Entity\SlideProducer $producer
   *
   * @return Slide
   */
  public function addProducer(\App\Entity\SlideProducer $producer) {
    $producer->setSlideFk($this);
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   *
   * @param \App\Entity\SlideProducer $producer
   */
  public function removeProducer(\App\Entity\SlideProducer $producer) {
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
