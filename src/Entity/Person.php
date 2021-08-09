<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Person
 *
 * @ORM\Table(name="person",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_person__person_name", columns={"person_name"})},
 *  indexes={@ORM\Index(name="IDX_FCEC9EFE8441376", columns={"institution_fk"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"nomPersonne"}, message="A person with this name is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Person extends AbstractTimestampedEntity {
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
   * @var \Institution
   *
   * @ORM\ManyToOne(targetEntity="Institution")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="institution_fk", referencedColumnName="id", nullable=true)
   * })
   */
  private $institutionFk;

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
   * @return Person
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
   * @return Person
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
   * @return Person
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
   * @return Person
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
   * Set institutionFk
   *
   * @param \App\Entity\Institution $institutionFk
   *
   * @return Person
   */
  public function setInstitutionFk(\App\Entity\Institution $institutionFk = null) {
    $this->institutionFk = $institutionFk;

    return $this;
  }

  /**
   * Get institutionFk
   *
   * @return \App\Entity\Institution
   */
  public function getInstitutionFk() {
    return $this->institutionFk;
  }
}
