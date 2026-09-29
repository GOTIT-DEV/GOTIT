<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
class Individu {
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
   * @var integer
   *
   * @ORM\Column(name="stock", type="smallint", nullable=false)
   */
  private $stock;


  /**
   * @var string
   *
   * @ORM\Column(name="specimen_comments", type="text", nullable=true)
   */
  private $commentaireInd;

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
   * @ORM\OneToMany(targetEntity="EspeceIdentifiee", mappedBy="individuFk", cascade={"persist"})
   * @ORM\OrderBy({"id" = "ASC"})
   */
  protected $especeIdentifiees;
  
  public function __construct() {
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
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return Individu
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
   * @return Individu
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
   * @return Individu
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
   * @return Individu
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
   * Add especeIdentifiee
   *
   * @param \App\Entity\EspeceIdentifiee $especeIdentifiee
   *
   * @return Individu
   */
  public function addEspeceIdentifiee(\App\Entity\EspeceIdentifiee $especeIdentifiee) {
    $especeIdentifiee->setIndividuFk($this);
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

  public function getStock(): ?int
  {
      return $this->stock;
  }

  public function setStock(int $stock): self
  {
      $this->stock = $stock;

      return $this;
  }
}
