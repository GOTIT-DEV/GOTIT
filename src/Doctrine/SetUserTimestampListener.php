<?php

namespace App\Doctrine;

use App\Doctrine\TimestampedEntityInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * A doctrine event listener to keep track of the user creating or updating entities
 */
class SetUserTimestampListener {

  private $security;

  public function __construct(Security $security) {
    $this->security = $security;
  }

  public function prePersist(TimestampedEntityInterface $entity, LifecycleEventArgs $args) {
    $user = $this->security->getUser();
    if (!$entity->getMetaCreationUser() && $user) {
      $entity->setMetaCreationUser($user);
    }
    if (!$entity->getMetaCreationDate()) {
      $entity->setMetaCreationDate(new \DateTime());
      $entity->setMetaUpdateDate(new \DateTime());
    }

  }

  public function preUpdate(TimestampedEntityInterface $entity, PreUpdateEventArgs $args) {
    $user = $this->security->getUser();
    $entity->setMetaUpdateUser($user);
    $entity->setMetaUpdateDate(new \DateTime());
  }

}