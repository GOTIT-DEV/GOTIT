<?php

namespace App\Repository;

use App\Entity\Dna;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
}