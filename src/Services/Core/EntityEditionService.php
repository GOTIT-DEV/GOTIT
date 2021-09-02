<?php

namespace App\Services\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service
 */
class EntityEditionService {
  private $entityManager;

  public function __construct(EntityManagerInterface $manager) {
    $this->entityManager = $manager;
  }

  public function copyArrayCollection($collection) {
    return new ArrayCollection($collection->toArray());
  }

  public function removeStaleCollection($originalCollection, $newCollection, $embedProperty = NULL) {
    if ($embedProperty) {
      $getEmbedProperty = 'get' . ucfirst($embedProperty);
      $embedPropertiesArray = $originalCollection->map(
        function ($originalItem) use ($getEmbedProperty) {
          return $originalItem->$getEmbedProperty()->toArray();
        }
      );
      $embedCollection = new ArrayCollection(array_merge(...$embedPropertiesArray));
      foreach ($newCollection as $item) {
        $this->removeStaleCollection($embedCollection, $item->$getEmbedProperty());
      }
    }

    $deletedItems = $originalCollection->filter(
      function ($item) use (&$newCollection) {
        $delete = !($newCollection->contains($item));
        if ($delete) {$this->entityManager->remove($item);}
        return $delete;
      });
    return $deletedItems;
  }
}