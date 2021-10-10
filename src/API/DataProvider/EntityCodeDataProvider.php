<?php

namespace App\API\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * A custom data provider to allow referencing entities by their `code` property.
 */
class EntityCodeDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface {
  public function __construct(private ManagerRegistry $managerRegistry) {
  }

  public function supports(
    string $resourceClass,
    ?string $operationName = null,
    array $context = []
  ): bool {
    return isset(class_uses($resourceClass)['CompositeCodeEntityTrait'])
            || property_exists($resourceClass, 'code');
  }

  public function getItem(
    string $resourceClass,
    $id,
    ?string $operationName = null,
    array $context = []
  ) {
    $manager = $this->managerRegistry->getManagerForClass($resourceClass);
    $repository = $manager->getRepository($resourceClass);
    $useIntegerId = is_numeric($id);

    return $useIntegerId ? $repository->find($id) : $repository->findOneBy(['code' => $id]);
  }
}
