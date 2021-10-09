<?php

namespace App\Entity\Abstraction;

use App\Entity\User;

interface TimestampedEntityInterface {
  public function setMetaCreationDate(?\DateTime $metaCreationDate): self;

  public function getMetaCreationDate(): ?\DateTime;

  public function setMetaUpdateDate(?\DateTime $metaUpdateDate): self;

  public function getMetaUpdateDate(): ?\DateTime;

  public function setMetaCreationUser(?User $metaCreationUser): self;

  public function getMetaCreationUser(): ?User;

  public function setMetaUpdateUser(?User $metaUpdateUser): self;

  public function getMetaUpdateUser(): ?User;
}
