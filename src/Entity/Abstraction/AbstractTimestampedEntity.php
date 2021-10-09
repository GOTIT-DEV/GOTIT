<?php

namespace App\Entity\Abstraction;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Entity\User;
use App\Listener\SetUserTimestampListener;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\MappedSuperclass]
#[ORM\EntityListeners([SetUserTimestampListener::class])]
abstract class AbstractTimestampedEntity implements TimestampedEntityInterface {
  #[ORM\Column(name: 'date_of_creation', type: 'datetime', nullable: true)]
  #[ApiProperty(writable: false, readable: false)]
  #[NotBlank(allowNull: true)]
  protected ?\DateTime $metaCreationDate = null;

  #[ORM\Column(name: 'date_of_update', type: 'datetime', nullable: true)]
  #[ApiProperty(writable: false, readable: false)]
  #[NotBlank(allowNull: true)]
  protected ?\DateTime $metaUpdateDate = null;

  #[ORM\ManyToOne(targetEntity: 'User')]
  #[ORM\JoinColumn(name: 'creation_user_name', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
  #[ApiProperty(writable: false, readable: false)]
  #[NotBlank(allowNull: true)]
  protected ?User $metaCreationUser = null;

  #[ORM\ManyToOne(targetEntity: 'User')]
  #[ORM\JoinColumn(name: 'update_user_name', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
  #[ApiProperty(writable: false, readable: false)]
  #[NotBlank(allowNull: true)]
  protected ?User $metaUpdateUser = null;

  #[SerializedName('_meta')]
  public function getMetadata(): array {
    return [
      'creation' => [
        'user' => $this->getMetaCreationUser(),
        'date' => $this->getMetaCreationDate(),
      ],
      'update' => [
        'user' => $this->getMetaUpdateUser(),
        'date' => $this->getMetaUpdateDate(),
      ],
    ];
  }

  public function setMetaCreationDate(?\DateTime $metaCreationDate): self {
    $this->metaCreationDate = $metaCreationDate;

    return $this;
  }

  public function getMetaCreationDate(): ?\DateTime {
    return $this->metaCreationDate;
  }

  public function setMetaUpdateDate(?\DateTime $metaUpdateDate): self {
    $this->metaUpdateDate = $metaUpdateDate;

    return $this;
  }

  public function getMetaUpdateDate(): ?\DateTime {
    return $this->metaUpdateDate;
  }

  public function setMetaCreationUser(?User $metaCreationUser): self {
    $this->metaCreationUser = $metaCreationUser;

    return $this;
  }

  public function getMetaCreationUser(): ?User {
    return $this->metaCreationUser;
  }

  public function setMetaUpdateUser(?User $metaUpdateUser): self {
    $this->metaUpdateUser = $metaUpdateUser;

    return $this;
  }

  public function getMetaUpdateUser(): ?User {
    return $this->metaUpdateUser;
  }
}
