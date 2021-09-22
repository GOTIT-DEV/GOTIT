<?php

namespace App\Entity;

use App\Doctrine\SetUserTimestampListener;
use App\Doctrine\TimestampedEntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MappedSuperclass
 * @ORM\EntityListeners({"App\Doctrine\SetUserTimestampListener"})
 *
 */
abstract class AbstractTimestampedEntity implements TimestampedEntityInterface {

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_creation", type="datetime", nullable=true)
   * @Assert\NotBlank(allowNull = true)
   */
  protected $metaCreationDate;

  /**
   * @var \DateTime
   * @ORM\Column(name="date_of_update", type="datetime", nullable=true)
   * @Assert\NotBlank(allowNull = true)
   */
  protected $metaUpdateDate;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(
   *  name="creation_user_name",
   *  referencedColumnName="id",
   *  onDelete="SET NULL",
   *  nullable=true)
   * @Assert\NotBlank(allowNull = true)
   */
  protected $metaCreationUser;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(
   *  name="update_user_name",
   *  referencedColumnName="id",
   *  onDelete="SET NULL",
   *  nullable=true)
   * @Assert\NotBlank(allowNull = true)
   */
  protected $metaUpdateUser;

  /**
   * @SerializedName("_meta")
   * @return array
   */
  public function getMetadata(): array{
    return [
      "creation" => [
        "user" => $this->getMetaCreationUser(),
        "date" => $this->getMetaCreationDate(),
      ],
      "update" => [
        "user" => $this->getMetaUpdateUser(),
        "date" => $this->getMetaUpdateDate(),
      ],
    ];
  }

  /**
   * Set metaCreationDate
   * @param \DateTime $metaCreationDate
   */
  public function setMetaCreationDate(?\DateTime $metaCreationDate) {
    $this->metaCreationDate = $metaCreationDate;
    return $this;
  }

  /**
   * Get metaCreationDate
   * @return \DateTime
   */
  public function getMetaCreationDate(): ?\DateTime {
    return $this->metaCreationDate;
  }

  /**
   * Set metaUpdateDate
   * @param \DateTime $metaUpdateDate
   */
  public function setMetaUpdateDate(?\DateTime $metaUpdateDate) {
    $this->metaUpdateDate = $metaUpdateDate;
    return $this;
  }

  /**
   * Get metaUpdateDate
   * @return \DateTime
   */
  public function getMetaUpdateDate(): ?\DateTime {
    return $this->metaUpdateDate;
  }

  /**
   * Set metaCreationUser
   * @param User $metaCreationUser
   */
  public function setMetaCreationUser(?User $metaCreationUser) {
    $this->metaCreationUser = $metaCreationUser;
    return $this;
  }

  /**
   * Get metaCreationUser
   * @return User
   */
  public function getMetaCreationUser(): ?User {
    return $this->metaCreationUser;
  }

  /**
   * Set metaUpdateUser
   * @param User $metaUpdateUser
   */
  public function setMetaUpdateUser(?User $metaUpdateUser) {
    $this->metaUpdateUser = $metaUpdateUser;
    return $this;
  }

  /**
   * Get metaUpdateUser
   * @return User
   */
  public function getMetaUpdateUser(): ?User {
    return $this->metaUpdateUser;
  }
}