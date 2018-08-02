<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller pour les requêtes sur l'assignation des MOTUs et leur comptage
 *
 * @Route("/requetes/common")
 */
class CommonController extends Controller {

  /**
   * @Route("/methods-in-date", name="methodsindate")
   *
   * Liste les méthodes présentes dans un dataset
   * Utilisé pour remplir les select du formulaire
   */
  public function methodsByDate(Request $request, QueryBuilderService $service) {
    # Dataset sélectionné par l'utilisateur
    $id_dataset = $request->request->get('dataset');
    # Obtention des méthodes incluses dans le dataset
    $methodes = $service->getMethodsByDate($id_dataset);
    # Renvoi de la réponse JSON
    return new JsonResponse(array('data' => $methodes));
  }
}