<?php

namespace App\Entity;

use App\Doctrine\SetUserTimestampListener;
use App\Doctrine\TimestampedEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

/**
 * @MappedSuperclass
 * @ORM\EntityListeners({"App\Doctrine\SetUserTimestampListener"})
 */
abstract class AbstractTimestampedEntity implements TimestampedEntityInterface {

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
   */
  private $metaCreationDate;

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
   */
  private $metaUpdateDate;

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
  private $metaCreationUser;

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
  private $metaUpdateUser;

  /**
   * Set metaCreationDate
   * @param \DateTime $metaCreationDate
   */
  public function setMetaCreationDate($metaCreationDate) {
    $this->metaCreationDate = $metaCreationDate;
    return $this;
  }

  /**
   * Get metaCreationDate
   * @return \DateTime
   */
  public function getMetaCreationDate() {
    return $this->metaCreationDate;
  }

  /**
   * Set metaUpdateDate
   * @param \DateTime $metaUpdateDate
   */
  public function setMetaUpdateDate($metaUpdateDate) {
    $this->metaUpdateDate = $metaUpdateDate;
    return $this;
  }

  /**
   * Get metaUpdateDate
   * @return \DateTime
   */
  public function getMetaUpdateDate() {
    return $this->metaUpdateDate;
  }

  /**
   * Set metaCreationUser
   * @param integer $metaCreationUser
   */
  public function setMetaCreationUser($metaCreationUser) {
    $this->metaCreationUser = $metaCreationUser;
    return $this;
  }

  /**
   * Get metaCreationUser
   * @return integer
   */
  public function getMetaCreationUser() {
    return $this->metaCreationUser;
  }

  /**
   * Set metaUpdateUser
   * @param integer $metaUpdateUser
   */
  public function setMetaUpdateUser($metaUpdateUser) {
    $this->metaUpdateUser = $metaUpdateUser;
    return $this;
  }

  /**
   * Get metaUpdateUser
   * @return integer
   */
  public function getMetaUpdateUser() {
    return $this->metaUpdateUser;
  }
}