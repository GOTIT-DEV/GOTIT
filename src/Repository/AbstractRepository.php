<?php

namespace App\Repository;

use App\Repository\ApiRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use League\Csv\Statement;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Validator\ConstraintViolation;
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

    $fieldTargets = [];
    $validationErrors = [];
    $entities = [];

    foreach ($header as $h) {
      preg_match(
        '/(?P<name>\w+)((?P<relation>[\(\[])' .
        '(?P<entity>[^\.#]+)' .
        '#?(?P<vocParent>[^\.]*)' .
        '\.(?P<prop>[^\.]+)' .
        '(\)|\]))?$/', $h, $matches);
      $name = $this->toCamelCase($matches['name']);
      $fieldTargets[$name] = [
        'entity' => ucfirst($this->toCamelCase($matches['entity'] ?? null)),
        'vocParent' => $matches['vocParent'] ?? null,
        'prop' => $this->toCamelCase($matches['prop'] ?? null),
        'isManyToMany' => ($matches['relation'] ?? null) === '[',
      ];
    }

    $renamedHeader = array_keys($fieldTargets);
    $headerErrors = $this->validateHeader($renamedHeader);
    if ($headerErrors) {
      return [
        "errors" => [
          ["line" => 0, "payload" => $headerErrors],
        ],
        "records" => [],
        "entities" => [],
      ];
    }

    $records = $stmt->process($csv, $renamedHeader);

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
    foreach ($fields as $key => $def) {
      if ($def['entity']) {
        if ($def['isManyToMany']) {
          // not implemented
          // throw new InvalidArgumentException("Bad argument");
        } else {
          $conditions = [$def['prop'] => $record[$key]];
          if ((string) $def['vocParent']) {
            $conditions['parent'] = $def['vocParent'];
          }
          $relatedEntity = $this->_em
            ->getRepository('App:' . $def['entity'])
            ->findOneBy($conditions);
          if ($relatedEntity === null) {
            $message = $def['vocParent']
            ? sprintf("Vocabulary term '%s' was not found in parent domain '%s'",
              $def['prop'], $def['vocParent'])
            : sprintf("Related %s entity was not found with %s = '%s'",
              $def['entity'], $def['prop'], $record[$key]);
            $errors[] = new ConstraintViolation(
              $message, $message, $conditions,
              $record, $key, $record[$key],
            );
          } else {
            $entity->{'set' . ucfirst($key)}($relatedEntity);
          }
        }
      } else {
        $value = (is_string($record[$key]) && $record[$key] === "") ? null : $record[$key];
        $entity->{'set' . ucfirst($key)}($value);
      }
    }
    $validation = $this->validator->validate($entity);
    foreach ($errors as $err) {
      $validation->add($err);
    }
    return [
      "entity" => $entity,
      "errors" => $validation,
    ];
  }

}