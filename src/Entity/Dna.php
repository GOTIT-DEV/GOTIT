<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
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
 *
 * @ApiResource(
 *     itemOperations={
 *       "get"={
 *          "normalization_context"={"groups"={"item","dna:item"}}
 *       },
 *       "delete"
 *     },
 *     collectionOperations={
 *       "get"={
 *          "normalization_context"={"groups"={"item", "dna:list"}}
 *       },
 *       "post"
 *     },
 *     order={"code"="ASC"},
 *     paginationEnabled=true
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *  "code":"partial",
 *  "specimenFk.molecularCode":"partial",
 *  "specimenFk.morphologicalCode":"partial",
 *  "storeFk.code": "partial",
 *  "datePrecisionVocFk.code":"partial",
 *  "extractionMethodVocFk.code":"partial",
 *  "quality.code":"partial"
 * })
 * @ApiFilter(DateFilter::class, properties={
 *  "date": "DateFilter::EXCLUDE_NULL"
 * })
 * @ApiFilter(PropertyFilter::class)
 */
class Dna extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="dna_id_seq", allocationSize=1, initialValue=1)
   * @ApiProperty(identifier=false)
   * @Groups({"item"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_code", type="string", length=255, nullable=false, unique=true)
   * @Groups({"item"})
   * @ApiProperty(identifier=true)
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
   * @Groups({"item"})
   */
  private $date;

  /**
   * @var float
   *
   * @ORM\Column(name="dna_concentration", type="float", precision=10, scale=0, nullable=true)
   * @Groups({"item"})
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
   * @Groups({"dna:list", "dna:item"})
   */
  private $datePrecisionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_extraction_method_voc_fk", referencedColumnName="id", nullable=false))
   * @Groups({"dna:list", "dna:item"})
   */
  private $extractionMethodVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_quality_voc_fk", referencedColumnName="id", nullable=false)
   * @Groups({"dna:list", "dna:item"})
   */
  private $quality;

  /**
   * @var \Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=false)
   * @ORM\OrderBy({"molecularCode" = "ASC"})
   * @Groups({"dna:list", "dna:item"})
   */
  private $specimenFk;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="dnas", fetch="EAGER")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * @Groups({"dna:list", "dna:item"})
   */
  private $storeFk;

  /**
   * @ORM\OneToMany(targetEntity="DnaProducer", mappedBy="dnaFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   * @Groups({"dna:list", "dna:item"})
   */
  protected $dnaProducers;

  /**
   * @var Pcr
   * @ORM\OneToMany(targetEntity="Pcr", mappedBy="dnaFk", fetch="EXTRA_LAZY")
   * @Groups({"dna:list", "dna:item"})
   */
  protected $pcrs;

  public function __construct() {
    $this->dnaProducers = new ArrayCollection();
  }

  /**
   * @Groups({"dna:list", "dna:item"})
   *
   * @return array
   */
  public function getMetadata(): array{
    return parent::getMetadata();
  }

  /**
   * Get id
   *
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * Set code
   *
   * @param string $code
   *
   * @return Dna
   */
  public function setCode($code): Dna {
    $this->code = $code;
    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode(): ?string {
    return $this->code;
  }

  /**
   * Set date
   *
   * @param \DateTime $date
   *
   * @return Dna
   */
  public function setDate($date): Dna {
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
  public function getDate(): ?DateTime {
    return $this->date;
  }

  /**
   * Set concentrationNgMicrolitre
   *
   * @param float $concentrationNgMicrolitre
   *
   * @return Dna
   */
  public function setConcentrationNgMicrolitre($concentrationNgMicrolitre): Dna {
    $this->concentrationNgMicrolitre = $concentrationNgMicrolitre;
    return $this;
  }

  /**
   * Get concentrationNgMicrolitre
   *
   * @return float
   */
  public function getConcentrationNgMicrolitre(): ?float {
    return $this->concentrationNgMicrolitre;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Dna
   */
  public function setComment($comment): Dna {
    $this->comment = $comment;
    return $this;
  }

  /**
   * Get comment
   *
   * @return string
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param Voc $datePrecisionVocFk
   *
   * @return Dna
   */
  public function setDatePrecisionVocFk(Voc $datePrecisionVocFk = null): Dna {
    $this->datePrecisionVocFk = $datePrecisionVocFk;
    return $this;
  }

  /**
   * Get datePrecisionVocFk
   *
   * @return Voc
   */
  public function getDatePrecisionVocFk(): ?Voc {
    return $this->datePrecisionVocFk;
  }

  /**
   * Set extractionMethodVocFk
   *
   * @param Voc $extractionMethodVocFk
   *
   * @return Dna
   */
  public function setExtractionMethodVocFk(Voc $extractionMethodVocFk = null): Dna {
    $this->extractionMethodVocFk = $extractionMethodVocFk;
    return $this;
  }

  /**
   * Get extractionMethodVocFk
   *
   * @return Voc
   */
  public function getExtractionMethodVocFk(): ?Voc {
    return $this->extractionMethodVocFk;
  }

  /**
   * Set specimenFk
   *
   * @param Specimen $specimenFk
   *
   * @return Dna
   */
  public function setSpecimenFk(Specimen $specimenFk = null): Dna {
    $this->specimenFk = $specimenFk;
    return $this;
  }

  /**
   * Get specimenFk
   *
   * @return Specimen
   */
  public function getSpecimenFk(): ?Specimen {
    return $this->specimenFk;
  }

  /**
   * Set quality
   *
   * @param Voc $quality
   *
   * @return Dna
   */
  public function setQuality(Voc $quality = null): Dna {
    $this->quality = $quality;
    return $this;
  }

  /**
   * Get quality
   *
   * @return Voc
   */
  public function getQuality(): ?Voc {
    return $this->quality;
  }

  /**
   * Set storeFk
   *
   * @param Store $storeFk
   *
   * @return Dna
   */
  public function setStoreFk(Store $storeFk = null): Dna {
    $this->storeFk = $storeFk;
    return $this;
  }

  /**
   * Get storeFk
   *
   * @return Store
   */
  public function getStoreFk(): ?Store {
    return $this->storeFk;
  }

  /**
   * Add dnaProducer
   *
   * @param DnaProducer $dnaProducer
   *
   * @return Dna
   */
  public function addDnaProducer(DnaProducer $dnaProducer): Dna {
    $dnaProducer->setDnaFk($this);
    $this->dnaProducers[] = $dnaProducer;
    return $this;
  }

  /**
   * Remove dnaProducer
   *
   * @param DnaProducer $dnaProducer
   */
  public function removeDnaProducer(DnaProducer $dnaProducer): Dna {
    $this->dnaProducers->removeElement($dnaProducer);
    return $this;
  }

  /**
   * Get dnaProducers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getDnaProducers(): Collection {
    return $this->dnaProducers;
  }
}
