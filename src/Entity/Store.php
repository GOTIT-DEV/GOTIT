<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

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
 * @ApiResource
 */
class Store extends AbstractTimestampedEntity {
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="storage_box_id_seq", allocationSize=1, initialValue=1)
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="box_code", type="string", length=255, nullable=false)
   * @Groups({"item"})
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="box_title", type="string", length=1024, nullable=false)
   * @Groups({"item"})
   */
  private $label;

  /**
   * @var string
   *
   * @ORM\Column(name="box_comments", type="text", nullable=true)
   * @Groups({"item"})
   */
  private $comment;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="collection_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $collectionType;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="collection_code_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $collectionCode;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="box_type_voc_fk", referencedColumnName="id", nullable=false)
   */
  private $storageType;

  /**
   * @ORM\OneToMany(targetEntity="InternalLot", mappedBy="store", cascade={"persist"})
   * @ORM\OrderBy({"code": "ASC"})
   * @Groups({"store_list", "store_details"})
   */
  protected $internalLots;

  /**
   * @ORM\OneToMany(targetEntity="Dna", mappedBy="store", cascade={"persist"})
   * @ORM\OrderBy({"code": "ASC"})
   * @Groups({"store_list", "store_details"})
   */
  protected $dnas;

  /**
   * @ORM\OneToMany(targetEntity="Slide", mappedBy="store", cascade={"persist"})
   * @ORM\OrderBy({"code": "ASC"})
   * @Groups({"store_list", "store_details"})
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
   * @return int
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
   * Set collectionType
   *
   * @param Voc $collectionType
   *
   * @return Store
   */
  public function setCollectionType(Voc $collectionType = null) {
    $this->collectionType = $collectionType;

    return $this;
  }

  /**
   * Get collectionType
   *
   * @return Voc
   */
  public function getCollectionType() {
    return $this->collectionType;
  }

  /**
   * Set collectionCode
   *
   * @param Voc $collectionCode
   *
   * @return Store
   */
  public function setCollectionCode(Voc $collectionCode = null) {
    $this->collectionCode = $collectionCode;

    return $this;
  }

  /**
   * Get collectionCode
   *
   * @return Voc
   */
  public function getCollectionCode() {
    return $this->collectionCode;
  }

  /**
   * Set storageType
   *
   * @param Voc $storageType
   *
   * @return Store
   */
  public function setStorageType(Voc $storageType = null) {
    $this->storageType = $storageType;

    return $this;
  }

  /**
   * Get storageType
   *
   * @return Voc
   */
  public function getStorageType() {
    return $this->storageType;
  }

  /**
   * Add internalLot
   *
   * @return Store
   */
  public function addInternalLot(internalLot $internalLot) {
    $internalLot->setStore($this);
    $this->internalLots[] = $internalLot;

    return $this;
  }

  /**
   * Remove internalLot
   */
  public function removeInternalLot(internalLot $internalLot) {
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
   * @return Store
   */
  public function addDna(Dna $dna) {
    $dna->setStore($this);
    $this->dnas[] = $dna;

    return $this;
  }

  /**
   * Remove dna
   */
  public function removeDna(Dna $dna) {
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
   * @return Store
   */
  public function addSlide(Slide $slide) {
    $slide->setStore($this);
    $this->slides[] = $slide;

    return $this;
  }

  /**
   * Remove slide
   */
  public function removeSlide(Slide $slide) {
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
