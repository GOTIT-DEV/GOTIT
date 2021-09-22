<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A DNA sample extracted from a Specimen.
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
 *  "specimen.molecularCode":"partial",
 *  "specimen.morphologicalCode":"partial",
 *  "store.code": "partial",
 *  "datePrecision.code":"partial",
 *  "extractionMethod.code":"partial",
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
  private $datePrecision;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_extraction_method_voc_fk", referencedColumnName="id", nullable=false))
   * @Groups({"dna:list", "dna:item"})
   */
  private $extractionMethod;

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
  private $specimen;

  /**
   * @var \Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="dnas", fetch="EAGER")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * @Groups({"dna:list", "dna:item"})
   */
  private $store;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="dna_is_extracted_by",
   *  joinColumns={@ORM\JoinColumn(name="dna_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id" = "ASC"})
   * @Groups({"dna:list", "dna:item"})
   * @Assert\Count(min = 1, minMessage = "At least one person is required as producer")
   */
  protected $producers;

  /**
   * @var Pcr
   * @ORM\OneToMany(targetEntity="Pcr", mappedBy="dna", fetch="EXTRA_LAZY")
   * @Groups({"dna:list", "dna:item"})
   */
  protected $pcrs;

  public function __construct() {
    $this->producers = new ArrayCollection();
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
   * Set datePrecision
   *
   * @param Voc $datePrecision
   *
   * @return Dna
   */
  public function setDatePrecision(Voc $datePrecision = null): Dna {
    $this->datePrecision = $datePrecision;
    return $this;
  }

  /**
   * Get datePrecision
   *
   * @return Voc
   */
  public function getDatePrecision(): ?Voc {
    return $this->datePrecision;
  }

  /**
   * Set extractionMethod
   *
   * @param Voc $extractionMethod
   *
   * @return Dna
   */
  public function setExtractionMethod(Voc $extractionMethod = null): Dna {
    $this->extractionMethod = $extractionMethod;
    return $this;
  }

  /**
   * Get extractionMethod
   *
   * @return Voc
   */
  public function getExtractionMethod(): ?Voc {
    return $this->extractionMethod;
  }

  /**
   * Set specimen
   *
   * @param Specimen $specimen
   *
   * @return Dna
   */
  public function setSpecimen(Specimen $specimen = null): Dna {
    $this->specimen = $specimen;
    return $this;
  }

  /**
   * Get specimen
   *
   * @return Specimen
   */
  public function getSpecimen(): ?Specimen {
    return $this->specimen;
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
   * Set store
   *
   * @param Store $store
   *
   * @return Dna
   */
  public function setStore(Store $store = null): Dna {
    $this->store = $store;
    return $this;
  }

  /**
   * Get store
   *
   * @return Store
   */
  public function getStore(): ?Store {
    return $this->store;
  }

  /**
   * Add producer
   *
   * @param Person $producer
   *
   * @return Dna
   */
  public function addProducer(Person $producer): Dna {
    $this->producers[] = $producer;
    return $this;
  }

  /**
   * Remove producer
   *
   * @param Person $producer
   */
  public function removeProducer(Person $producer): Dna {
    $this->producers->removeElement($producer);
    return $this;
  }

  /**
   * Get producers
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProducers(): Collection {
    return $this->producers;
  }
}
