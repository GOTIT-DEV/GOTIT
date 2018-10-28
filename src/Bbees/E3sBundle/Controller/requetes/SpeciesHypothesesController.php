<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Bbees\E3sBundle\Services\RearrangementsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller pour requête sur le réarrangements des MOTUs
 *
 * @Route("requetes/species-hypotheses")
 * @Security("has_role('ROLE_INVITED')")
 */
class SpeciesHypothesesController extends Controller {
  /**
   * @Route("/", name="species-hypotheses")
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
    return $this->render('requetes/species-hypotheses/index.html.twig', array(
      'datasets'  => $datasets,
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="species-hypotheses-query")
   *
   * Renvoie un objet JSON pour remplir la table de résultats
   */
  public function searchQuery(Request $request, RearrangementsService $service) {
    $result = $service->processQuery($request->request);

    return new JsonResponse($result);
  }
}