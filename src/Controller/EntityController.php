<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class EntityController extends AbstractController {
  protected $entityManager;
  public function __construct(EntityManagerInterface $em) {
    $this->entityManager = $em;
  }

  public function getUser(): User | null {
    return parent::getUser();
  }

  public function getRepository($class): EntityRepository {
    return $this->entityManager->getRepository($class);
  }
}
