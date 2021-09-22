<?php

namespace App\Entity\Abstraction;

use App\Entity\User;

interface TimestampedEntityInterface {
  public function setMetaCreationDate(?\DateTime $metaCreationDate);
  public function getMetaCreationDate(): ?\DateTime;
  public function setMetaUpdateDate(?\DateTime $metaUpdateDate);
  public function getMetaUpdateDate(): ?\DateTime;
  public function setMetaCreationUser(?User $metaCreationUser);
  public function getMetaCreationUser(): ?User;
  public function setMetaUpdateUser(?User $metaUpdateUser);
  public function getMetaUpdateUser(): ?User;
}