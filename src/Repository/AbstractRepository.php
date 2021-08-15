<?php

namespace App\Repository;

use App\Repository\ApiRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
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
    foreach ($header as $h) {
      preg_match(
        '/(?P<name>\w+)((?P<relation>[\(\[])' .
        '(?P<entity>[^\.#]+)' .
        '#?(?P<vocParent>[^\.]*)' .
        '\.(?P<prop>[^\.]+)' .
        '(\)|\]))?$/', $h, $matches);
      $name = $this->toCamelCase($matches['name']);
      $fieldTargets[$name] = [
        'entity' => $matches['entity'] ?? null,
        'vocParent' => $matches['vocParent'] ?? null,
        'prop' => $matches['prop'] ?? null,
        'isManyToMany' => ($matches['relation'] ?? null) === '[',
      ];
    }
    $renamedHeader = array_keys($fieldTargets);
    $records = $stmt->process($csv, $renamedHeader);
    foreach ($records as $record) {
      $entity = $this->deserializeCsvItem($record, $fieldTargets);
      $this->_em->persist($entity);
    }
    $this->_em->flush();
    return $records;
  }

  /**
   * Deserialize a CSV record to an Entity object
   *
   * @param array $record
   * @param array $fields
   * @return Entity
   */
  public function deserializeCsvItem(array $record, array $fields) {
    $entityClass = $this->getClassName();
    $entity = new $entityClass();
    foreach ($fields as $key => $def) {
      if ($def['entity']) {
        if ($def['isManyToMany']) {
          //not implemented
        } else {
          $conditions = [$this->toCamelCase($def['prop']) => $record[$key]];
          if ((string) $def['vocParent']) {
            $conditions['parent'] = $def['vocParent'];
          }
          $relatedEntity = $this->_em
            ->getRepository('App:' . $def['entity'])
            ->findOneBy($conditions);
          if ($relatedEntity === null) {
            $e = EntityNotFoundException::fromClassNameAndIdentifier($def['entity'], $conditions);
            $e->record = $record;
            throw $e;
          } else {
            $entity->{'set' . ucfirst($key)}($relatedEntity);
          }
        }
      } else {
        $value = (is_string($record[$key]) && $record[$key] === "") ? null : $record[$key];
        $entity->{'set' . ucfirst($key)}($value);
      }
    }
    $errors = $this->validator->validate($entity);
    return $entity;
  }

}