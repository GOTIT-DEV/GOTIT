<?php

namespace App\Controller\Querybuilder;

use App\Services\Querybuilder\QueryBuilderService;
use App\Services\Querybuilder\SchemaInspectorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for querying the GOTIT database.
 *
 * @Route("/qbuilder")
 */
class QueryBuilderController extends AbstractController {

  /**
   * @author Philippe Grison  <philippe.grison@mnhn.fr>
   */
  private $doctrine;
  public function __construct(ManagerRegistry $doctrine) {
    $this->doctrine = $doctrine;
  }

  /**
   * @Route("/", name="query_builder_index")
   * Index : render query form interface
   */
  public function indexAction() {
    return $this->render('QueryBuilder/index.html.twig');
  }

  /**
   * @Route("/init", name="querybuilder_init", methods={"GET"})
   *
   */
  public function init_builders(SchemaInspectorService $service) {
    $config = $service->make_qbuilder_config();
    return new JsonResponse($config);
  }

  /**
   *  @Route("/query", name="qb_make_query", methods={"POST"})
   *
   *  Main function to query the database.
   *  Creates a QueryBuilder with Doctrine.
   *  Returns the response of the query.
   */
  public function query(Request $request, QueryBuilderService $service) {
    $data = json_decode($request->getContent(), true);
    $selectedFields = $service->getSelectFields($data);
    $em = $this->doctrine->getManager();
    $qb = $em->createQueryBuilder();
    $user = $this->getUser();
    $query = $service->makeQuery($data, $qb, $user);
    $q = $query->getQuery();
    $results = $q->getScalarResult();
    return new JsonResponse([
      "dql" => $q->getDql(),
      "sql" => $q->getSql(),
      "results" => $results,
      "fields" => $selectedFields,
    ]);
  }
}
