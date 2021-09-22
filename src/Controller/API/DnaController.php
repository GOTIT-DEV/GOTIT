<?php

namespace App\Controller\API;

use App\Entity\Dna;
use App\Repository\DnaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DNA API controller
 *
 * @Security("is_granted('ROLE_INVITED')")
 */
class DnaController {

  public function __construct(DnaRepository $repo) {
    $this->repository = $repo;
  }

  // /**
  //  * @Rest\Post("/")
  //  * @Rest\View(StatusCode = 201)
  //  * @Rest\View(serializerGroups={"field", "dna_details"})
  //  */
  // public function show(Dna $dna) {
  //   return $dna;
  // }

  // /**
  //  * @Rest\Get("/")
  //  * @Rest\View(serializerGroups={"field", "dna_list"})
  //  */
  // public function list(ParamFetcherInterface $params) {
  //   return parent::list($params);
  // }

  // /**
  //  * @Rest\Post("/")
  //  * @Rest\View(StatusCode = 201)
  //  */
  // public function create(Dna $dna) {
  //   dump($dna);
  //   die;
  // }

  // /**
  //  * @Rest\Delete("/{id}", requirements = {"id"="\d+"})
  //  * @Rest\View(StatusCode=204)
  //  */
  // public function delete(Dna $dna, EntityManagerInterface $em) {
  //   return parent::delete($dna, $em);
  // }

  // /**
  //  * @Rest\Post("/import", format="json")
  //  * @Rest\FileParam(name="csvFile", key="csvFile", description="CSV file to import")
  //  * @Rest\View(serializerGroups={"field", "dna_list"}, StatusCode = 201)
  //  * @Security("is_granted('ROLE_COLLABORATION')")
  //  */
  // public function import(ParamFetcherInterface $params) {
  //   return parent::import($params);
  // }
}
