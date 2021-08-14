<?php

namespace App\Controller\API;

use App\Entity\Dna;
use App\Repository\DnaRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DNA API controller
 *
 * @Rest\Route("/dna")
 * @Security("is_granted('ROLE_INVITED')")
 */
class DnaController extends AbstractFOSRestController {

  private $dnaRepository;

  public function __construct(DnaRepository $repo) {
    $this->dnaRepository = $repo;
  }

  /**
   * @Rest\Get("/{id}", requirements = {"id"="\d+"})
   * @Rest\View(serializerGroups={"field", "dna_details"})
   */
  public function show(Dna $dna) {
    return $dna;
  }

  /**
   * @Rest\Get("/")
   * @Rest\QueryParam(
   *     name="order",
   *     requirements="asc|desc",
   *     default="asc",
   *     description="Sort order (asc or desc)"
   * )
   * @Rest\QueryParam(
   *     name="perPage",
   *     requirements="\d+",
   *     default="15",
   *     description="Max number of entities per page."
   * )
   * @Rest\QueryParam(
   *     name="currentPage",
   *     requirements="\d+",
   *     default="1",
   *     description="The pagination offset"
   * )
   * @Rest\QueryParam(
   *    name="sortBy",
   *    default="id",
   *    description="The sorting column"
   * )
   * @Rest\QueryParam(
   *    name="sortDesc",
   *    default="false",
   *    requirements="true|false",
   *    description="The sorting column"
   * )
   * @Rest\QueryParam(
   *    map=true,
   *    name="filter",
   *    default=null,
   *    description="List of search terms"
   * )
   * @Rest\QueryParam(
   *    name="filterop",
   *    default="AND",
   *    requirements="AND|OR",
   *    description="List of search terms"
   * )
   *
   * @Rest\View(serializerGroups={"field", "dna_list"})
   */
  public function list(ParamFetcherInterface $params) {
    /**
     * Relying on DQL fetch join implementation to efficiently
     * retrieve many-to-many association to Person
     */
    $order = $params->get('sortDesc') === "true" ? "DESC" : "ASC";
    $pager = $this->dnaRepository->search(
      $order = $order,
      $perPage = $params->get('perPage'),
      $currentPage = $params->get('currentPage'),
      $sortBy = $params->get('sortBy'),
      $terms = (array) $params->get('filter'),
      $logicalOp = $params->get('filterop')
    );
    return $pager;
  }


  /**
   * @Rest\Delete("/{id}", requirements = {"id"="\d+"})
   * @Rest\View(StatusCode=204)
   */
  public function delete(Dna $dna, EntityManagerInterface $em) {
    $em->remove($dna);
    $em->flush();
    return;
  }
}
