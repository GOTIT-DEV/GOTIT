<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Entity\Voc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/requetes/assign-motu")
 */
class AssignationMotuController extends Controller {

  /**
   * @Route("/", name="assign-motu")
   * 
   * Rendu du template de la page
   */
  public function index() {
    // Get services
    $service  = $this->get('bbees_e3s.query_builder_e3s');
    $doctrine = $this->getDoctrine();

    # obtention de la liste des genres
    $genus_set = $service->getGenusSet();
    # obtention de la liste des critères d'identification
    $criteres = $doctrine->getRepository(Voc::class)->findByParent('critereIdentification');
    # liste des dates des méthodes de délimitation
    $dates_methode = $doctrine->getRepository(Motu::class)->findAll();

    # Renvoyer le template et les paramètres
    return $this->render('requetes/assign-motu/index.html.twig', array(
      'genus_set'     => $genus_set,
      'criteres'      => $criteres,
      'dates_methode' => $dates_methode,
    ));
  }

  /**
   * @Route("/methods-in-date", name="methodsindate")
   * 
   * Liste les méthodes présentes dans un dataset
   * Utilisé pour remplir les select du formulaire
   */
  public function methodsByDate(Request $request) {
    # Dataset sélectionné par l'utilisateur
    $id_date_motu = $request->request->get('date_methode');
    # Service
    $service = $this->get('bbees_e3s.query_builder_e3s');
    # Obtention des méthodes incluses dans le dataset
    $methodes = $service->getMethodsByDate($id_date_motu);
    # Renvoi de la réponse JSON
    return new JsonResponse(array('data' => $methodes));
  }

  /**
   * @Route("/requete", name="requete1")
   * 
   * Renvoie un objet JSON utilisé pour remplir la table de résultats (rows),
   * et des informations supplémentaires indiquant les paramètres de la 
   * requêtes initiale (methodes, niveau, criteres)
   */
  public function searchQuery(Request $request) {
    # Raccourci requete POST
    $data = $request->request;
    # Obtention des paramètres
    $niveau   = $data->get('niveau');
    $methodes = $data->get('methodes');
    $criteres = $data->get('criteres');
    # Service
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $res     = $service->getMotuCountList($data);
    # Renvoi de la réponse JSON
    return new JsonResponse(array(
      'rows'     => $res,
      'methodes' => $methodes,
      'niveau'   => $niveau,
      'criteres' => $criteres,
    ));
  }

  /**
   * @Route("/detailsModal", name="detailsModal")
   *
   * Controle le rendu de la pop-in modale affichable depuis
   * la colonne Détails de la table de résultats
   */
  public function detailsModal(Request $request) {
    # Raccourci requete POST
    $data = $request->request;
    # Obtention de la liste
    $service = $this->get('bbees_e3s.query_builder_e3s');
    $res     = $service->getMotuSeqList($data);
    # Processing template de la modal
    return $this->render('requetes/assign-motu/details.modal.html.twig', array(
      'details' => $res,
    ));
  }
}
