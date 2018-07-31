<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Bbees\E3sBundle\Services\RearrangementsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller pour requête sur le réarrangements des MOTUs
 *
 * @Route("requetes/rearrangements")
 */
class RearrangementsController extends Controller {
  /**
   * @Route("/", name="rearrangements")
   *
   * Rendu du template de la page principale
   */
  public function indexAction(QueryBuilderService $service) {
    // Obtention de la liste des Genus
    $genus_set = $service->getGenusSet();
    # Obtention de la liste des MOTU Datasets
    $doctrine = $this->getDoctrine();
    $datasets = $doctrine->getRepository(Motu::class)->findAll();
    # Rendu du template
    return $this->render('requetes/rearrangements/index.html.twig', array(
      'datasets'  => $datasets,
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/requete", name="requete2")
   *
   * Renvoie un objet JSON pour remplir la table de résultats
   */
  public function searchQuery(Request $request, RearrangementsService $service) {

    $service->fetch($request->request);
    $service->countSeqSta();
    $service->indexResults();

    $service->compare();

    return new JsonResponse($service->getResults());
  }
}