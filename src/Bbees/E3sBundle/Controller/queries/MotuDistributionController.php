<?php

/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 * Author : Louis Duchemin <ls.duchemin@gmail.com>
 */

namespace Bbees\E3sBundle\Controller\queries;

use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for querying MOTU distribution 
 *
 * @Route("/queries/distribution")
 * @Security("has_role('ROLE_INVITED')")
 */
class MotuDistributionController extends Controller {

  /**
   * @Route("/", name="distribution")
   *
   * Index : render query form template
   */
  public function index(QueryBuilderService $service) {
    $doctrine = $this->getDoctrine();
    # fetch datasets
    $datasets = $doctrine->getRepository(Motu::class)->findAll();
    # fetch genus and method sets 
    $genus_set    = $service->getGenusSet();
    $methods_list = $service->listMethodsByDate();
    # render form template
    return $this->render('queries/motu-distribution/index.html.twig', array(
      'genus_set'    => $genus_set,
      'datasets'     => $datasets,
      'methods_list' => $methods_list,
    ));
  }

  /**
   * @Route("/query", name="distribution-query")
   *
   * returns a JSON response with 
   * - query : the initial query parameters 
   * - rows : geographical + motu data for each sequence
   * - methode : MOTU method details
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # POST parameters 
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
