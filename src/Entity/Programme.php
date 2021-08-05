<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Programme
 *
 * @ORM\Table(name="program",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="uk_program__program_code", columns={"program_code"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"codeProgramme"}, message="This code is already registered")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Programme extends AbstractTimestampedEntity {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="program_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="program_code", type="string", length=255, nullable=false)
   */
  private $codeProgramme;

  /**
   * @var string
   *
   * @ORM\Column(name="program_name", type="string", length=1024, nullable=false)
   */
  private $nomProgramme;

  /**
   * @var string
   *
   * @ORM\Column(name="coordinator_names", type="text", nullable=false)
   */
  private $nomsResponsables;

  /**
   * @var string
   *
   * @ORM\Column(name="funding_agency", type="string", length=1024, nullable=true)
   */
  private $typeFinanceur;

  /**
   * @var integer
   *
   * @ORM\Column(name="starting_year", type="bigint", nullable=true)
   */
  private $anneeDebut;

  /**
   * @var integer
   *
   * @ORM\Column(name="ending_year", type="bigint", nullable=true)
   */
  private $anneeFin;

  /**
   * @var string
   *
   * @ORM\Column(name="program_comments", type="text", nullable=true)
   */
  private $commentaireProgramme;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set codeProgramme
   *
   * @param string $codeProgramme
   *
   * @return Programme
   */
  public function setCodeProgramme($codeProgramme) {
    $this->codeProgramme = $codeProgramme;

    return $this;
  }

  /**
   * Get codeProgramme
   *
   * @return string
   */
  public function getCodeProgramme() {
    return $this->codeProgramme;
  }

  /**
   * Set nomProgramme
   *
   * @param string $nomProgramme
   *
   * @return Programme
   */
  public function setNomProgramme($nomProgramme) {
    $this->nomProgramme = $nomProgramme;

    return $this;
  }

  /**
   * Get nomProgramme
   *
   * @return string
   */
  public function getNomProgramme() {
    return $this->nomProgramme;
  }

  /**
   * Set nomsResponsables
   *
   * @param string $nomsResponsables
   *
   * @return Programme
   */
  public function setNomsResponsables($nomsResponsables) {
    $this->nomsResponsables = $nomsResponsables;

    return $this;
  }

  /**
   * Get nomsResponsables
   *
   * @return string
   */
  public function getNomsResponsables() {
    return $this->nomsResponsables;
  }

  /**
   * Set typeFinanceur
   *
   * @param string $typeFinanceur
   *
   * @return Programme
   */
  public function setTypeFinanceur($typeFinanceur) {
    $this->typeFinanceur = $typeFinanceur;

    return $this;
  }

  /**
   * Get typeFinanceur
   *
   * @return string
   */
  public function getTypeFinanceur() {
    return $this->typeFinanceur;
  }

  /**
   * Set anneeDebut
   *
   * @param integer $anneeDebut
   *
   * @return Programme
   */
  public function setAnneeDebut($anneeDebut) {
    $this->anneeDebut = $anneeDebut;

    return $this;
  }

  /**
   * Get anneeDebut
   *
   * @return integer
   */
  public function getAnneeDebut() {
    return $this->anneeDebut;
  }

  /**
   * Set anneeFin
   *
   * @param integer $anneeFin
   *
   * @return Programme
   */
  public function setAnneeFin($anneeFin) {
    $this->anneeFin = $anneeFin;

    return $this;
  }

  /**
   * Get anneeFin
   *
   * @return integer
   */
  public function getAnneeFin() {
    return $this->anneeFin;
  }

  /**
   * Set commentaireProgramme
   *
   * @param string $commentaireProgramme
   *
   * @return Programme
   */
  public function setCommentaireProgramme($commentaireProgramme) {
    $this->commentaireProgramme = $commentaireProgramme;

    return $this;
  }

  /**
   * Get commentaireProgramme
   *
   * @return string
   */
  public function getCommentaireProgramme() {
    return $this->commentaireProgramme;
  }
}
