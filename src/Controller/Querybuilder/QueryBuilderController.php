<?php

namespace App\Controller\Querybuilder;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Services\Querybuilder\SchemaInspectorService;
use App\Services\Querybuilder\QueryBuilderService;

/**
 * Controller for querying the GOTIT database.
 *
 * @Route("/qbuilder")
 * @Security("has_role('ROLE_INVITED')")
 */
class QueryBuilderController extends AbstractController
{
  /**
   * @Route("/", name="query_builder_index")
   * Index : render query form interface
   */
  public function indexAction()
  {
    return $this->render('QueryBuilder/index.html.twig');
  }


  /**
   * @Route("/init", name="querybuilder_init", methods={"GET"})
   * 
   */
  public function init_builders(SchemaInspectorService $service)
  {
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
  public function query(Request $request, QueryBuilderService $service)
  {
    $data = $request->request->all();
    $selectedFields = $service->getSelectFields($data);
    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();
    $query = $service->makeQuery($data, $qb);
    $q = $query->getQuery();
    $dqlresults = $q->getDql();
    $sqlresults = $q->getSql();
    $results = $q->getArrayResult();
    return new JsonResponse([
      "dql" => $dqlresults,
      "sql" => $sqlresults,
      "results" => $this->renderView(
        'QueryBuilder/resultQuery.html.twig',
        ["results" => $results, "selectedFields" => $selectedFields]
      )
    ]);
  }
}
