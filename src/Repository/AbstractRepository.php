<?php

namespace App\Repository;

use App\Repository\ApiRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use League\Csv\Statement;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRepository extends ServiceEntityRepository implements ApiRepositoryInterface {

  /**
   * A doctrine querybuilder that must be instanciated in children classes.
   * In order to optimize fetching and hydration of related entities,
   * it is intended to rely on Doctrine fetch join mecanism :
   * see https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/reference/dql-doctrine-query-language.html#joins
   *
   * @var QueryBuilder
   */
  protected $fetchJoinQueryBuilder;
  protected $validator;

  public function __construct(ManagerRegistry $managerRegistry, string $entityClass, ValidatorInterface $validator) {
    parent::__construct($managerRegistry, $entityClass);
    $this->validator = $validator;
  }

  public function toCamelCase($str) {
    return str_replace('_', '', lcfirst(ucwords($str, '_')));
  }

  public function paginate(QueryBuilder $qb, int $perPage = 10, int $currentPage = 1): Pagerfanta {
    $pager = new Pagerfanta(new QueryAdapter($qb));
    $pager->setCurrentPage($currentPage);
    $pager->setMaxPerPage($perPage);
    return $pager;
  }

  /**
   * Find all entities in the repository is a fetch join.
   *
   * @return array
   */
  public function findAllFetchJoin(): array{
    return $this->fetchJoinQueryBuilder->getQuery()->getResult();
  }

  /**
   * Finds all entities in the repository.
   *
   * @psalm-return list<T> The entities.
   */
  public function findAll($fetch_join = false) {
    return $fetch_join ? $this->findAllFetchJoin() : $this->findBy([]);
  }

  public function validateHeader(array $header) {
    $metadata = $this->_em->getClassMetadata($this->getClassName());
    // ignore metadata fields
    $fieldMappings = array_filter(
      $metadata->fieldMappings,
      function ($field) {return !str_starts_with($field['fieldName'], "meta");}
    );

    $fieldAssoc = $metadata->associationMappings;
    $entityProperties = $fieldMappings + $fieldAssoc;
    $headerProperties = array_fill_keys($header, true);
    $notFoundProperties = array_keys(array_diff_key($headerProperties, $entityProperties));

    $errors = array_map(
      function ($prop) {return [
        "property_path" => $prop,
        "message" => sprintf(
          "Corrupted CSV header : unknown property '%s' for entity %s. " .
          "Please use the provided CSV template.", $prop, $this->getClassName()),
      ];},
      $notFoundProperties);

    return $errors;
  }

  public function parseHeader($header) {
    $fieldTargets = [];
    $headerErrors = [];
    foreach ($header as $h) {
      /**
       * matches :
       * - propertyName
       * - propertyName(relatedEntity.property)
       * - propertyName[relatedEntity.property]
       * - propertyName(relatedEntity#vocParent.property)
       * - propertyName[relatedEntity#vocParent.property]
       */
      preg_match(
        '/(?P<name>\w+)((?P<relation>[\(\[])' .
        '(?P<entity>[^\.#]+)' .
        '#?(?P<vocParent>[^\.]*)' .
        '\.(?P<prop>[^\.]+)' .
        '(\)|\]))?$/', $h, $matches);

      // no match on property name means parsing failure
      if (!$matches['name']) {
        $headerErrors[] = [
          "property_path" => $h,
          "message" => sprintf(
            "Corrupted CSV header : could not parse '%s'. Please use the provided CSV template.", $h
          ),
        ];
      } else {
        $name = $this->toCamelCase($matches['name']);
        $fieldTargets[$name] = [
          'entity' => ucfirst($this->toCamelCase($matches['entity'] ?? null)),
          'property' => $name,
          'vocParent' => $matches['vocParent'] ?? null,
          'prop' => $this->toCamelCase($matches['prop'] ?? null),
          'isManyToMany' => ($matches['relation'] ?? null) === '[',
        ];
      }
    }

    $headerErrors = array_merge($headerErrors, $this->validateHeader(array_keys($fieldTargets)));
    return [
      'errors' => $headerErrors,
      'fields' => $fieldTargets,
    ];
  }

  /**
   * Imports a CSV file containing entity records
   *
   * @param string $csvPath
   * @return void
   */
  public function importCsv($csvPath) {
    $csv = Reader::createFromPath($csvPath, 'r');
    $csv->setHeaderOffset(0)->setDelimiter(';');
    $stmt = Statement::create();
    $header = $stmt->limit(1)->process($csv)->getHeader();
    $headerParsing = $this->parseHeader($header);
    if ($headerParsing['errors']) {
      return [
        "errors" => [
          ["line" => 0, "payload" => $headerParsing['errors']],
        ],
        "records" => [],
        "entities" => [],
      ];
    }
    $fieldTargets = $headerParsing['fields'];
    $renamedHeader = array_keys($fieldTargets);

    $records = $stmt->process($csv, $renamedHeader);

    $validationErrors = [];
    $entities = [];
    $this->_em->getConnection()->beginTransaction();
    foreach ($records as $line => $record) {
      $res = $this->deserializeCsvItem($record, $fieldTargets);
      $entity = $res['entity'];
      $errors = $res['errors'];
      if (0 === count($errors)) {
        $entities[] = $entity;
        $this->_em->persist($entity);
      } else {
        $validationErrors[] = [
          "line" => $line,
          "payload" => $errors,
        ];
      }
    }
    if (count($validationErrors)) {
      $this->_em->getConnection()->rollBack();
    } else {
      $this->_em->flush();
      $this->_em->getConnection()->commit();
    }

    return [
      'entities' => $entities,
      'records' => $records->jsonSerialize(),
      'errors' => $validationErrors,
    ];
  }

  public function parseRelatedProperty($entity, array $record, array $fieldDef) {
    $propertyName = $fieldDef['property'];
    $conditions = [$fieldDef['prop'] => $record[$propertyName]];
    if ((string) $fieldDef['vocParent']) {
      $conditions['parent'] = $fieldDef['vocParent'];
    }
    $relatedEntity = $this->_em
      ->getRepository('App:' . $fieldDef['entity'])
      ->findOneBy($conditions);
    if ($relatedEntity === null) {
      $message = $fieldDef['vocParent']
      ? sprintf("Vocabulary term '%s' was not found in parent domain '%s'",
        $fieldDef['prop'], $fieldDef['vocParent'])
      : sprintf("Related %s entity was not found with %s = '%s'",
        $fieldDef['entity'], $fieldDef['prop'], $record[$propertyName]);
      throw new ConstraintViolationException(
        $message, $entity, $propertyName, $record[$propertyName]
      );
    }
    return $relatedEntity;
  }

  /**
   * Deserialize a CSV record to an Entity object
   *
   * @param array $record
   * @param array $fields
   * @return Entity|Validation
   */
  public function deserializeCsvItem(array $record, array $fields) {
    $entityClass = $this->getClassName();
    $entity = new $entityClass();
    $errors = [];
    foreach ($fields as $property => $def) {
      if ($def['entity']) {
        try {
          if ($def['isManyToMany']) {
            // parse related many-to-many property
            // Not implemented
          } else {
            // parse related entity property
            $relatedEntity = $this->parseRelatedProperty($entity, $record, $def);
            $entity->{'set' . ucfirst($property)}($relatedEntity);
          }
        } catch (ConstraintViolationException $e) {
          $errors[] = $e->getConstraintViolation();
        }
      } else {
        // parse property owned by entity
        $value = ($record[$property] === "") ? null : $record[$property];
        $entity->{'set' . ucfirst($property)}($value);
      }
    }
    // validate entity properties using constraints in its class definition
    $validation = $this->validator->validate($entity);
    // merge all validation errors
    foreach ($errors as $err) {
      $validation->add($err);
    }
    return [
      "entity" => $entity,
      "errors" => $validation,
    ];
  }

}