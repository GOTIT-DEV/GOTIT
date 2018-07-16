<?php

namespace Bbees\E3sBundle\Controller\requetes;

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
  public function index() {
    # obtention de la liste des genres
    $service   = $this->get('bbees_e3s.query_builder_e3s');
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
  public function searchQuery(Request $request) {
    # Raccourci requête POST
    $data = $request->request;
    dump($data);

    # Obtention des données géographiques
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $res = $service->getSpeciesAssignment($data);
    dump($res);
    # Renvoi réponse JSON
    return new JsonResponse(array('rows' => $res));
  }
}
