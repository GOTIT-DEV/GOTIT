<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Boite
 *
 * @ORM\Table(name="storage_box",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_storage_box__box_code", columns={"box_code"})},
 *  indexes={
 *      @ORM\Index(name="IDX_7718EDEF9E7B0E1F", columns={"collection_type_voc_fk"}),
 *      @ORM\Index(name="IDX_7718EDEF41A72D48", columns={"collection_code_voc_fk"}),
 *      @ORM\Index(name="IDX_7718EDEF57552D30", columns={"box_type_voc_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeBoite"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Boite extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="storage_box_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="box_code", type="string", length=255, nullable=false)
   */
  private $codeBoite;

  /**
   * @var string
   *
   * @ORM\Column(name="box_title", type="string", length=1024, nullable=false)
   */
  private $libelleBoite;

  /**
   * @var string
   *
   * @ORM\Column(name="box_comments", type="text", nullable=true)
   */
  private $commentaireBoite;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="collection_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $typeCollectionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="collection_code_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $codeCollectionVocFk;

  /**
   * @var \Voc
   *
   * @ORM\ManyToOne(targetEntity="Voc")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="box_type_voc_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $typeBoiteVocFk;

  /**
   * @ORM\OneToMany(targetEntity="LotMateriel", mappedBy="boiteFk", cascade={"persist"})
   * @ORM\OrderBy({"codeLotMateriel" = "ASC"})
   */
  protected $lotMateriels;

  /**
   * @ORM\OneToMany(targetEntity="Dna", mappedBy="boiteFk", cascade={"persist"})
   * @ORM\OrderBy({"codeAdn" = "ASC"})
   */
  protected $adns;

  /**
   * @ORM\OneToMany(targetEntity="IndividuLame", mappedBy="boiteFk", cascade={"persist"})
   * @ORM\OrderBy({"codeLameColl" = "ASC"})
   */
  protected $individuLames;

  public function __construct() {
    $this->lotMateriels = new ArrayCollection();
    $this->adns = new ArrayCollection();
    $this->individuLames = new ArrayCollection();
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
   * Set codeBoite
   *
   * @param string $codeBoite
   *
   * @return Boite
   */
  public function setCodeBoite($codeBoite) {
    $this->codeBoite = $codeBoite;

    return $this;
  }

  /**
   * Get codeBoite
   *
   * @return string
   */
  public function getCodeBoite() {
    return $this->codeBoite;
  }

  /**
   * Set libelleBoite
   *
   * @param string $libelleBoite
   *
   * @return Boite
   */
  public function setLibelleBoite($libelleBoite) {
    $this->libelleBoite = $libelleBoite;

    return $this;
  }

  /**
   * Get libelleBoite
   *
   * @return string
   */
  public function getLibelleBoite() {
    return $this->libelleBoite;
  }

  /**
   * Set commentaireBoite
   *
   * @param string $commentaireBoite
   *
   * @return Boite
   */
  public function setCommentaireBoite($commentaireBoite) {
    $this->commentaireBoite = $commentaireBoite;

    return $this;
  }

  /**
   * Get commentaireBoite
   *
   * @return string
   */
  public function getCommentaireBoite() {
    return $this->commentaireBoite;
  }

  /**
   * Set typeCollectionVocFk
   *
   * @param \App\Entity\Voc $typeCollectionVocFk
   *
   * @return Boite
   */
  public function setTypeCollectionVocFk(\App\Entity\Voc $typeCollectionVocFk = null) {
    $this->typeCollectionVocFk = $typeCollectionVocFk;

    return $this;
  }

  /**
   * Get typeCollectionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getTypeCollectionVocFk() {
    return $this->typeCollectionVocFk;
  }

  /**
   * Set codeCollectionVocFk
   *
   * @param \App\Entity\Voc $codeCollectionVocFk
   *
   * @return Boite
   */
  public function setCodeCollectionVocFk(\App\Entity\Voc $codeCollectionVocFk = null) {
    $this->codeCollectionVocFk = $codeCollectionVocFk;

    return $this;
  }

  /**
   * Get codeCollectionVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getCodeCollectionVocFk() {
    return $this->codeCollectionVocFk;
  }

  /**
   * Set typeBoiteVocFk
   *
   * @param \App\Entity\Voc $typeBoiteVocFk
   *
   * @return Boite
   */
  public function setTypeBoiteVocFk(\App\Entity\Voc $typeBoiteVocFk = null) {
    $this->typeBoiteVocFk = $typeBoiteVocFk;

    return $this;
  }

  /**
   * Get typeBoiteVocFk
   *
   * @return \App\Entity\Voc
   */
  public function getTypeBoiteVocFk() {
    return $this->typeBoiteVocFk;
  }

  /**
   * Add lotMateriel
   *
   * @param \App\Entity\LotMateriel $lotMateriel
   *
   * @return Boite
   */
  public function addLotMateriel(\App\Entity\LotMateriel $lotMateriel) {
    $lotMateriel->setBoiteFk($this);
    $this->lotMateriels[] = $lotMateriel;

    return $this;
  }

  /**
   * Remove lotMateriel
   *
   * @param \App\Entity\LotMateriel $lotMateriel
   */
  public function removeLotMateriel(\App\Entity\LotMateriel $lotMateriel) {
    $this->lotMateriels->removeElement($lotMateriel);
  }

  /**
   * Get lotMateriels
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getLotMateriels() {
    return $this->lotMateriels;
  }

  /**
   * Add adn
   *
   * @param \App\Entity\Dna $adn
   *
   * @return Boite
   */
  public function addAdn(\App\Entity\Dna $adn) {
    $adn->setBoiteFk($this);
    $this->adns[] = $adn;

    return $this;
  }

  /**
   * Remove adn
   *
   * @param \App\Entity\Dna $adn
   */
  public function removeAdn(\App\Entity\Dna $adn) {
    $this->adns->removeElement($adn);
  }

  /**
   * Get adns
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getAdns() {
    return $this->adns;
  }

  /**
   * Add individuLame
   *
   * @param \App\Entity\IndividuLame $individuLame
   *
   * @return Boite
   */
  public function addIndividuLame(\App\Entity\IndividuLame $individuLame) {
    $individuLame->setBoiteFk($this);
    $this->individuLames[] = $individuLame;

    return $this;
  }

  /**
   * Remove individuLame
   *
   * @param \App\Entity\IndividuLame $individuLame
   */
  public function removeIndividuLame(\App\Entity\IndividuLame $individuLame) {
    $this->individuLames->removeElement($individuLame);
  }

  /**
   * Get individuLames
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getIndividuLames() {
    return $this->individuLames;
  }
}
