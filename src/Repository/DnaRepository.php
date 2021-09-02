<?php

namespace App\Repository;

use App\Entity\Dna;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UnexpectedValueException;

final class DnaRepository extends AbstractRepository {

  public function __construct(ManagerRegistry $managerRegistry, ValidatorInterface $validator) {
    parent::__construct($managerRegistry, Dna::class, $validator);
    $this->fetchJoinQueryBuilder = $this->createQueryBuilder('dna')
      ->select('dna, sto, spec, prod, person, inst, pcr')
      ->join('dna.specimenFk', 'spec')
      ->leftJoin('dna.storeFk', 'sto')
      ->leftJoin('dna.pcrs', 'pcr')
      ->leftJoin('dna.dnaProducers', 'prod')
      ->leftJoin('prod.personFk', 'person')
      ->leftJoin('person.institutionFk', 'inst');
  }

  public function getColumnKey(string $column): string {
    $column = $this->toCamelCase($column);
    switch ($column) {
    case "specimenFk":
      $column = "spec.molecularCode";
      break;
    case "storeFk":
      $column = "sto.code";
      break;
    default:
      $column = 'dna.' . $column;
    }
    return $column;
  }

  public function likeTerm($term) {
    return '%' . strtolower($term) . '%';
  }

  public function search(
    string $order = 'ASC', int $perPage = 10,
    int $currentPage = 0, string $sortBy = "id",
    array $terms = [], string $logicalOp = "AND"
  ) {
    $sortBy = $this->getColumnKey($sortBy);
    $qb = $this->fetchJoinQueryBuilder->orderBy($sortBy, $order);

    if (is_array($terms)) {
      $terms = array_filter($terms, function ($t) {return !empty($t);});
      if (!empty($terms)) {
        $keys = array_keys($terms);
        $terms = array_map(function ($t) use ($qb) {return $this->likeTerm($t);}, $terms);
        $constraints = array_map(function ($key) use ($qb) {
          $queryKey = $this->getColumnKey($key);
          return $qb->expr()->like($qb->expr()->lower($queryKey), ':' . $key);
        }, $keys);
        $expr = null;
        if ($logicalOp == "AND") {
          $expr = $qb->expr()->andX(...$constraints);
        } elseif ($logicalOp == "OR") {
          $expr = $qb->expr()->orX(...$constraints);
        }

        if ($expr === null) {
          throw new UnexpectedValueException('Invalid argument : $logicalOp must be AND | OR');
        }
        $qb->andWhere($expr)->setParameters($terms);
      }
    }

    if ($perPage === 0) {
      $items = $qb->getQuery()->getResult();
      return [
        "items" => $items,
        "pagination" => [
          "total_items" => count($items),
        ],
      ];
    }
    return $this->paginate($qb, (int) $perPage, (int) $currentPage);
  }
}