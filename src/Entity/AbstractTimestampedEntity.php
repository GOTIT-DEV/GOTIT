<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping as ORM;
use App\Doctrine\TimestampedEntityInterface;
use App\Doctrine\SetUserTimestampListener;

/**
 * @MappedSuperclass
 * @ORM\EntityListeners({"App\Doctrine\SetUserTimestampListener"})
 */
abstract class AbstractTimestampedEntity implements TimestampedEntityInterface {

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
   */
  private $dateCre;

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
   */
  private $dateMaj;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(
   *  name="creation_user_name",
   *  referencedColumnName="id",
   *  nullable=true,
   *  onDelete="SET NULL")
   */
  private $userCre;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(
   *  name="update_user_name",
   *  referencedColumnName="id",
   *  nullable=true,
   *  onDelete="SET NULL")
   */
  private $userMaj;

  /**
   * Set dateCre
   * @param \DateTime $dateCre
   */
  public function setDateCre($dateCre) {
    $this->dateCre = $dateCre;
    return $this;
  }

  /**
   * Get dateCre
   * @return \DateTime
   */
  public function getDateCre() {
    return $this->dateCre;
  }

  /**
   * Set dateMaj
   * @param \DateTime $dateMaj
   */
  public function setDateMaj($dateMaj) {
    $this->dateMaj = $dateMaj;
    return $this;
  }

  /**
   * Get dateMaj
   * @return \DateTime
   */
  public function getDateMaj() {
    return $this->dateMaj;
  }

  /**
   * Set userCre
   * @param integer $userCre
   */
  public function setUserCre($userCre) {
    $this->userCre = $userCre;
    return $this;
  }

  /**
   * Get userCre
   * @return integer
   */
  public function getUserCre() {
    return $this->userCre;
  }

  /**
   * Set userMaj
   * @param integer $userMaj
   */
  public function setUserMaj($userMaj) {
    $this->userMaj = $userMaj;
    return $this;
  }

  /**
   * Get userMaj
   * @return integer
   */
  public function getUserMaj() {
    return $this->userMaj;
  }
}