<?php

namespace App\Doctrine;

interface TimestampedEntityInterface {
  public function setMetaCreationDate($metaCreationDate);
  public function getMetaCreationDate();
  public function setMetaUpdateDate($metaUpdateDate);
  public function getMetaUpdateDate();
  public function setMetaCreationUser($metaCreationUser);
  public function getMetaCreationUser();
  public function setMetaUpdateUser($metaUpdateUser);
  public function getMetaUpdateUser();
}