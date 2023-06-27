<?php

namespace App\Controller\Querybuilder;

use App\Services\Querybuilder\QueryBuilderService;
use App\Services\Querybuilder\SchemaInspectorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controller for querying the GOTIT database.
 */
#[Route("/qbuilder")]
class QueryBuilderController extends AbstractController {

  /**
   * Index : render query form interface
   */
  #[Route("/", name: "query_builder_index")]
  public function indexAction() {
    return $this->render('QueryBuilder/index.html.twig');
  }

  #[Route("/init", name: "querybuilder_init", methods: ["GET"])]
  public function init_builders(SchemaInspectorService $service) {
    $config = $service->make_qbuilder_config();
    return new JsonResponse($config);
  }

  /**
   *
   *  Main function to query the database.
   *  Creates a QueryBuilder with Doctrine.
   *  Returns the response of the query.
   */
  #[Route("/query", name: "qb_make_query", methods: ["POST"])]
  public function query(Request $request, EntityManagerInterface $em, QueryBuilderService $service) {
    $data = json_decode($request->getContent(), true);
    $selectedFields = $service->getSelectFields($data);
    $qb = $em->createQueryBuilder();
    $user = $this->getUser();
    $query = $service->makeQuery($data, $qb, $user)
      ->getQuery();
    $results = $query->getScalarResult();
    return new JsonResponse([
      "dql" => $query->getDql(),
      "sql" => $query->getSql(),
      "results" => $results,
      "fields" => $selectedFields,
    ]);
  }
}
