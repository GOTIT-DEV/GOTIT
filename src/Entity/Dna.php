<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Controller\API\ImportCSVAction;
use App\DTO\CsvRecordsRequest;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Filter\OrSearchFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A DNA sample extracted from a Specimen
 *
 * @ORM\Table(name="dna",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_dna__dna_code", columns={"dna_code"})},
 *  indexes={
 *      @ORM\Index(name="dna_code_adn", columns={"dna_code"}),
 *      @ORM\Index(name="idx_dna__date_precision_voc_fk", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="idx_dna__specimen_fk", columns={"specimen_fk"}),
 *      @ORM\Index(name="idx_dna__dna_extraction_method_voc_fk", columns={"dna_extraction_method_voc_fk"}),
 *      @ORM\Index(name="idx_dna__storage_box_fk", columns={"storage_box_fk"}),
 *      @ORM\Index(name="IDX_1DCF9AF9C53B46B", columns={"dna_quality_voc_fk"})
 * })
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="Code {{ value }} is already registered")
 *
 * @ApiResource(
 *     itemOperations={
 *       "get": {
 *          "normalization_context": {"groups": {"item", "dna:item"}}
 *       },
 *       "delete"
 *     },
 *     collectionOperations={
 *       "get": {
 *          "normalization_context": {"groups": {"item", "dna:list"}}
 *       },
 *       "post": {
 * 					"normalization_context": {"groups": {"item", "dna:item"}},
 * 					"denormalization_context": {"groups": {"dna:write"}}
 * 			 },
 * 			 "import": {
 * 					"method": "POST",
 * 					"path": "/dnas/import",
 * 					"controller": ImportCSVAction::class,
 * 					"input": CsvRecordsRequest::class,
 * 					"output": ArrayCollection::class,
 * 					"normalization_context": {"groups": {"item", "dna:item"}},
 * 					"denormalization_context": {"groups": {"csv:import"}},
 * 					"openapi_context": {
 * 						"summary": "Import Dna resources from CSV string."
 * 					}
 * 				}
 *     },
 *     order={"code": "ASC"},
 *     paginationEnabled=true
 * )
 * @ApiFilter(SearchFilter::class, properties=Dna::API_SEARCH_PROPERTIES)
 * @ApiFilter(OrSearchFilter::class, properties=Dna::API_SEARCH_PROPERTIES)
 * @ApiFilter(DateFilter::class, properties={"date": "DateFilter::EXCLUDE_NULL"})
 * @ApiFilter(OrderFilter::class, properties={"code", "date", "concentrationNgMicrolitre", "specimen.molecularCode", "store.code"})
 * @ApiFilter(PropertyFilter::class)
 */
class Dna extends AbstractTimestampedEntity {
  public const API_SEARCH_PROPERTIES = [
    'code' => 'ipartial',
    'specimen.molecularCode' => 'ipartial',
    'specimen.morphologicalCode' => 'ipartial',
    'store.code' => 'ipartial',
    'datePrecision.code' => 'ipartial',
    'extractionMethod.code' => 'ipartial',
    'quality.code' => 'ipartial',
  ];

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="dna_id_seq", allocationSize=1, initialValue=1)
   * @ApiProperty(identifier=false)
   * @Groups({"item", "dna:write"})
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_code", type="string", length=255, nullable=false, unique=true)
   * @Groups({"item", "dna:write"})
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
   * @Groups({"item", "dna:write"})
   */
  private $date;

  /**
   * @var float
   *
   * @ORM\Column(name="dna_concentration", type="float", precision=10, scale=0, nullable=true)
   * @Groups({"item", "dna:write"})
   * @Assert\PositiveOrZero
   */
  private $concentrationNgMicrolitre;

  /**
   * @var string
   *
   * @ORM\Column(name="dna_comments", type="text", nullable=true)
   * @Groups({"dna:item", "dna:write"})
   */
  private $comment;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="date_precision_voc_fk", referencedColumnName="id", nullable=false)
   * @Groups({"dna:list", "dna:item", "dna:write"})
   */
  private $datePrecision;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_extraction_method_voc_fk", referencedColumnName="id", nullable=false))
   * @Groups({"dna:list", "dna:item", "dna:write"})
   */
  private $extractionMethod;

  /**
   * @var Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="dna_quality_voc_fk", referencedColumnName="id", nullable=false)
   * @Groups({"dna:list", "dna:item", "dna:write"})
   */
  private $quality;

  /**
   * @var Specimen
   *
   * @ORM\ManyToOne(targetEntity="Specimen", fetch="EAGER")
   * @ORM\JoinColumn(name="specimen_fk", referencedColumnName="id", nullable=false)
   * @ORM\OrderBy({"molecularCode": "ASC"})
   * @Groups({"dna:list", "dna:item", "dna:write"})
   */
  private $specimen;

  /**
   * @var Store
   *
   * @ORM\ManyToOne(targetEntity="Store", inversedBy="dnas", fetch="EAGER")
   * @ORM\JoinColumn(name="storage_box_fk", referencedColumnName="id", nullable=true)
   * @Groups({"dna:list", "dna:item", "dna:write"})
   */
  private $store;

  /**
   * @ORM\ManyToMany(targetEntity="Person", cascade={"persist"})
   * @ORM\JoinTable(name="dna_is_extracted_by",
   *  joinColumns={@ORM\JoinColumn(name="dna_fk", referencedColumnName="id")},
   *  inverseJoinColumns={@ORM\JoinColumn(name="person_fk", referencedColumnName="id")})
   * @ORM\OrderBy({"id": "ASC"})
   * @Groups({"dna:list", "dna:item", "dna:write"})
   * @Assert\Count(min=1, minMessage="At least one person is required as producer")
   */
  private $producers;

  /**
   * @var Collection
   * @ORM\OneToMany(targetEntity="Pcr", mappedBy="dna")
   * @ApiProperty(writable=false)
   * @Groups({"dna:list", "dna:item"})
   */
  private $pcrs;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->pcrs = new ArrayCollection();
  }

  /**
   * @Groups({"dna:list", "dna:item"})
   */
  public function getMetadata(): array {
    return parent::getMetadata();
  }

  /**
   * Get id.
   *
   * @return string
   */
  public function getId(): ?string {
    return $this->id;
  }

  /**
   * Set code.
   *
   * @param string $code
   */
  public function setCode($code): Dna {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code.
   *
   * @return string
   */
  public function getCode(): ?string {
    return $this->code;
  }

  /**
   * Set date.
   *
   * @param \DateTime $date
   */
  public function setDate($date): Dna {
    if (is_string($date)) {
      $date = new DateTime($date);
    }
    $this->date = $date;

    return $this;
  }

  /**
   * Get date.
   *
   * @return \DateTime
   */
  public function getDate(): ?DateTime {
    return $this->date;
  }

  /**
   * Set concentrationNgMicrolitre.
   *
   * @param float $concentrationNgMicrolitre
   */
  public function setConcentrationNgMicrolitre(
        $concentrationNgMicrolitre,
    ): Dna {
    $this->concentrationNgMicrolitre = $concentrationNgMicrolitre;

    return $this;
  }

  /**
   * Get concentrationNgMicrolitre.
   *
   * @return float
   */
  public function getConcentrationNgMicrolitre(): ?float {
    return $this->concentrationNgMicrolitre;
  }

  /**
   * Set comment.
   *
   * @param string $comment
   */
  public function setComment($comment): Dna {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment.
   *
   * @return string
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set datePrecision.
   *
   * @param Voc $datePrecision
   */
  public function setDatePrecision(Voc $datePrecision = null): Dna {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  /**
   * Get datePrecision.
   *
   * @return Voc
   */
  public function getDatePrecision(): ?Voc {
    return $this->datePrecision;
  }

  /**
   * Set extractionMethod.
   *
   * @param Voc $extractionMethod
   */
  public function setExtractionMethod(Voc $extractionMethod = null): Dna {
    $this->extractionMethod = $extractionMethod;

    return $this;
  }

  /**
   * Get extractionMethod.
   *
   * @return Voc
   */
  public function getExtractionMethod(): ?Voc {
    return $this->extractionMethod;
  }

  /**
   * Set specimen.
   *
   * @param Specimen $specimen
   */
  public function setSpecimen(Specimen $specimen = null): Dna {
    $this->specimen = $specimen;

    return $this;
  }

  /**
   * Get specimen.
   *
   * @return Specimen
   */
  public function getSpecimen(): ?Specimen {
    return $this->specimen;
  }

  /**
   * Set quality.
   *
   * @param Voc $quality
   */
  public function setQuality(Voc $quality = null): Dna {
    $this->quality = $quality;

    return $this;
  }

  /**
   * Get quality.
   *
   * @return Voc
   */
  public function getQuality(): ?Voc {
    return $this->quality;
  }

  /**
   * Set store.
   *
   * @param Store $store
   */
  public function setStore(Store $store = null): Dna {
    $this->store = $store;

    return $this;
  }

  /**
   * Get store.
   *
   * @return Store
   */
  public function getStore(): ?Store {
    return $this->store;
  }

  /**
   * Add producer.
   */
  public function addProducer(Person $producer): Dna {
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer.
   */
  public function removeProducer(Person $producer): Dna {
    $this->producers->removeElement($producer);

    return $this;
  }

  /**
   * Get producers.
   */
  public function getProducers(): Collection {
    return $this->producers;
  }

  /**
   * Get PCRs.
   */
  public function getPcrs(): Collection {
    return $this->pcrs;
  }

  public function addPcr(Pcr $pcr): Dna {
    $pcr->setDna($this);
    $this->pcrs[] = $pcr;

    return $this;
  }
}
