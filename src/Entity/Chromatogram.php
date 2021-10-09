<?php

namespace App\Entity;

use App\Entity\Abstraction\AbstractTimestampedEntity;
use App\Entity\Abstraction\CompositeCodeEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Chromatogram
 *
 * @ORM\Table(name="chromatogram",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_chromatogram__chromatogram_code", columns={"chromatogram_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_FCB2DAB7286BBCA9", columns={"chromato_primer_voc_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB7206FE5C0", columns={"chromato_quality_voc_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB7E8441376", columns={"institution_fk"}),
 *      @ORM\Index(name="IDX_FCB2DAB72B63D494", columns={"pcr_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"code"}, message="This code is already registered")
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Chromatogram extends AbstractTimestampedEntity {
  use CompositeCodeEntityTrait;

  /**
   * @ORM\Id
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private int $id;

  /**
   * @ORM\Column(name="chromatogram_code", type="string", length=255, nullable=false, unique=true)
   */
  private string $code;

  /**
   * @ORM\Column(name="chromatogram_number", type="string", length=255, nullable=false)
   */
  private string $yasNumber;

  /**
   * @ORM\Column(name="chromatogram_comments", type="text", nullable=true)
   */
  private ?string $comment = null;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="chromato_primer_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $primer;

  /**
   * @ORM\ManyToOne(targetEntity="Voc", fetch="EAGER")
   * @ORM\JoinColumn(name="chromato_quality_voc_fk", referencedColumnName="id", nullable=false)
   */
  private Voc $quality;

  /**
   * @ORM\ManyToOne(targetEntity="Institution")
   * @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=false)
   */
  private Institution $institution;

  /**
   * @ORM\ManyToOne(targetEntity="Pcr")
   * @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false)
   */
  private Pcr $pcr;

  /**
   * Get id
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * Set code
   */
  public function setCode(string $code): self {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   */
  public function getCode(): string {
    return $this->code;
  }

  private function _generateCode(): string {
    return join('|', [$this->getYasNumber(), $this->getPrimer()->getCode()]);
  }

  /**
   * Set yasNumber
   */
  public function setYasNumber(string $yasNumber): self {
    $this->yasNumber = $yasNumber;

    return $this;
  }

  /**
   * Get yasNumber
   */
  public function getYasNumber(): string {
    return $this->yasNumber;
  }

  /**
   * Set comment
   */
  public function setComment(?string $comment): self {
    $this->comment = $comment;

    return $this;
  }

  /**
   * Get comment
   */
  public function getComment(): ?string {
    return $this->comment;
  }

  /**
   * Set primer
   */
  public function setPrimer(Voc $primer): self {
    $this->primer = $primer;

    return $this;
  }

  /**
   * Get primer
   */
  public function getPrimer(): Voc {
    return $this->primer;
  }

  /**
   * Set quality
   */
  public function setQuality(Voc $quality): self {
    $this->quality = $quality;

    return $this;
  }

  /**
   * Get quality
   */
  public function getQuality(): Voc {
    return $this->quality;
  }

  /**
   * Set institution
   */
  public function setInstitution(Institution $institution): self {
    $this->institution = $institution;

    return $this;
  }

  /**
   * Get institution
   */
  public function getInstitution(): Institution {
    return $this->institution;
  }

  /**
   * Set pcr
   */
  public function setPcr(Pcr $pcr): self {
    $this->pcr = $pcr;

    return $this;
  }

  /**
   * Get pcr
   */
  public function getPcr(): Pcr {
    return $this->pcr;
  }

  /**
   * Get CodeSpecificity
   */
  public function getCodeSpecificity(): string {
    $specificity = $this->pcr->getSpecificity()->getCode();

    return "{$this->code}|{$specificity}";
  }
}
