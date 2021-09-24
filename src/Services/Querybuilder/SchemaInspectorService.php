<?php

namespace App\Services\Querybuilder;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchemaInspectorService {

  public function __construct(EntityManagerInterface $em, TranslatorInterface $translator) {
    $this->em = $em;
    $this->translator = $translator;
  }

  public function make_qbuilder_config() {
    $meta = $this->em->getMetadataFactory()->getAllMetadata();
    // dump($meta);

    $schema = $this->parse_entities_metadata($meta);

    // Fetch content of tables to be used as select options
    $schema['Voc']['content'] = $this->em
      ->createQuery('select v.id, v.code, v.label, v.parent from App:Voc v')
      ->getArrayResult();

    return $schema;
  }

  public function parse_entities_metadata(&$metadata_array) {
    $res = [];
    $relations = [];
    foreach ($metadata_array as $m) {
      $entity = $this->parse_entity_name($m->getName());
      $tableName = $m->getTableName();

      // Skip User entity that must not be exposed
      if ($entity == "User" || $m->isMappedSuperclass) {
        continue;
      }

      // Parse entity
      $res[$entity] = $this->parse_metadata($m);
      // dump($m->getAssociationMappings());
      // Parse entity relations
      $relations[$entity] = array_reduce(
        $m->getAssociationMappings(),
        function ($acc, $relation) use ($tableName) {
          if (isset($relation["joinColumns"])) {
            $target = $this->parse_entity_name($relation['targetEntity']);
            $acc[$target] = $acc[$target] ?? [];
            $acc[$target][] = [
              "entity" => $this->parse_entity_name($relation["targetEntity"]),
              "from" => [
                'id' => $relation["fieldName"],
                'label' => $this->translator->trans($tableName . '.' . $relation['joinColumns'][0]['name'], [], 'fields'),
              ],
              "to" => [
                "id" => 'id',
                "label" => 'id',
              ],
            ];
          }
          return $acc;
        },
        []);
    }

    # add relations the other way back
    $this->reverse_relations($relations);

    foreach ($res as $entity => $data) {
      $res[$entity]["relations"] = $relations[$entity];
      $res[$entity]['type'] = $this->guess_type($entity);
    }
    return $res;
  }

  private function parse_entity_name($entity) {
    $entity = explode('\\', $entity);
    return array_pop($entity);
  }

  private function reverse_relations(&$relations) {
    foreach ($relations as $sourceEntity => $targets) {
      foreach ($targets as $targetEntity => $data) {
        $reverse_relation = function (&$d) use (&$sourceEntity) {
          return [
            "entity" => $sourceEntity,
            "from" => $d["to"],
            "to" => $d["from"],
          ];
        };
        $relations[$targetEntity][$sourceEntity] = array_map(
          $reverse_relation,
          $data
        );
      }
    }
  }

  private function guess_type($entity) {
    return (int) preg_match('/(^APour|Par$|Dans$|InternalSequenceAssembly|TargetTaxon|Fixative|SamplingMethod)/', $entity);
  }

  private function parse_metadata(ClassMetadata $metadata) {
    $class = $metadata->getName();
    $table = $metadata->getTableName();

    $make_filter = function ($field) use ($table) {

      $filter = [
        "id" => $field['fieldName'],
        "name" => $field['columnName'],
        "label" => $this->translator->trans($table . '.' . $field['columnName'], [], "fields"),
        "type" => null,
        "attrs" => [],
        "choices" => [],
      ];
      $filter = $this->parse_field_type($field['type'], $filter);

      if ($table == "vocabulary" && $field['fieldName'] == "parent") {
        $filter['choices'] = array_map('current', $this->em
            ->createQuery('select distinct v.parent from App:Voc v')
            ->getArrayResult());
        $filter['type'] = 'custom-component';
      }

      return $filter;
    };
    $filters = array_values(array_map($make_filter, $metadata->fieldMappings));
    return [
      "class" => $class,
      "filters" => $filters,
      "entity" => $this->parse_entity_name($class),
      "name" => $table,
      "label" => $this->translator->trans($table, [], "tables")
    ];
  }

  private function parse_field_type(String $type, array $filter) {
    $t = $type;
    if (strpos($type, "int") != false) {
      $t = "numeric";
    } elseif ($type == "float") {
      $t = "numeric";
      $filter['attrs']['step'] = 0.0001;
    } elseif ($type == "string") {
      $t = "text";
    } elseif (strpos($type, "bool") != false) {
      $t = "boolean";
    }

    $filter['type'] = $t;

    return $filter;
  }
}
