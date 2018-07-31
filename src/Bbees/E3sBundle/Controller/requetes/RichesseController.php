<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller pour l'affichage de la carte de richesse
 *
 * @Route("/requetes/richesse")
 */
class RichesseController extends Controller {

  /**
   * @Route("/", name="richesse")
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
    return $this->render('requetes/richesse/index.html.twig', array(
      'genus_set'    => $genus_set,
      'datasets'     => $datasets,
      'methods_list' => $methods_list,
    ));
  }

  /**
   * @Route("/methodes", name="richesse-methodes")
   */
  public function listMethods(Request $request, QueryBuilderService $service) {
    $methodes = $service->listMethodsByDate();
    return new JsonResponse($methodes);
  }

  /**
   * @Route("/requete4", name="requete4")
   *
   * Renvoie le JSON utilisé pour remplir la table de résultats (rows),
   * et afficher la carte de richesse (geo)
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # Raccourci requete POST
    $data = $request->request;
    # Obtention de la localisation géographique
    $res = $service->getMotuGeoLocation($data);
    # Renvoyer résultats vides si aucun filtrage sur taxon/methode
    $geo_res = [];
    $methode = [];
    if ($data->get('methode')) {
      $geo_res = $service->getMotuGeoLocation($data, true);
      $methode = $service->getMethod($data->get('methode'), $data->get('date_methode'));
    }
    # Renvoi réponse JSON
    return new JsonResponse(array(
      'rows'    => $res,
      'geo'     => $geo_res,
      'methode' => $methode,
    ));
  }
}
