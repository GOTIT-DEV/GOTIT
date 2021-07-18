<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Etablissement
 *
 * @ORM\Table(name="institution",
 * uniqueConstraints={@ORM\UniqueConstraint(name="uk_institution__institution_name", columns={"institution_name"})})
 * @ORM\Entity
 * @UniqueEntity(fields={"nomEtablissement"}, message="This name already exists")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class Etablissement {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="institution_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_name", type="string", length=1024, nullable=false)
   */
  private $nomEtablissement;

  /**
   * @var string
   *
   * @ORM\Column(name="institution_comments", type="text", nullable=true)
   */
  private $commentaireEtablissement;

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
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set nomEtablissement
   *
   * @param string $nomEtablissement
   *
   * @return Etablissement
   */
  public function setNomEtablissement($nomEtablissement) {
    $this->nomEtablissement = $nomEtablissement;

    return $this;
  }

  /**
   * Get nomEtablissement
   *
   * @return string
   */
  public function getNomEtablissement() {
    return $this->nomEtablissement;
  }

  /**
   * Set commentaireEtablissement
   *
   * @param string $commentaireEtablissement
   *
   * @return Etablissement
   */
  public function setCommentaireEtablissement($commentaireEtablissement) {
    $this->commentaireEtablissement = $commentaireEtablissement;

    return $this;
  }

  /**
   * Get commentaireEtablissement
   *
   * @return string
   */
  public function getCommentaireEtablissement() {
    return $this->commentaireEtablissement;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return Etablissement
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
   * @return Etablissement
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
   * @return Etablissement
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
   * @return Etablissement
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
}
