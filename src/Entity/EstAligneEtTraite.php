<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EstAligneEtTraite
 *
 * @ORM\Table(name="chromatogram_is_processed_to",
 *  indexes={
 *      @ORM\Index(name="IDX_BD45639EEFCFD332", columns={"chromatogram_fk"}),
 *      @ORM\Index(name="IDX_BD45639E5BE90E48", columns={"internal_sequence_fk"})})
 * @ORM\Entity
 * @UniqueEntity(
 *  fields={"chromatogrammeFk", "sequenceAssembleeFk"},
 *  message = "Duplicated sequence to chromatogram relation"
 * )
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class EstAligneEtTraite {
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="bigint", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @ORM\SequenceGenerator(sequenceName="chromatogram_is_processed_to_id_seq", allocationSize=1, initialValue=1)
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
   * @var \Chromatogramme
   *
   * @ORM\ManyToOne(targetEntity="Chromatogramme", inversedBy="assemblages")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="chromatogram_fk", referencedColumnName="id", nullable=false)
   * })
   */
  private $chromatogrammeFk;

  /**
   * @var \SequenceAssemblee
   *
   * @ORM\ManyToOne(targetEntity="SequenceAssemblee", inversedBy="estAligneEtTraites")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="internal_sequence_fk", referencedColumnName="id", nullable=false, onDelete="CASCADE")
   * })
   */
  private $sequenceAssembleeFk;

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
   * @return EstAligneEtTraite
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
   * @return EstAligneEtTraite
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
   * @return EstAligneEtTraite
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
   * @return EstAligneEtTraite
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
   * Set chromatogrammeFk
   *
   * @param \App\Entity\Chromatogramme $chromatogrammeFk
   *
   * @return EstAligneEtTraite
   */
  public function setChromatogrammeFk(\App\Entity\Chromatogramme $chromatogrammeFk = null) {
    $this->chromatogrammeFk = $chromatogrammeFk;

    return $this;
  }

  /**
   * Get chromatogrammeFk
   *
   * @return \App\Entity\Chromatogramme
   */
  public function getChromatogrammeFk() {
    return $this->chromatogrammeFk;
  }

  /**
   * Set sequenceAssembleeFk
   *
   * @param \App\Entity\SequenceAssemblee $sequenceAssembleeFk
   *
   * @return EstAligneEtTraite
   */
  public function setSequenceAssembleeFk(\App\Entity\SequenceAssemblee $sequenceAssembleeFk = null) {
    $this->sequenceAssembleeFk = $sequenceAssembleeFk;

    return $this;
  }

  /**
   * Get sequenceAssembleeFk
   *
   * @return \App\Entity\SequenceAssemblee
   */
  public function getSequenceAssembleeFk() {
    return $this->sequenceAssembleeFk;
  }
}
