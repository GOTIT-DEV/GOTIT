<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\RearrangementsService;
use Bbees\E3sBundle\Services\QueryBuilderService;
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
    $doctrine      = $this->getDoctrine();
    $dates_methode = $doctrine->getRepository(Motu::class)->findAll();
    # Rendu du template
    return $this->render('requetes/rearrangements/index.html.twig', array(
      'dates_methode' => $dates_methode,
      'genus_set'     => $genus_set,
      "with_taxname"  => true,
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