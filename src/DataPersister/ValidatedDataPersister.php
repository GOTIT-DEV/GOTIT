<?php

namespace App\DataPersister;

use ApiPlatform\Core\Bridge\Doctrine\Common\DataPersister;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * https://api-platform.com/docs/core/data-persisters/#decorating-the-built-in-data-persisters
 */
class ValidatedDataPersister implements ContextAwareDataPersisterInterface {
  private $decorated;

  public function __construct(
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
                ManagerRegistry $reg,
    ) {
    $this->decorated = new DataPersister($reg);
  }

  public function supports($data, array $context = []): bool {
    return $this->decorated->supports($data);
  }

  public function persist($data, array $context = []): object {
    return $this->decorated->persist($data);
  }

  public function remove($data, array $context = []) {
    $this->validator->validate($data, ['groups' => ['delete']]);
    $this->em->remove($data);
    $this->em->flush();
  }
}
