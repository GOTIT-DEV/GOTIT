<?php

namespace App\Controller\Querybuilder;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Querybuilder\QueryBuilderService;





/**
 * Controller for querying the GOTIT database.
 *
 * @Route("/")
 * @Security("has_role('ROLE_INVITED')")
 */
class QueryBuilderController extends Controller
{
  /**
   * @Route("/", name="query_builder_index")
   * Index : render query form interface
   */
  public function indexAction()
  {
    return $this->render('@LehnaQueryBuilder/index.html.twig');
  }


  /**
   * @Route("/init", name="querybuilder_init", methods={"GET"})
   * 
   */
  public function init_builders(QueryBuilderService $service)
  {
    $config = $service->make_qbuilder_config();
    return new JsonResponse($config);
  }

  /**
   *  @Route("/query", name="query_test", methods={"POST"})
   * 
   *  Main function to query the database. 
   *  Creates a QueryBuilder with Doctrine.
   *  Returns the response of the query.
   */
  public function getRequestBuilder(Request $request, QueryBuilderService $service)
  {
    $data = $request->request->all();
    $selectedFields = $service->getSelectFields($data);
    $em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();
    $query = $service->getBlocks($data, $qb); // Getting the info of the blocks 
    $q = $query->getQuery();
    $dqlresults = $q->getDql();
    $sqlresults = $q->getSql();
    $results = $q->getArrayResult();
    return new JsonResponse([
      "dql" => $dqlresults, "sql" => $sqlresults,
      "results" => $this->renderView(
        '@LehnaQueryBuilder/resultQuery.html.twig',
        ["results" => $results, "selectedFields" => $selectedFields]
      )
    ]);
  }
}
