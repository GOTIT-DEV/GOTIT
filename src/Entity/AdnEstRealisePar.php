<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdnEstRealisePar
 *
 * @ORM\Table(name="dna_is_extracted_by",
 *  indexes={
 *      @ORM\Index(name="IDX_B786C5214B06319D", columns={"dna_fk"}),
 *      @ORM\Index(name="IDX_B786C521B53CD04C", columns={"person_fk"})})
 * @ORM\Entity
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class AdnEstRealisePar {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="dna_is_extracted_by_id_seq", allocationSize=1, initialValue=1)
   */
  private $id;

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
   * @var \Dna
   *
   * @ORM\ManyToOne(targetEntity="Dna", inversedBy="adnEstRealisePars")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="dna_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $adnFk;

  /**
   * @var \Personne
   *
   * @ORM\ManyToOne(targetEntity="Personne")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="person_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $personneFk;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set dateCre
   *
   * @param \DateTime $dateCre
   *
   * @return AdnEstRealisePar
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
   * @return AdnEstRealisePar
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
   * @return AdnEstRealisePar
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
   * @return AdnEstRealisePar
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
   * Set adnFk
   *
   * @param \App\Entity\Dna $adnFk
   *
   * @return AdnEstRealisePar
   */
  public function setAdnFk(\App\Entity\Dna $adnFk = null) {
    $this->adnFk = $adnFk;

    return $this;
  }

  /**
   * Get adnFk
   *
   * @return \App\Entity\Dna
   */
  public function getAdnFk() {
    return $this->adnFk;
  }

  /**
   * Set personneFk
   *
   * @param \App\Entity\Personne $personneFk
   *
   * @return AdnEstRealisePar
   */
  public function setPersonneFk(\App\Entity\Personne $personneFk = null) {
    $this->personneFk = $personneFk;

    return $this;
  }

  /**
   * Get personneFk
   *
   * @return \App\Entity\Personne
   */
  public function getPersonneFk() {
    return $this->personneFk;
  }
}
