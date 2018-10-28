<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller pour l'affichage de la carte de richesse
 *
 * @Route("/queries/distribution")
 * @Security("has_role('ROLE_INVITED')")
 */
class MotuDistributionController extends Controller {

  /**
   * @Route("/", name="distribution")
   *
   * Rendu du template de la page principale
   */
  public function index(QueryBuilderService $service) {
    # Services
    $doctrine = $this->getDoctrine();
    // obtention de la liste des datasets,
    // utilisée pour adaptation des colonnes de la table
    $datasets = $doctrine->getRepository(Motu::class)->findAll();
    # obtention de la liste des genres
    $genus_set    = $service->getGenusSet();
    $methods_list = $service->listMethodsByDate();
    # Rendu du template
    return $this->render('requetes/motu-distribution/index.html.twig', array(
      'genus_set'    => $genus_set,
      'datasets'     => $datasets,
      'methods_list' => $methods_list,
    ));
  }

  /**
   * @Route("/query", name="distribution-query")
   *
   * Renvoie le JSON utilisé pour remplir la table de résultats (rows),
   * et afficher la carte de richesse (geo)
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # Raccourci requete POST
    $data = $request->request;
    # Obtention de la localisation géographique
    $res = $service->getMotuGeoLocation($data);
    $methode = $service->getMethod($data->get('methode'), $data->get('dataset'));
    # Renvoi réponse JSON
    return new JsonResponse(array(
      'query'   => $data->all(),
      'rows'    => $res,
      'methode' => $methode,
    ));
  }
}
