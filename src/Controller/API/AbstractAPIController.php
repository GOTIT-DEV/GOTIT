<?php

namespace App\Controller\API;

use App\Entity\Dna;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\FileParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * DNA API controller
 *
 * @Rest\Route("/dna")
 * @Security("is_granted('ROLE_INVITED')")
 */
abstract class AbstractAPIController {

  protected $repository;

  /**
   * @Rest\QueryParam(name="perPage", requirements="\d+",
   *     default="15", description="Max number of entities per page."
   * )
   * @Rest\QueryParam(name="currentPage", requirements="\d+",
   *     default="1", description="The pagination offset"
   * )
   * @Rest\QueryParam(name="sortBy", default="id",
   *    description="The sorting column"
   * )
   * @Rest\QueryParam(name="sortDesc", requirements="true|false",
   *    default="false", description="The sorting column"
   * )
   * @Rest\QueryParam(name="filter", map=true,
   *    default=null, description="List of search terms"
   * )
   * @Rest\QueryParam(name="filterop", requirements="AND|OR",
   *    default="AND", description="Logical operator to use between terms"
   * )
   *
   */
  public function list(ParamFetcherInterface $params) {
    /**
     * Relying on DQL fetch join implementation to efficiently
     * retrieve many-to-many association to Person
     */
    return $this->repository->search(
      $order = $params->get('sortDesc') === "true" ? "DESC" : "ASC",
      $perPage = $params->get('perPage'),
      $currentPage = $params->get('currentPage'),
      $sortBy = $params->get('sortBy'),
      $terms = (array) $params->get('filter'),
      $logicalOp = $params->get('filterop')
    );
  }

  // abstract public function create(Dna $dna);

  public function delete(Dna $dna, EntityManagerInterface $em) {
    $em->remove($dna);
    try {
      $em->flush();
    } catch (ForeignKeyConstraintViolationException $e) {
      return $this->view(
        ["message" => $e->getMessage()],
        Response::HTTP_BAD_REQUEST
      );
    }
    return;
  }

  /**
   * @Rest\Post("/import", format="json")
   * @Rest\FileParam(name="csvFile", key="csvFile", description="CSV file to import")
   * @Rest\View(serializerGroups={"field", "dna_list"}, StatusCode = 201)
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function import(ParamFetcherInterface $params) {
    $file = $params->get("csvFile");
    $results = $this->repository->importCsv($file->getRealPath());
    if ($results['errors']) {
      return $this->view($results, Response::HTTP_BAD_REQUEST);
    }
    return $results;
  }
}
