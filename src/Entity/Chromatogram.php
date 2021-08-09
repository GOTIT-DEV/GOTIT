<?php

namespace App\Entity;

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
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Chromatogram extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="chromatogram_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="chromatogram_code", type="string", length=255, nullable=false)
   */
  private $code;

  /**
   * @var string
   *
   * @ORM\Column(name="chromatogram_number", type="string", length=255, nullable=false)
   */
  private $yasNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="chromatogram_comments", type="text", nullable=true)
   */
  private $comment;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="chromato_primer_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $primerVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="chromato_quality_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $qualityVocFk;

  /**
   * @var \Institution
   *
   * @ORM\ManyToOne(targetEntity="Institution")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $institutionFk;

  /**
   * @var \Pcr
   *
   * @ORM\ManyToOne(targetEntity="Pcr")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="pcr_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $pcrFk;

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
   * @return Chromatogram
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
   * Set yasNumber
   *
   * @param string $yasNumber
   *
   * @return Chromatogram
   */
  public function setYasNumber($yasNumber) {
    $this->yasNumber = $yasNumber;

    return $this;
  }

  /**
   * Get yasNumber
   *
   * @return string
   */
  public function getYasNumber() {
    return $this->yasNumber;
  }

  /**
   * Set comment
   *
   * @param string $comment
   *
   * @return Chromatogram
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
   * Set primerVocFk
   *
   * @param Voc $primerVocFk
   *
   * @return Chromatogram
   */
  public function setPrimerVocFk(Voc $primerVocFk = null) {
    $this->primerVocFk = $primerVocFk;

    return $this;
  }

  /**
   * Get primerVocFk
   *
   * @return Voc
   */
  public function getPrimerVocFk() {
    return $this->primerVocFk;
  }

  /**
   * Set qualityVocFk
   *
   * @param Voc $qualityVocFk
   *
   * @return Chromatogram
   */
  public function setQualityVocFk(Voc $qualityVocFk = null) {
    $this->qualityVocFk = $qualityVocFk;

    return $this;
  }

  /**
   * Get qualityVocFk
   *
   * @return Voc
   */
  public function getQualityVocFk() {
    return $this->qualityVocFk;
  }

  /**
   * Set institutionFk
   *
   * @param Institution $institutionFk
   *
   * @return Chromatogram
   */
  public function setInstitutionFk(Institution $institutionFk = null) {
    $this->institutionFk = $institutionFk;

    return $this;
  }

  /**
   * Get institutionFk
   *
   * @return Institution
   */
  public function getInstitutionFk() {
    return $this->institutionFk;
  }

  /**
   * Set pcrFk
   *
   * @param Pcr $pcrFk
   *
   * @return Chromatogram
   */
  public function setPcrFk(Pcr $pcrFk = null) {
    $this->pcrFk = $pcrFk;

    return $this;
  }

  /**
   * Get pcrFk
   *
   * @return Pcr
   */
  public function getPcrFk() {
    return $this->pcrFk;
  }

  /**
   * Get CodeSpecificity
   *
   * @return string
   */
  public function getCodeSpecificity() {
    $specificite = $this->pcrFk->getSpecificiteVocFk()->getCode();
    return $this->code . '|' . $specificite;
  }
}
