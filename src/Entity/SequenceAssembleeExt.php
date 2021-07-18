<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SequenceAssembleeExt
 *
 * @ORM\Table(name="external_sequence",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_external_sequence__external_sequence_code", columns={"external_sequence_code"}),
 *      @ORM\UniqueConstraint(name="uk_external_sequence__external_sequence_alignment_code", columns={"external_sequence_alignment_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_9E9F85CF9D3CDB05", columns={"gene_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CFA30C442F", columns={"date_precision_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF514D78E0", columns={"external_sequence_origin_voc_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF662D9B98", columns={"sampling_fk"}),
 *      @ORM\Index(name="IDX_9E9F85CF88085E0F", columns={"external_sequence_status_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeSqcAssExt"}, message="This code is already registered")
 * @UniqueEntity(fields={"codeSqcAssExtAlignement"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeExt {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="external_sequence_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_code", type="string", length=1024, nullable=false, unique=true)
   */
  private $codeSqcAssExt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="external_sequence_creation_date", type="date", nullable=true)
   */
  private $dateCreationSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_accession_number", type="string", length=255, nullable=false)
   */
  private $accessionNumberSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_alignment_code", type="string", length=1024, nullable=true)
   */
  private $codeSqcAssExtAlignement;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_specimen_number", type="string", length=255, nullable=false)
   */
  private $numIndividuSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_primary_taxon", type="string", length=255, nullable=true)
   */
  private $taxonOrigineSqcAssExt;

  /**
   * @var string
   *
   * @ORM\Column(name="external_sequence_comments", type="text", nullable=true)
   */
  private $commentaireSqcAssExt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
   */
  private $dateCre;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
   */
  private $dateMaj;

  /**
   * @var integer
   *
   * @ORM\Column(name="creation_user_name", type="bigint", nullable=true)
   */
  private $userCre;

  /**
   * @var integer
   *
   * @ORM\Column(name="update_user_name", type="bigint", nullable=true)
   */
  private $userMaj;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="gene_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $geneVocFk;

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
   *   @ORM\JoinColumn(name="external_sequence_origin_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $origineSqcAssExtVocFk;

  /**
   * @var \Collecte
   *
   * @ORM\ManyToOne(targetEntity="Collecte")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="sampling_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $collecteFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="external_sequence_status_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $statutSqcAssVocFk;

  /**
   * @ORM\OneToMany(targetEntity="SqcExtEstRealisePar", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sqcExtEstRealisePars;

  /**
   * @ORM\OneToMany(targetEntity="SqcExtEstReferenceDans", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $sqcExtEstReferenceDanss;

  /**
   * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="sequenceAssembleeExtFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $especeIdentifiees;

  public function __construct() {
    $this->sqcExtEstRealisePars = new ArrayCollection();
    $this->sqcExtEstReferenceDanss = new ArrayCollection();
    $this->especeIdentifiees = new ArrayCollection();
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
   * Set codeSqcAssExt
   *
   * @param string $codeSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setCodeSqcAssExt($codeSqcAssExt) {
    $this->codeSqcAssExt = $codeSqcAssExt;

    return $this;
  }

  /**
   * Get codeSqcAssExt
   *
   * @return string
   */
  public function getCodeSqcAssExt() {
    return $this->codeSqcAssExt;
  }

  /**
   * Set dateCreationSqcAssExt
   *
   * @param \DateTime $dateCreationSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setDateCreationSqcAssExt($dateCreationSqcAssExt) {
    $this->dateCreationSqcAssExt = $dateCreationSqcAssExt;

    return $this;
  }

  /**
   * Get dateCreationSqcAssExt
   *
   * @return \DateTime
   */
  public function getDateCreationSqcAssExt() {
    return $this->dateCreationSqcAssExt;
  }

  /**
   * Set accessionNumberSqcAssExt
   *
   * @param string $accessionNumberSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setAccessionNumberSqcAssExt($accessionNumberSqcAssExt) {
    $this->accessionNumberSqcAssExt = $accessionNumberSqcAssExt;

    return $this;
  }

  /**
   * Get accessionNumberSqcAssExt
   *
   * @return string
   */
  public function getAccessionNumberSqcAssExt() {
    return $this->accessionNumberSqcAssExt;
  }

  /**
   * Set codeSqcAssExtAlignement
   *
   * @param string $codeSqcAssExtAlignement
   *
   * @return SequenceAssembleeExt
   */
  public function setCodeSqcAssExtAlignement($codeSqcAssExtAlignement) {
    $this->codeSqcAssExtAlignement = $codeSqcAssExtAlignement;

    return $this;
  }

  /**
   * Get codeSqcAssExtAlignement
   *
   * @return string
   */
  public function getCodeSqcAssExtAlignement() {
    return $this->codeSqcAssExtAlignement;
  }

  /**
   * Set numIndividuSqcAssExt
   *
   * @param string $numIndividuSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setNumIndividuSqcAssExt($numIndividuSqcAssExt) {
    $this->numIndividuSqcAssExt = $numIndividuSqcAssExt;

    return $this;
  }

  /**
   * Get numIndividuSqcAssExt
   *
   * @return string
   */
  public function getNumIndividuSqcAssExt() {
    return $this->numIndividuSqcAssExt;
  }

  /**
   * Set taxonOrigineSqcAssExt
   *
   * @param string $taxonOrigineSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setTaxonOrigineSqcAssExt($taxonOrigineSqcAssExt) {
    $this->taxonOrigineSqcAssExt = $taxonOrigineSqcAssExt;

    return $this;
  }

  /**
   * Get taxonOrigineSqcAssExt
   *
   * @return string
   */
  public function getTaxonOrigineSqcAssExt() {
    return $this->taxonOrigineSqcAssExt;
  }

  /**
   * Set commentaireSqcAssExt
   *
   * @param string $commentaireSqcAssExt
   *
   * @return SequenceAssembleeExt
   */
  public function setCommentaireSqcAssExt($commentaireSqcAssExt) {
    $this->commentaireSqcAssExt = $commentaireSqcAssExt;

    return $this;
  }

  /**
   * Get commentaireSqcAssExt
   *
   * @return string
   */
  public function getCommentaireSqcAssExt() {
    return $this->commentaireSqcAssExt;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return SequenceAssembleeExt
   */
  public function setDateCre($dateCre) {
    $this->dateCre = $dateCre;

    return $this;
  }

  /**
   * Get dateCre
   *
   * @return \DateTime
   */
  public function getDateCre() {
    return $this->dateCre;
  }

  /**
   * Set dateMaj
   *
   * @param \DateTime $dateMaj
   *
   * @return SequenceAssembleeExt
   */
  public function setDateMaj($dateMaj) {
    $this->dateMaj = $dateMaj;

    return $this;
  }

  /**
   * Get dateMaj
   *
   * @return \DateTime
   */
  public function getDateMaj() {
    return $this->dateMaj;
  }

  /**
   * Set userCre
   *
   * @param integer $userCre
   *
   * @return SequenceAssembleeExt
   */
  public function setUserCre($userCre) {
    $this->userCre = $userCre;

    return $this;
  }

  /**
   * Get userCre
   *
   * @return integer
   */
  public function getUserCre() {
    return $this->userCre;
  }

  /**
   * Set userMaj
   *
   * @param integer $userMaj
   *
   * @return SequenceAssembleeExt
   */
  public function setUserMaj($userMaj) {
    $this->userMaj = $userMaj;

    return $this;
  }

  /**
   * Get userMaj
   *
   * @return integer
   */
  public function getUserMaj() {
    return $this->userMaj;
  }

  /**
   * Set geneVocFk
   *
   * @param \App\Entity\Voc $geneVocFk
   *
   * @return SequenceAssembleeExt
   */
  public function setGeneVocFk(\App\Entity\Voc $geneVocFk = null) {
    $this->geneVocFk = $geneVocFk;

    return $this;
  }

  /**
   * Get geneVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getGeneVocFk() {
    return $this->geneVocFk;
  }

  /**
   * Set datePrecisionVocFk
   *
   * @param \App\Entity\Voc $datePrecisionVocFk
   *
   * @return SequenceAssembleeExt
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
   * Set origineSqcAssExtVocFk
   *
   * @param \App\Entity\Voc $origineSqcAssExtVocFk
   *
   * @return SequenceAssembleeExt
   */
  public function setOrigineSqcAssExtVocFk(\App\Entity\Voc $origineSqcAssExtVocFk = null) {
    $this->origineSqcAssExtVocFk = $origineSqcAssExtVocFk;

    return $this;
  }

  /**
   * Get origineSqcAssExtVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getOrigineSqcAssExtVocFk() {
    return $this->origineSqcAssExtVocFk;
  }

  /**
   * Set collecteFk
   *
   * @param \App\Entity\Collecte $collecteFk
   *
   * @return SequenceAssembleeExt
   */
  public function setCollecteFk(\App\Entity\Collecte $collecteFk = null) {
    $this->collecteFk = $collecteFk;

    return $this;
  }

  /**
   * Get collecteFk
   *
   * @return \App\Entity\Collecte
   */
  public function getCollecteFk() {
    return $this->collecteFk;
  }

  /**
   * Set statutSqcAssVocFk
   *
   * @param \App\Entity\Voc $statutSqcAssVocFk
   *
   * @return SequenceAssembleeExt
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
   * Add sqcExtEstRealisePar
   *
   * @param \App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
   *
   * @return SequenceAssembleeExt
   */
  public function addSqcExtEstRealisePar(\App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar) {
    $sqcExtEstRealisePar->setSequenceAssembleeExtFk($this);
    $this->sqcExtEstRealisePars[] = $sqcExtEstRealisePar;

    return $this;
  }

  /**
   * Remove sqcExtEstRealisePar
   *
   * @param \App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar
   */
  public function removeSqcExtEstRealisePar(\App\Entity\SqcExtEstRealisePar $sqcExtEstRealisePar) {
    $this->sqcExtEstRealisePars->removeElement($sqcExtEstRealisePar);
  }

  /**
   * Get sqcExtEstRealisePars
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSqcExtEstRealisePars() {
    return $this->sqcExtEstRealisePars;
  }

  /**
   * Add sqcExtEstReferenceDans
   *
   * @param \App\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans
   *
   * @return SequenceAssembleeExt
   */
  public function addSqcExtEstReferenceDans(\App\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans) {
    $sqcExtEstReferenceDans->setSequenceAssembleeExtFk($this);
    $this->sqcExtEstReferenceDanss[] = $sqcExtEstReferenceDans;

    return $this;
  }

  /**
   * Remove sqcExtEstReferenceDans
   *
   * @param \App\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans
   */
  public function removeSqcExtEstReferenceDans(\App\Entity\SqcExtEstReferenceDans $sqcExtEstReferenceDans) {
    $this->sqcExtEstReferenceDanss->removeElement($sqcExtEstReferenceDans);
  }

  /**
   * Get sqcExtEstReferenceDanss
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getSqcExtEstReferenceDanss() {
    return $this->sqcExtEstReferenceDanss;
  }

  /**
   * Add especeIdentifiee
   *
   * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
   *
   * @return SequenceAssembleeExt
   */
  public function addEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee) {
    $especeIdentifiee->setSequenceAssembleeExtFk($this);
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
}
