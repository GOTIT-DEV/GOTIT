<?php

namespace App\Repository;

use App\Repository\ApiRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

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

  public function __construct(ManagerRegistry $managerRegistry, string $entityClass) {
    parent::__construct($managerRegistry, $entityClass);
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
}