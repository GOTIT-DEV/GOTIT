<?php

namespace App\Services\Querybuilder;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;

class SchemaInspectorService
{

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }
  public function make_qbuilder_config()
  {
    $meta = $this->em->getMetadataFactory()->getAllMetadata();
    $schema = $this->parse_entities_metadata($meta);
    $schema['Voc']['content'] = $this->em
      ->createQuery('select v.id, v.code, v.libelle, v.parent from App:Voc v')
      ->getArrayResult();
    return $schema;
  }



  public function parse_entities_metadata(&$metadata_array)
  {
    $parse_relation = function ($acc, $relation) {
      if (array_key_exists("joinColumns", $relation)) {
        $target = $this->parse_entity_name($relation['targetEntity']);
        $acc[$target] = $acc[$target] ?? [];
        $acc[$target][] = $this->parse_associated($relation);
      }
      return $acc;
    };

    $res = [];
    $relations = [];
    foreach ($metadata_array as $m) {
      $entity = $this->parse_entity_name($m->getName());
      // Skip User entity since it is not part of the main process
      if ($entity == "User") continue;

      $res[$entity] = $this->parse_metadata($m);
      $relations[$entity] = array_reduce($m->getAssociationMappings(), $parse_relation, []);
    }

    $this->reverse_relations($relations);

    foreach ($res as $entity => $data) {
      $res[$entity]["relations"] = $relations[$entity];
      $res[$entity]['type'] = $this->guess_type($entity);
    }
    return $res;
  }

  private function parse_entity_name($entity)
  {
    $entity = explode('\\', $entity);
    return array_pop($entity);
  }


  private function reverse_relations(&$relations)
  {
    foreach ($relations as $sourceEntity => $targets) {
      foreach ($targets as $targetEntity => $data) {
        $reverse_relation = function (&$d) use (&$sourceEntity) {
          return [
            "entity" => $sourceEntity,
            "from" => $d["to"],
            "to" => $d["from"]
          ];
        };
        $relations[$targetEntity][$sourceEntity] = array_map(
          $reverse_relation,
          $data
        );
      }
    }
  }

  private function guess_type($entity)
  {
    return (int) preg_match('/(^APour|Par$|Dans$|EstAligneEtTraite|ACibler)/', $entity);
  }

  private function parse_associated($mapping)
  {
    return [
      "entity" => $this->parse_entity_name($mapping["targetEntity"]),
      "from" => $mapping["fieldName"],
      "to" => "id"
    ];
  }

  private function parse_metadata(ClassMetadata $metadata)
  {
    $entity = $metadata->getName();
    $name = $this->parse_entity_name($entity);

    $make_filter = function ($field) use ($name) {
      $res = [
        "id" => $field['fieldName'],
        "label" => $field['fieldName'],
        "type" => $this->convert_field_type($field['type']),
        "choices" => []
      ];

      if ($name == "Voc" && $field['fieldName'] == "parent") {
        $res['choices'] = array_map('current', $this->em
          ->createQuery('select distinct v.parent from App:Voc v')
          ->getArrayResult());
        $res['type'] = 'custom-component';
      }

      return $res;
    };

    $filters = array_values(array_map($make_filter, $metadata->fieldMappings));
    return [
      "class" => $entity,
      "filters" => $filters,
      "name" => $name,
      "table" => $metadata->table["name"]
    ];
  }

  private function convert_field_type($type)
  {
    if (strpos($type, "int") != false) {
      $type = "numeric";
    } elseif ($type == "float") {
      $type = "numeric";
    } elseif ($type == "string") {
      // $type = "string";
      $type = "text";
    } elseif (strpos($type, "bool") != false) {
      $type = "boolean";
    }
    return $type;
  }
}
