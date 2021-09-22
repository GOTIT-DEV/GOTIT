<?php

namespace App\Repository;

use App\Entity\Dna;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DnaRepository extends AbstractRepository {

  public function __construct(ManagerRegistry $managerRegistry, ValidatorInterface $validator) {
    parent::__construct($managerRegistry, Dna::class, $validator);
    $this->fetchJoinQueryBuilder = $this->createQueryBuilder('dna')
      ->select('dna, sto, spec, prod, inst, pcr')
      ->join('dna.specimen', 'spec')
      ->leftJoin('dna.store', 'sto')
      ->leftJoin('dna.pcrs', 'pcr')
      ->leftJoin('dna.producers', 'prod')
      ->leftJoin('prod.institution', 'inst');
  }

  public function getColumnKey(string $column): string {
    $column = $this->toCamelCase($column);
    switch ($column) {
    case "specimen":
      $column = "spec.molecularCode";
      break;
    case "store":
      $column = "sto.code";
      break;
    default:
      $column = 'dna.' . $column;
    }
    return $column;
  }
}