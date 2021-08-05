<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SequenceAssemblee
 *
 * @ORM\Table(name="internal_sequence",
 * uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_internal_sequence__internal_sequence_code", columns={"internal_sequence_code"}),
 *      @ORM\UniqueConstraint(name="uk_internal_sequence__internal_sequence_alignment_code", columns={"internal_sequence_alignment_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_353CF669A30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_353CF66988085E0F", columns={"internal_sequence_status_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeSqcAss"}, message="This code is already registered")
 * @UniqueEntity(fields={"codeSqcAlignement"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssemblee extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="internal_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_code", type="string", length=1024, nullable=false)
   */
  private $codeSqcAss;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="internal_sequence_creation_date", type="date", nullable=true)
   */
  private $dateCreationSqcAss;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_accession_number", type="string", length=255, nullable=true)
   */
  private $accessionNumber;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private $codeSqcAlignement;

  /**
   * @var string
   *
   * @ORM\Column(name="internal_sequence_comments", type="text", nullable=true)
   */
  private $commentaireSqcAss;

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
   *   @ORM\JoinColumn(name="internal_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $statutSqcAssVocFk;

  /**
   * @ORM\OneToMany(targetEntity="SequenceAssembleeEstRealisePar", mappedBy="sequenceAssembleeFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sequenceAssembleeEstRealisePars;

  /**
   * @ORM\OneToMany(targetEntity="SqcEstPublieDans", mappedBy="sequenceAssembleeFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sqcEstPublieDanss;

  /**
   * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="sequenceAssembleeFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $especeIdentifiees;

  /**
   * @ORM\OneToMany(targetEntity="EstAligneEtTraite", mappedBy="sequenceAssembleeFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $estAligneEtTraites;

  public function __construct() {
    $this->sequenceAssembleeEstRealisePars = new ArrayCollection();
    $this->sqcEstPublieDanss = new ArrayCollection();
    $this->especeIdentifiees = new ArrayCollection();
    $this->estAligneEtTraites = new ArrayCollection();
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
   * Set codeSqcAss
   *
   * @param string $codeSqcAss
   *
   * @return SequenceAssemblee
   */
  public function setCodeSqcAss($codeSqcAss) {
    $this->codeSqcAss = $codeSqcAss;

    return $this;
  }

  /**
   * Get codeSqcAss
   *
   * @return string
   */
  public function getCodeSqcAss() {
    return $this->codeSqcAss;
  }

  /**
   * Set dateCreationSqcAss
   *
   * @param \DateTime $dateCreationSqcAss
   *
   * @return SequenceAssemblee
   */
  public function setDateCreationSqcAss($dateCreationSqcAss) {
    $this->dateCreationSqcAss = $dateCreationSqcAss;

    return $this;
  }

  /**
   * Get dateCreationSqcAss
   *
   * @return \DateTime
   */
  public function getDateCreationSqcAss() {
    return $this->dateCreationSqcAss;
  }

  /**
   * Set accessionNumber
   *
   * @param string $accessionNumber
   *
   * @return SequenceAssemblee
   */
  public function setAccessionNumber($accessionNumber) {
    $this->accessionNumber = $accessionNumber;

    return $this;
  }

  /**
   * Get accessionNumber
   *
   * @return string
   */
  public function getAccessionNumber() {
    return $this->accessionNumber;
  }

  /**
   * Set codeSqcAlignement
   *
   * @param string $codeSqcAlignement
   *
   * @return SequenceAssemblee
   */
  public function setCodeSqcAlignement($codeSqcAlignement) {
    $this->codeSqcAlignement = $codeSqcAlignement;

    return $this;
  }

  /**
   * Get codeSqcAlignement
   *
   * @return string
   */
  public function getCodeSqcAlignement() {
    return $this->codeSqcAlignement;
  }

  /**
   * Set commentaireSqcAss
   *
   * @param string $commentaireSqcAss
   *
   * @return SequenceAssemblee
   */
  public function setCommentaireSqcAss($commentaireSqcAss) {
    $this->commentaireSqcAss = $commentaireSqcAss;

    return $this;
  }

  /**
   * Get commentaireSqcAss
   *
   * @return string
   */
  public function getCommentaireSqcAss() {
    return $this->commentaireSqcAss;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return SequenceAssemblee
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
   * Set statutSqcAssVocFk
   *
   * @param \App\Entity\Voc $statutSqcAssVocFk
   *
   * @return SequenceAssemblee
   */
  public function setStatutSqcAssVocFk(\App\Entity\Voc $statutSqcAssVocFk = null) {
    $this->statutSqcAssVocFk = $statutSqcAssVocFk;

    return $this;
  }

  /**
   * Get statutSqcAssVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getStatutSqcAssVocFk() {
    return $this->statutSqcAssVocFk;
  }

  /**
   * Add sequenceAssembleeEstRealisePar
   *
   * @param \App\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar
   *
   * @return SequenceAssemblee
   */
  public function addSequenceAssembleeEstRealisePar(\App\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar) {
    $sequenceAssembleeEstRealisePar->setSequenceAssembleeFk($this);
    $this->sequenceAssembleeEstRealisePars[] = $sequenceAssembleeEstRealisePar;

    return $this;
  }

  /**
   * Remove sequenceAssembleeEstRealisePar
   *
   * @param \App\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar
   */
  public function removeSequenceAssembleeEstRealisePar(\App\Entity\SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar) {
    $this->sequenceAssembleeEstRealisePars->removeElement($sequenceAssembleeEstRealisePar);
  }

  /**
   * Get sequenceAssembleeEstRealisePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSequenceAssembleeEstRealisePars() {
    return $this->sequenceAssembleeEstRealisePars;
  }

  /**
   * Add sqcEstPublieDans
   *
   * @param \App\Entity\SqcEstPublieDans $sqcEstPublieDans
   *
   * @return SequenceAssemblee
   */
  public function addSqcEstPublieDans(\App\Entity\SqcEstPublieDans $sqcEstPublieDans) {
    $sqcEstPublieDans->setSequenceAssembleeFk($this);
    $this->sqcEstPublieDanss[] = $sqcEstPublieDans;

    return $this;
  }

  /**
   * Remove sqcEstPublieDans
   *
   * @param \App\Entity\SqcEstPublieDans $sqcEstPublieDans
   */
  public function removeSqcEstPublieDans(\App\Entity\SqcEstPublieDans $sqcEstPublieDans) {
    $this->sqcEstPublieDanss->removeElement($sqcEstPublieDans);
  }

  /**
   * Get sqcEstPublieDanss
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSqcEstPublieDanss() {
    return $this->sqcEstPublieDanss;
  }

  /**
   * Add especeIdentifiee
   *
   * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
   *
   * @return SequenceAssemblee
   */
  public function addEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee) {
    $especeIdentifiee->setSequenceAssembleeFk($this);
    $this->especeIdentifiees[] = $especeIdentifiee;

    return $this;
  }

  /**
   * Remove especeIdentifiee
   *
   * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
   */
  public function removeEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee) {
    $this->especeIdentifiees->removeElement($especeIdentifiee);
  }

  /**
   * Get especeIdentifiees
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEspeceIdentifiees() {
    return $this->especeIdentifiees;
  }

  /**
   * Add estAligneEtTraite
   *
   * @param \App\Entity\EstAligneEtTraite $estAligneEtTraite
   *
   * @return SequenceAssemblee
   */
  public function addEstAligneEtTraite(\App\Entity\EstAligneEtTraite $estAligneEtTraite) {
    $estAligneEtTraite->setSequenceAssembleeFk($this);
    $this->estAligneEtTraites[] = $estAligneEtTraite;

    return $this;
  }

  /**
   * Remove estAligneEtTraite
   *
   * @param \App\Entity\EstAligneEtTraite $estAligneEtTraite
   */
  public function removeEstAligneEtTraite(\App\Entity\EstAligneEtTraite $estAligneEtTraite) {
    $this->estAligneEtTraites->removeElement($estAligneEtTraite);
  }

  /**
   * Get estAligneEtTraite
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEstAligneEtTraites() {
    return $this->estAligneEtTraites;
  }

  /**
   * Get geneVocFk
   *
   * This assumes that a sequence matches ONE gene only,
   * even if it was processed through multiple chromatograms
   *
   * @return mixed
   */
  public function getGeneVocFk() {
    $process = $this->estAligneEtTraites->first();
    return $process
    ? $process
      ->getChromatogrammeFk()
      ->getPcrFk()
      ->getGeneVocFk()
    : null;
  }

  /**
   * Get individuFk
   *
   * this assumes that a sequence matches ONE specimen only,
   * even if it was processed through multiple chromatograms
   *
   * @return mixed
   */
  public function getIndividuFk() {
    $process = $this->estAligneEtTraites->first();
    return $process
    ? $process
      ->getChromatogrammeFk()
      ->getPcrFk()
      ->getAdnFk()
      ->getIndividuFk()
    : null;
  }

  /**
   * Generate alignment code
   *
   * generates an alignment code from sequence metadata
   * generated code is saved as the sequence alignment code
   *
   * @return string
   */
  public function generateAlignmentCode() {
    $nbChromato = count($this->getEstAligneEtTraites());
    $nbIdentifiedSpecies = count($this->getEspeceIdentifiees());
    if ($nbChromato < 1 || $nbIdentifiedSpecies < 1) {
      $seqCode = null;
    } else {
      $seqCodeElts = [];
      $statusCode = $this->getStatutSqcAssVocFk()->getCode();
      if (substr($statusCode, 0, 5) != 'VALID') {
        $seqCodeElts[] = $statusCode;
      }

      $lastTaxonCode = $this->getEspeceIdentifiees()->last()
        ->getReferentielTaxonFk()
        ->getCodeTaxon();
      $seqCodeElts[] = $lastTaxonCode;

      $specimen = $this->getIndividuFk();
      $samplingCode = $specimen->getLotMaterielFk()
        ->getCollecteFk()
        ->getCodeCollecte();
      $seqCodeElts[] = $samplingCode;

      $specimenCode = $specimen->getNumIndBiomol();
      $seqCodeElts[] = $specimenCode;

      $chromatoCodeList = $this->getEstAligneEtTraites()
        ->map(
          function ($seqProcessing) {
            $chromato = $seqProcessing->getChromatogrammeFk();
            $code = $chromato->getCodeChromato();
            $specificite = $chromato->getPcrFk()->getSpecificiteVocFk()->getCode();
            return $code . '|' . $specificite;
          }
        )
        ->toArray();
      $chromatoCodeStr = implode("-", $chromatoCodeList);
      $seqCodeElts[] = $chromatoCodeStr;
      $seqCode = implode("_", $seqCodeElts);
    }
    $this->setCodeSqcAlignement($seqCode);
    return $seqCode;
  }
}
