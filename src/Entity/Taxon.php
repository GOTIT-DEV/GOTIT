<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Taxon
 *
 * @ORM\Table(name="taxon",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_taxon__taxon_name", columns={"taxon_name"}),
 *      @ORM\UniqueConstraint(name="uk_taxon__taxon_code", columns={"taxon_code"}) } )
 * @ORM\Entity
 * @UniqueEntity(fields={"taxname"}, message="This name already exists")
 * @UniqueEntity(fields={"codeTaxon"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Taxon extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="taxon_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_name", type="string", length=255, nullable=false)
   */
  private $taxname;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_full_name", type="string", length=255, nullable=true)
   */
  private $taxonFullName;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_rank", type="string", length=255, nullable=false)
   */
  private $rank;

  /**
   * @var string
   *
   * @ORM\Column(name="subclass", type="string", length=255, nullable=true)
   */
  private $subclass;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_order", type="string", length=255, nullable=true)
   */
  private $ordre;

  /**
   * @var string
   *
   * @ORM\Column(name="family", type="string", length=255, nullable=true)
   */
  private $family;

  /**
   * @var string
   *
   * @ORM\Column(name="genus", type="string", length=255, nullable=true)
   */
  private $genus;

  /**
   * @var string
   *
   * @ORM\Column(name="species", type="string", length=255, nullable=true)
   */
  private $species;

  /**
   * @var string
   *
   * @ORM\Column(name="subspecies", type="string", length=255, nullable=true)
   */
  private $subspecies;

  /**
   * @var integer
   *
   * @ORM\Column(name="taxon_validity", type="smallint", nullable=false)
   */
  private $validity;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_code", type="string", length=255, nullable=false)
   */
  private $codeTaxon;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_comments", type="text", nullable=true)
   */
  private $commentaireRef;

  /**
   * @var string
   *
   * @ORM\Column(name="clade", type="string", length=255, nullable=true)
   */
  private $clade;

  /**
   * @var string
   *
   * @ORM\Column(name="taxon_synonym", type="string", length=255, nullable=true)
   */
  private $taxnameRef;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set taxname
   *
   * @param string $taxname
   *
   * @return Taxon
   */
  public function setTaxname($taxname) {
    $this->taxname = $taxname;

    return $this;
  }

  /**
   * Get taxname
   *
   * @return string
   */
  public function getTaxname() {
    return $this->taxname;
  }

  /**
   * Set rank
   *
   * @param string $rank
   *
   * @return Taxon
   */
  public function setRank($rank) {
    $this->rank = $rank;

    return $this;
  }

  /**
   * Get rank
   *
   * @return string
   */
  public function getRank() {
    return $this->rank;
  }

  /**
   * Set subclass
   *
   * @param string $subclass
   *
   * @return Taxon
   */
  public function setSubclass($subclass) {
    $this->subclass = $subclass;

    return $this;
  }

  /**
   * Get subclass
   *
   * @return string
   */
  public function getSubclass() {
    return $this->subclass;
  }

  /**
   * Set ordre
   *
   * @param string $ordre
   *
   * @return Taxon
   */
  public function setOrdre($ordre) {
    $this->ordre = $ordre;

    return $this;
  }

  /**
   * Get ordre
   *
   * @return string
   */
  public function getOrdre() {
    return $this->ordre;
  }

  /**
   * Set family
   *
   * @param string $family
   *
   * @return Taxon
   */
  public function setFamily($family) {
    $this->family = $family;

    return $this;
  }

  /**
   * Get family
   *
   * @return string
   */
  public function getFamily() {
    return $this->family;
  }

  /**
   * Set genus
   *
   * @param string $genus
   *
   * @return Taxon
   */
  public function setGenus($genus) {
    $this->genus = $genus;

    return $this;
  }

  /**
   * Get genus
   *
   * @return string
   */
  public function getGenus() {
    return $this->genus;
  }

  /**
   * Set species
   *
   * @param string $species
   *
   * @return Taxon
   */
  public function setSpecies($species) {
    $this->species = $species;

    return $this;
  }

  /**
   * Get species
   *
   * @return string
   */
  public function getSpecies() {
    return $this->species;
  }

  /**
   * Set subspecies
   *
   * @param string $subspecies
   *
   * @return Taxon
   */
  public function setSubspecies($subspecies) {
    $this->subspecies = $subspecies;

    return $this;
  }

  /**
   * Get subspecies
   *
   * @return string
   */
  public function getSubspecies() {
    return $this->subspecies;
  }

  /**
   * Set validity
   *
   * @param integer $validity
   *
   * @return Taxon
   */
  public function setValidity($validity) {
    $this->validity = $validity;

    return $this;
  }

  /**
   * Get validity
   *
   * @return integer
   */
  public function getValidity() {
    return $this->validity;
  }

  /**
   * Set codeTaxon
   *
   * @param string $codeTaxon
   *
   * @return Taxon
   */
  public function setCodeTaxon($codeTaxon) {
    $this->codeTaxon = $codeTaxon;

    return $this;
  }

  /**
   * Get codeTaxon
   *
   * @return string
   */
  public function getCodeTaxon() {
    return $this->codeTaxon;
  }

  /**
   * Set commentaireRef
   *
   * @param string $commentaireRef
   *
   * @return Taxon
   */
  public function setCommentaireRef($commentaireRef) {
    $this->commentaireRef = $commentaireRef;

    return $this;
  }

  /**
   * Get commentaireRef
   *
   * @return string
   */
  public function getCommentaireRef() {
    return $this->commentaireRef;
  }

  /**
   * Set clade
   *
   * @param string $clade
   *
   * @return Taxon
   */
  public function setClade($clade) {
    $this->clade = $clade;

    return $this;
  }

  /**
   * Get clade
   *
   * @return string
   */
  public function getClade() {
    return $this->clade;
  }

  /**
   * Set taxnameRef
   *
   * @param string $taxnameRef
   *
   * @return Taxon
   */
  public function setTaxnameRef($taxnameRef) {
    $this->taxnameRef = $taxnameRef;

    return $this;
  }

  /**
   * Get taxnameRef
   *
   * @return string
   */
  public function getTaxnameRef() {
    return $this->taxnameRef;
  }

  /**
   * Set taxonFullName
   *
   * @param string $taxonFullName
   *
   * @return Taxon
   */
  public function setTaxonFullName($taxonFullName) {
    $this->taxonFullName = $taxonFullName;

    return $this;
  }

  /**
   * Get taxonFullName
   *
   * @return string
   */
  public function getTaxonFullName() {
    return $this->taxonFullName;
  }
}
