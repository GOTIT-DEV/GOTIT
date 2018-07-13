<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\ReferentielTaxon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller pour les requêtes sur la couverture d'échantillonnage 
 * par espèce, sur le gène COI
 * 
 * @Route("/requetes/echantillonnage")
 */
class EchantillonnageController extends Controller {

  /**
   * @Route("/", name="echantillonnage")
   * 
   * Rendu du template de la page principale
   */
  public function index() {
    # obtention de la liste des genres
    $service   = $this->get('bbees_e3s.query_builder_e3s');
    $genus_set = $service->getGenusSet();
    # Rendu du template
    return $this->render('requetes/echantillonnage/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/requete3", name="requete3")
   * 
   * Données JSON pour remplir la table de résultats
   */
  public function searchQuery(Request $request) {
    # Raccourci requête POST
    $data   = $request->request;
    $niveau = $data->get('niveau');

    # Obtention des données géographiques
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $all_sta = $service->getSpeciesGeoSummary($data);
    $coi_sta = $service->getSpeciesGeoSummary($data, $coi = true);

    # Fusion des données géographiques et des données COI
    foreach ($all_sta as $id => $sta) {
      $all_sta[$id] = array_merge(array(
        'nb_sta_co1' => 0,
        'lmp_co1'    => null,
        'mle_co1'    => null,
      ), $all_sta[$id]);
    }
    foreach ($coi_sta as $id => $coi) {
      if (array_key_exists($id, $all_sta)) {
        $all_sta[$id] = array_merge($all_sta[$id], $coi_sta[$id]);
      } else {
        $all_sta[$id] = array_merge(array(
          'nb_sta' => 0,
          'lmp'    => null,
          'mle'    => null,
        ), $coi_sta[$id]);
      }
    }
    $res = array_values($all_sta);
    # Renvoi réponse JSON
    return new JsonResponse(array('rows' => $res));
  }

  /**
   * @Route("/geocoords/", name="geocoords")
   */
  public function geoCoords(Request $request) {
    # Données POST
    $data     = $request->request;
    $id       = $data->get('taxon');
    $niveau   = $data->get('niveau');
    # Obtention des données de localisation
    $service  = $this->get('bbees_e3s.query_builder_e3s');
    $no_co1   = $service->getSpeciesGeoDetails($id, 0);
    $with_co1 = $service->getSpeciesGeoDetails($id, 1);
    # Obtention de l'objet taxon correspondant à la requête
    $taxon = $this->getDoctrine()->getRepository(ReferentielTaxon::class)->find($id);
    # Renvoi des données de localisation pour affichage sur la carte
    return new JsonResponse(array(
      'taxname'  => $taxon->getTaxname(),
      'all'      => $service->getSpeciesGeoDetails($id, -1),
      'with_co1' => $with_co1,
      'no_co1'   => $no_co1,
    ));
  }
}
