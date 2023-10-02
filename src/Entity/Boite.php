<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
class Boite {
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="LotMateriel", mappedBy="boites", cascade={"persist"})
     * 
     */
    private $lotsMateriels = array();


  /**
   * @ORM\OneToMany(targetEntity="Adn", mappedBy="boiteFk", cascade={"persist"})
   * @ORM\OrderBy({"codeAdn" = "ASC"})
   */
  protected $adns;

  /**
   * @ORM\OneToMany(targetEntity="IndividuLame", mappedBy="boiteFk", cascade={"persist"})
   * @ORM\OrderBy({"codeLameColl" = "ASC"})
   */
  protected $individuLames;

  public function __construct() {
    $this->adns = new ArrayCollection();
    $this->individuLames = new ArrayCollection();
    $this->lotsMateriels = new ArrayCollection();
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
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return Boite
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
   * @return Boite
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
   * @return Boite
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
   * @return Boite
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
   * Add adn
   *
   * @param \App\Entity\Adn $adn
   *
   * @return Boite
   */
  public function addAdn(\App\Entity\Adn $adn) {
    $adn->setBoiteFk($this);
    $this->adns[] = $adn;

    return $this;
  }

  /**
   * Remove adn
   *
   * @param \App\Entity\Adn $adn
   */
  public function removeAdn(\App\Entity\Adn $adn) {
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

  /**
   * @return Collection<int, LotMateriel>
   */
  public function getLotsMateriels(): Collection
  {
      return $this->lotsMateriels;
  }

  public function addLotsMateriel(LotMateriel $lotsMateriel): self
  {
      if (!$this->lotsMateriels->contains($lotsMateriel)) {
          $this->lotsMateriels[] = $lotsMateriel;
          $lotsMateriel->addBoite($this);
      }

      return $this;
  }

  public function removeLotsMateriel(LotMateriel $lotsMateriel): self
  {
      if ($this->lotsMateriels->removeElement($lotsMateriel)) {
          $lotsMateriel->removeBoite($this);
      }

      return $this;
  }
}
