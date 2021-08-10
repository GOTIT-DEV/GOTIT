<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Store
 *
 * @ORM\Table(name="storage_box",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_storage_box__box_code", columns={"box_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_7718EDEF9E7B0E1F", columns={"collection_type_voc_fk"}),
 *      @ORM\Index(name="IDX_7718EDEF41A72D48", columns={"collection_code_voc_fk"}),
 *      @ORM\Index(name="IDX_7718EDEF57552D30", columns={"box_type_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Store extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="storage_box_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="box_code", type="string", length=255, nullable=false)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="box_title", type="string", length=1024, nullable=false)
   */
  private $label;

  /**
   * @var string
   *
   * @ORM\Column(name="box_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="collection_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $collectionTypeVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="collection_code_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $collectionCodeVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="box_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $storageTypeVocFk;

  /**
   * @ORM\OneToMany(targetEntity="InternalLot", mappedBy="storeFk", cascade={"persist"})
   * @ORM\OrderBy({"code" = "ASC"})
   */
  protected $internalLots;

  /**
   * @ORM\OneToMany(targetEntity="Dna", mappedBy="storeFk", cascade={"persist"})
   * @ORM\OrderBy({"code" = "ASC"})
   */
  protected $dnas;

  /**
   * @ORM\OneToMany(targetEntity="Slide", mappedBy="storeFk", cascade={"persist"})
   * @ORM\OrderBy({"code" = "ASC"})
   */
  protected $slides;

  public function __construct() {
    $this->internalLots = new ArrayCollection();
    $this->dnas = new ArrayCollection();
    $this->slides = new ArrayCollection();
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
   * @return Store
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
   * Set label
   *
   * @param string $label
   *
   * @return Store
   */
  public function setLabel($label) {
    $this->label = $label;

    return $this;
  }

  /**
   * Get label
   *
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Store
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
   * Set collectionTypeVocFk
   *
   * @param \App\Entity\Voc $collectionTypeVocFk
   *
   * @return Store
   */
  public function setCollectionTypeVocFk(\App\Entity\Voc $collectionTypeVocFk = null) {
    $this->collectionTypeVocFk = $collectionTypeVocFk;

    return $this;
  }

  /**
   * Get collectionTypeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getCollectionTypeVocFk() {
    return $this->collectionTypeVocFk;
  }

  /**
   * Set collectionCodeVocFk
   *
   * @param \App\Entity\Voc $collectionCodeVocFk
   *
   * @return Store
   */
  public function setCollectionCodeVocFk(\App\Entity\Voc $collectionCodeVocFk = null) {
    $this->collectionCodeVocFk = $collectionCodeVocFk;

    return $this;
  }

  /**
   * Get collectionCodeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getCollectionCodeVocFk() {
    return $this->collectionCodeVocFk;
  }

  /**
   * Set storageTypeVocFk
   *
   * @param \App\Entity\Voc $storageTypeVocFk
   *
   * @return Store
   */
  public function setStorageTypeVocFk(\App\Entity\Voc $storageTypeVocFk = null) {
    $this->storageTypeVocFk = $storageTypeVocFk;

    return $this;
  }

  /**
   * Get storageTypeVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getStorageTypeVocFk() {
    return $this->storageTypeVocFk;
  }

  /**
   * Add internalLot
   *
   * @param \App\Entity\internalLot $internalLot
   *
   * @return Store
   */
  public function addInternalLot(\App\Entity\internalLot $internalLot) {
    $internalLot->setStoreFk($this);
    $this->internalLots[] = $internalLot;

    return $this;
  }

  /**
   * Remove internalLot
   *
   * @param \App\Entity\internalLot $internalLot
   */
  public function removeInternalLot(\App\Entity\internalLot $internalLot) {
    $this->internalLots->removeElement($internalLot);
  }

  /**
   * Get internalLots
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getInternalLots() {
    return $this->internalLots;
  }

  /**
   * Add dna
   *
   * @param \App\Entity\Dna $dna
   *
   * @return Store
   */
  public function addDna(\App\Entity\Dna $dna) {
    $dna->setStoreFk($this);
    $this->dnas[] = $dna;

    return $this;
  }

  /**
   * Remove dna
   *
   * @param \App\Entity\Dna $dna
   */
  public function removeDna(\App\Entity\Dna $dna) {
    $this->dnas->removeElement($dna);
  }

  /**
   * Get dnas
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDnas() {
    return $this->dnas;
  }

  /**
   * Add slide
   *
   * @param \App\Entity\Slide $slide
   *
   * @return Store
   */
  public function addSlide(\App\Entity\Slide $slide) {
    $slide->setStoreFk($this);
    $this->slides[] = $slide;

    return $this;
  }

  /**
   * Remove slide
   *
   * @param \App\Entity\Slide $slide
   */
  public function removeSlide(\App\Entity\Slide $slide) {
    $this->slides->removeElement($slide);
  }

  /**
   * Get slides
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSlides() {
    return $this->slides;
  }
}
