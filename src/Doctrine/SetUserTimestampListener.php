<?php

namespace App\Doctrine;

use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Doctrine\TimestampedEntityInterface;

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
    if (!$entity->getUserCre() && $user) {
      $entity->setUserCre($user);
    }
    if (!$entity->getDateCre()) {
      $entity->setDateCre(new \DateTime());
      $entity->setDateMaj(new \DateTime());
    }

  }

  public function preUpdate(TimestampedEntityInterface $entity, PreUpdateEventArgs $args) {
    $user = $this->security->getUser();
    $entity->setUserMaj($user);
    $entity->setDateMaj(new \DateTime());
  }

}