<?php

namespace App\Repository;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;

interface ApiRepositoryInterface {
  public function paginate(QueryBuilder $qb, int $perPage, int $currentPage): Pagerfanta;
  public function findAllFetchJoin(): array;
  public function getColumnKey(string $column): string;
  public function search(string $order, int $perPage, int $currentPage, string $sortBy, array $terms, string $logicalOp);
}