<?php

namespace App\Tests\Helper;

use Doctrine\ORM\Mapping\ClassMetadata;
use Faker\Factory;
use League\FactoryMuffin\Faker\Facade as Faker;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Validation;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Factories extends \Codeception\Module {
  protected $fakerMap = [
    'datetime' => 'dateTime',
    'date' => 'dateTime',
    'string' => 'sentence',
    'text' => 'text',
    'bigint' => 'randomNumber',
    'float' => 'randomFloat',
  ];

  protected $fixedValues = [
    'smallint' => 1,
  ];

  protected $faker;

  private function generate(array $mapping, bool $forceUnique) {
    return $this->fixedValues[$mapping['type']] ?? (
      ($forceUnique || $mapping['unique'])
      ? Faker::unique()->{$this->fakerMap[$mapping['type']]}()
      : Faker::{$this->fakerMap[$mapping['type']]}()
    );
  }

  private function factoryDefFromMetadata(ClassMetadata $metadata, array $constraintsMeta = []): array {
    $definition = [];
    $mappings = $metadata->fieldMappings;
    // dump($metadata->getName());
    $uniqueProperties = \array_reduce($constraintsMeta, function ($acc, $c) {
      // dump($c->fields);
      return ($c instanceof UniqueEntity && \is_array($c->fields)) ?
      array_merge($acc, $c->fields) : $acc;
    }, $initial = []);
    // dump($uniqueProperties);
    foreach ($mappings as $name => $m) {
      if (!$metadata->isIdentifier($name) && !$metadata->isNullable($name)) {
        $forceUnique = in_array($name, $uniqueProperties);
        $definition[$name] = $this->generate($m, $forceUnique);
      }
    }
    foreach ($metadata->getAssociationMappings() as $name => $m) {
      if ($m['isOwningSide']) {
        $isNullable = true;
        foreach ($m['joinColumns'] ?? [] as ['nullable' => $nullable]) {
          if (!$nullable) {
            $isNullable = false;
            break;
          }
        }
        if (!$isNullable) {
          $definition[$name] = "entity|{$m['targetEntity']}";
        }
      }
    }

    return $definition;
  }

  public function _beforeSuite($settings = []) {
    $this->faker = Factory::create();
    $validator = Validation::createValidator();
    $factory = $this->getModule('DataFactory');
    $em = $this->getModule('Doctrine2')->_getEntityManager();
    $metadata = $em->getMetadataFactory()->getAllMetadata();
    foreach ($metadata as $entityMeta) {
      if (false === $entityMeta->isMappedSuperclass) {
        $constraintsMeta = $validator
          ->getMetadataFor($entityMeta->getName())
          ->getConstraints();
        $definition = $this->factoryDefFromMetadata($entityMeta, $constraintsMeta);
        $factory->_define($entityMeta->getName(), $definition);
      }
    }
  }
}
