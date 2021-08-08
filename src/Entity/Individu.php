<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Individu
 *
 * @ORM\Table(name="specimen",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_specimen__specimen_morphological_code", columns={"specimen_morphological_code"}),
 *      @ORM\UniqueConstraint(name="uk_specimen__specimen_molecular_code", columns={"specimen_molecular_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_5EE42FCE4236D33E", columns={"specimen_type_voc_fk"}),
 *      @ORM\Index(name="IDX_5EE42FCE54DBBD4D", columns={"internal_biological_material_fk"})
 * })
 * @ORM\Entity
 * @UniqueEntity(fields={"codeIndTriMorpho"}, message="This code is already registered")
 * @UniqueEntity(fields={"codeIndTriMorpho"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Individu extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="specimen_id_seq ", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_molecular_code", type="string", length=255, nullable=true)
   */
  private $codeIndBiomol;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_morphological_code", type="string", length=255, nullable=false)
   */
  private $codeIndTriMorpho;

  /**
   * @var string
   *
   * @ORM\Column(name="tube_code", type="string", length=255, nullable=false)
   */
  private $codeTube;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_molecular_number", type="string", length=255, nullable=true)
   */
  private $numIndBiomol;

  /**
   * @var string
   *
   * @ORM\Column(name="specimen_comments", type="text", nullable=true)
   */
  private $commentaireInd;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="specimen_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $typeIndividuVocFk;

  /**
   * @var \LotMateriel
   *
   * @ORM\ManyToOne(targetEntity="LotMateriel")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_biological_material_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $lotMaterielFk;

  /**
   * @ORM\OneToMany(targetEntity="TaxonIdentification", mappedBy="individuFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $taxonIdentifications;

  public function __construct() {
    $this->taxonIdentifications = new ArrayCollection();
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
   * Set codeIndBiomol
   *
   * @param string $codeIndBiomol
   *
   * @return Individu
   */
  public function setCodeIndBiomol($codeIndBiomol) {
    $this->codeIndBiomol = $codeIndBiomol;

    return $this;
  }

  /**
   * Get codeIndBiomol
   *
   * @return string
   */
  public function getCodeIndBiomol() {
    return $this->codeIndBiomol;
  }

  /**
   * Set codeIndTriMorpho
   *
   * @param string $codeIndTriMorpho
   *
   * @return Individu
   */
  public function setCodeIndTriMorpho($codeIndTriMorpho) {
    $this->codeIndTriMorpho = $codeIndTriMorpho;

    return $this;
  }

  /**
   * Get codeIndTriMorpho
   *
   * @return string
   */
  public function getCodeIndTriMorpho() {
    return $this->codeIndTriMorpho;
  }

  /**
   * Set codeTube
   *
   * @param string $codeTube
   *
   * @return Individu
   */
  public function setCodeTube($codeTube) {
    $this->codeTube = $codeTube;

    return $this;
  }

  /**
   * Get codeTube
   *
   * @return string
   */
  public function getCodeTube() {
    return $this->codeTube;
  }

  /**
   * Set numIndBiomol
   *
   * @param string $numIndBiomol
   *
   * @return Individu
   */
  public function setNumIndBiomol($numIndBiomol) {
    $this->numIndBiomol = $numIndBiomol;

    return $this;
  }

  /**
   * Get numIndBiomol
   *
   * @return string
   */
  public function getNumIndBiomol() {
    return $this->numIndBiomol;
  }

  /**
   * Set commentaireInd
   *
   * @param string $commentaireInd
   *
   * @return Individu
   */
  public function setCommentaireInd($commentaireInd) {
    $this->commentaireInd = $commentaireInd;

    return $this;
  }

  /**
   * Get commentaireInd
   *
   * @return string
   */
  public function getCommentaireInd() {
    return $this->commentaireInd;
  }

  /**
   * Set typeIndividuVocFk
   *
   * @param \App\Entity\Voc $typeIndividuVocFk
   *
   * @return Individu
   */
  public function setTypeIndividuVocFk(\App\Entity\Voc $typeIndividuVocFk = null) {
    $this->typeIndividuVocFk = $typeIndividuVocFk;

    return $this;
  }

  /**
   * Get typeIndividuVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getTypeIndividuVocFk() {
    return $this->typeIndividuVocFk;
  }

  /**
   * Set lotMaterielFk
   *
   * @param \App\Entity\LotMateriel $lotMaterielFk
   *
   * @return Individu
   */
  public function setLotMaterielFk(\App\Entity\LotMateriel $lotMaterielFk = null) {
    $this->lotMaterielFk = $lotMaterielFk;

    return $this;
  }

  /**
   * Get lotMaterielFk
   *
   * @return \App\Entity\LotMateriel
   */
  public function getLotMaterielFk() {
    return $this->lotMaterielFk;
  }

  /**
   * Add taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   *
   * @return Individu
   */
  public function addTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $taxonIdentification->setIndividuFk($this);
    $this->taxonIdentifications[] = $taxonIdentification;

    return $this;
  }

  /**
   * Remove taxonIdentification
   *
   * @param \App\Entity\TaxonIdentification $taxonIdentification
   */
  public function removeTaxonIdentification(\App\Entity\TaxonIdentification $taxonIdentification) {
    $this->taxonIdentifications->removeElement($taxonIdentification);
  }

  /**
   * Get taxonIdentifications
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTaxonIdentifications() {
    return $this->taxonIdentifications;
  }
}
