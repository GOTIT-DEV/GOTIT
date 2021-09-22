<?php

namespace App\Repository;

use App\Entity\Pcr;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PcrRepository extends AbstractRepository {

  public function __construct(ManagerRegistry $managerRegistry, ValidatorInterface $validator) {
    parent::__construct($managerRegistry, Pcr::class, $validator);
    $this->fetchJoinQueryBuilder = $this->createQueryBuilder('pcr')
      ->select('pcr, dna, spec, prod, person, inst, pcr')
      ->join('pcr.dna', 'dna')
      ->leftJoin('pcr.producers', 'prod')
      ->leftJoin('prod.personFk', 'person')
      ->leftJoin('person.institution', 'inst');
  }

  public function getColumnKey(string $column): string {
    $column = $this->toCamelCase($column);
    switch ($column) {
    case "dna":
      $column = "dna.code";
      break;
    default:
      $column = 'pcr.' . $column;
    }
    return $column;
  }
}