<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Personne
 *
 * @ORM\Table(name="person",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_person__person_name", columns={"person_name"})},
 *  indexes={@ORM\Index(name="IDX_FCEC9EFE8441376", columns={"institution_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"nomPersonne"}, message="A person with this name is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Personne {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="person_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="person_name", type="string", length=255, nullable=false)
   */
  private $nomPersonne;

  /**
   * @var string
   *
   * @ORM\Column(name="person_full_name", type="string", length=1024, nullable=true)
   */
  private $nomComplet;

  /**
   * @var string
   *
   * @ORM\Column(name="person_name_bis", type="string", length=255, nullable=true)
   */
  private $nomPersonneRef;

  /**
   * @var string
   *
   * @ORM\Column(name="person_comments", type="text", nullable=true)
   */
  private $commentairePersonne;

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
   * @var \Etablissement
   *
   * @ORM\ManyToOne(targetEntity="Etablissement")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $etablissementFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set nomPersonne
   *
   * @param string $nomPersonne
   *
   * @return Personne
   */
  public function setNomPersonne($nomPersonne) {
    $this->nomPersonne = $nomPersonne;

    return $this;
  }

  /**
   * Get nomPersonne
   *
   * @return string
   */
  public function getNomPersonne() {
    return $this->nomPersonne;
  }

  /**
   * Set nomComplet
   *
   * @param string $nomComplet
   *
   * @return Personne
   */
  public function setNomComplet($nomComplet) {
    $this->nomComplet = $nomComplet;

    return $this;
  }

  /**
   * Get nomComplet
   *
   * @return string
   */
  public function getNomComplet() {
    return $this->nomComplet;
  }

  /**
   * Set nomPersonneRef
   *
   * @param string $nomPersonneRef
   *
   * @return Personne
   */
  public function setNomPersonneRef($nomPersonneRef) {
    $this->nomPersonneRef = $nomPersonneRef;

    return $this;
  }

  /**
   * Get nomPersonneRef
   *
   * @return string
   */
  public function getNomPersonneRef() {
    return $this->nomPersonneRef;
  }

  /**
   * Set commentairePersonne
   *
   * @param string $commentairePersonne
   *
   * @return Personne
   */
  public function setCommentairePersonne($commentairePersonne) {
    $this->commentairePersonne = $commentairePersonne;

    return $this;
  }

  /**
   * Get commentairePersonne
   *
   * @return string
   */
  public function getCommentairePersonne() {
    return $this->commentairePersonne;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return Personne
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
   * @return Personne
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
   * @return Personne
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
   * @return Personne
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
   * Set etablissementFk
   *
   * @param \App\Entity\Etablissement $etablissementFk
   *
   * @return Personne
   */
  public function setEtablissementFk(\App\Entity\Etablissement $etablissementFk = null) {
    $this->etablissementFk = $etablissementFk;

    return $this;
  }

  /**
   * Get etablissementFk
   *
   * @return \App\Entity\Etablissement
   */
  public function getEtablissementFk() {
    return $this->etablissementFk;
  }
}
