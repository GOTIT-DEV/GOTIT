<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller pour les requêtes sur la couverture d'échantillonnage
 * par espèce, sur le gène COI
 *
 * @Route("/requetes/concordance")
 */
class ConcordanceTaxoController extends Controller {

  /**
   * @Route("/", name="concordance")
   *
   * Rendu du template de la page principale
   */
  public function index(QueryBuilderService $service) {
    # obtention de la liste des genres
    $genus_set = $service->getGenusSet();
    # Rendu du template
    return $this->render('requetes/concordance/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/search", name="concordance-search")
   *
   * Données JSON pour remplir la table de résultats
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # Raccourci requête POST
    $data = $request->request;
    dump($data);

    # Obtention des données géographiques
    $res = $service->getSpeciesAssignment($data);
    dump($res);
    # Renvoi réponse JSON
    return new JsonResponse(array('rows' => $res));
  }
}
