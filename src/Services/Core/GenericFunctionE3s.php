<?php

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service GenericFunctionE3s
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class GenericFunctionE3s {
  private $entityManager;

  public function __construct(EntityManagerInterface $manager) {
    $this->entityManager = $manager;
  }

  public function GetMetaCreationUserId($entity) {
    return ($entity->getMetaCreationUser() !== null) ? $entity->getMetaCreationUser() : 0;
  }

  public function GetMetaCreationUserUsername($entity) {
    $user = $entity->getMetaCreationUser();
    return $user ? $user->getUsername() : 'NA';
  }

  public function GetMetaUpdateUserUsername($entity) {
    $user = $entity->getMetaUpdateUser();
    return $user ? $user->getUsername() : 'NA';
  }

  public function GetMetaCreationUserUserfullname($entity) {
    $user = $entity->getMetaCreationUser();
    return $user ? $user->getName() : 'NA';
  }

  public function GetMetaUpdateUserUserfullname($entity) {
    $user = $entity->getMetaUpdateUser();
    return $user ? $user->getName() : 'NA';
  }
}