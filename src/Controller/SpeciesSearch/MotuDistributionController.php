<?php

/*
 * This file is part of the SpeciesSearchBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * SpeciesSearchBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * SpeciesSearchBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with SpeciesSearchBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace App\Controller\SpeciesSearch;

use App\Entity\MotuDataset;
use App\Services\SpeciesSearch\SpeciesQueryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for querying MOTU distribution
 *
 * @Route("/distribution")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class MotuDistributionController extends AbstractController {

  /**
   * @Route("/", name="distribution", methods={"GET"})
   *
   * Index : render query form template
   */
  public function index(SpeciesQueryService $service) {
    $doctrine = $this->getDoctrine();
    # fetch datasets
    $datasets = $doctrine->getRepository(MotuDataset::class)->findAll();
    # fetch genus and method sets
    $genus_set = $service->getGenusSet();
    $methods_list = $service->listMethodsByDate();
    # render form template
    return $this->render('SpeciesSearch/motu-distribution/index.html.twig', array(
      'genus_set' => $genus_set,
      'datasets' => $datasets,
      'methods_list' => $methods_list,
    ));
  }

  /**
   * @Route("/query", name="distribution-query", methods={"POST"})
   *
   * returns a JSON response with
   * - query : the initial query parameters
   * - rows : geographical + motu data for each sequence
   * - methode : MOTU method details
   */
  public function searchQuery(Request $request, SpeciesQueryService $service) {
    # POST parameters
    $data = $request->request;
    # Obtention de la localisation géographique
    $res = $service->getMotuGeoLocation($data);
    $methode = $service->getMethod($data->get('methode'), $data->get('dataset'));
    # Renvoi réponse JSON
    return new JsonResponse([
      'query' => $data->all(),
      'rows' => $res,
      'methode' => $methode,
    ]);
  }
}
