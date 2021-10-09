<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Controller\API\ImportCSVAction;
use App\DTO\CsvRecordsRequest;
use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Filter\OrSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A DNA sample extracted from a Specimen
 */
#[ApiResource(
  attributes: ['security' => '"IS_AUTHENTICATED_FULLY"'],
  itemOperations: [
    'get' => ['normalization_context' => ['groups' => ['item', 'dna:item']]],
    'delete' => [
      'security' => 'is_granted("ROLE_ADMIN") or
      (is_granted("ROLE_COLLABORATION") and object.getMetaCreationUser() == user)',
    ],
  ],
  collectionOperations: [
    'get' => ['normalization_context' => ['groups' => ['item', 'dna:list']]],
    'post' => [
      'normalization_context' => ['groups' => ['item', 'dna:item']],
      'denormalization_context' => ['groups' => ['dna:write']],
      'security' => 'is_granted("ROLE_COLLABORATION")',
    ],
    'import' => [
      'method' => 'POST',
      'controller' => ImportCSVAction::class,
      'input' => CsvRecordsRequest::class,
      'security' => 'is_granted("ROLE_COLLABORATION")',
      'path' => '/dnas/import',
      'normalization_context' => ['groups' => ['item', 'dna:list']],
      'denormalization_context' => ['groups' => ['csv:import']],
    ],
  ],
  order: ['code' => 'ASC'],
  paginationEnabled: true
)]
#[ApiFilter(SearchFilter::class, properties: Dna::API_SEARCH_PROPERTIES)]
#[ApiFilter(OrSearchFilter::class, properties: Dna::API_SEARCH_PROPERTIES)]
#[ApiFilter(DateFilter::class, properties: ['date' => DateFilter::EXCLUDE_NULL])]
#[ApiFilter(
  OrderFilter::class,
  properties: ['code', 'date', 'concentrationNgMicrolitre', 'specimen.molecularCode', 'store.code']
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups', 'overrideDefaultGroups' => true])]
// ORM        ------------------------------------------------------------------
#[ORM\Entity]
#[ORM\Table(name: 'dna')]
#[ORM\Index(name: 'dna_code_adn', columns: ['dna_code'])]
#[ORM\Index(name: 'idx_dna__date_precision_voc_fk', columns: ['date_precision_voc_fk'])]
#[ORM\Index(name: 'idx_dna__specimen_fk', columns: ['specimen_fk'])]
#[ORM\Index(
  name: 'idx_dna__dna_extraction_method_voc_fk',
  columns: ['dna_extraction_method_voc_fk']
)]
#[ORM\Index(name: 'idx_dna__storage_box_fk', columns: ['storage_box_fk'])]
#[ORM\Index(name: 'IDX_1DCF9AF9C53B46B', columns: ['dna_quality_voc_fk'])]
#[ORM\UniqueConstraint(name: 'uk_dna__dna_code', columns: ['dna_code'])]
// Validation ------------------------------------------------------------------
#[UniqueEntity(fields: ['code'], message: 'Code {{ value }} is already registered')]
class Dna extends AbstractTimestampedEntity {
  public const API_SEARCH_PROPERTIES = [
    'code' => 'ipartial',
    'specimen.molecularCode' => 'ipartial',
    'specimen.morphologicalCode' => 'ipartial',
    'store.code' => 'ipartial',
    'datePrecision.code' => 'ipartial',
    'extractionMethod.code' => 'ipartial',
    'quality.code' => 'ipartial',
    'producers.name' => 'ipartial',
  ];

  #[ORM\Id]
  #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
  #[ORM\GeneratedValue(strategy: 'IDENTITY')]
  #[Groups(['item', 'compact'])]
  private int $id;

  #[ORM\Column(name: 'dna_code', type: 'string', length: 255, nullable: false, unique: true)]
  #[Groups(['item', 'compact', 'dna:write'])]
  #[Assert\Regex(pattern: '/^[\\w]+$/', message: 'Code {{ value }} contains invalid special characters')]
  private string $code;

  /**
   * Sample extraction date
   */
  #[ORM\Column(name: 'dna_extraction_date', type: 'date', nullable: true)]
  #[Groups(['item', 'dna:write'])]
  private ?\DateTime $date = null;

  /**
   * Sample concentration in ng/µL
   */
  #[ORM\Column(name: 'dna_concentration', type: 'float', precision: 10, scale: 0, nullable: true)]
  #[Groups(['item', 'dna:write'])]
  #[Assert\Positive()]
  private ?float  $concentrationNgMicrolitre = null;

  #[ORM\Column(name: 'dna_comments', type: 'text', nullable: true)]
  #[Groups(['dna:item', 'dna:write'])]
  private ?string $comment = null;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'date_precision_voc_fk', referencedColumnName: 'id', nullable: false)]
  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  private Voc $datePrecision;

  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'dna_extraction_method_voc_fk', referencedColumnName: 'id', nullable: false)]
  private Voc $extractionMethod;

  #[ORM\ManyToOne(targetEntity: 'Voc', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'dna_quality_voc_fk', referencedColumnName: 'id', nullable: false)]
  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  private Voc $quality;

  #[ORM\ManyToOne(targetEntity: 'Specimen', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'specimen_fk', referencedColumnName: 'id', nullable: false)]
  #[ORM\OrderBy(value: ['molecularCode' => 'ASC'])]
  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  private Specimen $specimen;

  #[ORM\ManyToOne(targetEntity: 'Store', inversedBy: 'dnas', fetch: 'EAGER')]
  #[ORM\JoinColumn(name: 'storage_box_fk', referencedColumnName: 'id', nullable: true)]
  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  private ?Store $store = null;

  #[ORM\ManyToMany(targetEntity: 'Person', cascade: ['persist'])]
  #[ORM\JoinTable(name: 'dna_is_extracted_by')]
  #[ORM\JoinColumn(name: 'dna_fk', referencedColumnName: 'id')]
  #[ORM\InverseJoinColumn(name: 'person_fk', referencedColumnName: 'id')]
  #[ORM\OrderBy(value: ['id' => 'ASC'])]
  #[Groups(['dna:list', 'dna:item', 'dna:write'])]
  #[Assert\Count(min: 1, minMessage: 'At least one person is required as producer')]
  private Collection $producers;

  #[Assert\Count(
    exactly: 0,
    groups: ['delete'],
    exactMessage: 'This DNA sample is referenced by some PCRs'
  )]
  #[ORM\OneToMany(targetEntity: 'Pcr', mappedBy: 'dna')]
  #[ApiProperty(writable: false)]
  #[Groups(['dna:list', 'dna:item'])]
  private Collection $pcrs;

  public function __construct() {
    $this->producers = new ArrayCollection();
    $this->pcrs = new ArrayCollection();
  }

  #[Groups(['dna:list', 'dna:item'])]
  public function getMetadata(): array {
    return parent::getMetadata();
  }

  public function getId(): ?int {
    return $this->id;
  }

  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  public function getCode(): ?string {
    return $this->code;
  }

  public function setDate($date): self {
    if (is_string($date)) {
      $date = new \DateTime($date);
    }
    $this->date = $date;

    return $this;
  }

  public function getDate(): ?\DateTime {
    return $this->date;
  }

  public function setConcentrationNgMicrolitre(?float $concentrationNgMicrolitre): self {
    $this->concentrationNgMicrolitre = $concentrationNgMicrolitre;

    return $this;
  }

  public function getConcentrationNgMicrolitre(): ?float {
    return $this->concentrationNgMicrolitre;
  }

  public function setComment(string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  public function getComment(): ?string {
    return $this->comment;
  }

  public function setDatePrecision(Voc $datePrecision = null): self {
    $this->datePrecision = $datePrecision;

    return $this;
  }

  public function getDatePrecision(): ?Voc {
    return $this->datePrecision;
  }

  public function setExtractionMethod(Voc $method = null): self {
    $this->extractionMethod = $method;

    return $this;
  }

  public function getExtractionMethod(): ?Voc {
    return $this->extractionMethod;
  }

  public function setSpecimen(Specimen $specimen = null): self {
    $this->specimen = $specimen;

    return $this;
  }

  public function getSpecimen(): ?Specimen {
    return $this->specimen;
  }

  public function setQuality(Voc $quality = null): self {
    $this->quality = $quality;

    return $this;
  }

  public function getQuality(): ?Voc {
    return $this->quality;
  }

  public function setStore(Store $store = null): self {
    $this->store = $store;

    return $this;
  }

  public function getStore(): ?Store {
    return $this->store;
  }

  /**
   * Add producer.
   */
  public function addProducer(Person $producer): self {
    $this->producers[] = $producer;

    return $this;
  }

  /**
   * Remove producer
   */
  public function removeProducer(Person $producer): self {
    $this->producers->removeElement($producer);

    return $this;
  }

  public function getProducers(): Collection {
    return $this->producers;
  }

  public function getPcrs(): Collection {
    return $this->pcrs;
  }

  /**
   * Add PCR
   */
  public function addPcr(Pcr $pcr): self {
    $pcr->setDna($this);
    $this->pcrs[] = $pcr;

    return $this;
  }
}
