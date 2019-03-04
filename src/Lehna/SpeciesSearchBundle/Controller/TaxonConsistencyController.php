<?php

/*
 * This file is part of the SpeciesSearchBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 *
 */

namespace Lehna\SpeciesSearchBundle\Controller;

use Lehna\SpeciesSearchBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for querying species assignment consistency 
 * among sequences, individuals and biological materials
 *
 * @Route("/consistency")
 * @Security("has_role('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class TaxonConsistencyController extends Controller {

  /**
   * @Route("/", name="consistency")
   *
   * Index : render query form template
   */
  public function index(QueryBuilderService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # render form template
    return $this->render('@LehnaSpeciesSearch/taxon-consistency/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="consistency-query")
   *
   * Returns a JSON response with species assignment at each identification level
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # POST parameters 
    $data = $request->request;
    # fetch species assignments
    $res = $service->getSpeciesAssignment($data);
    # return JSON reponse
    return new JsonResponse(array('rows' => $res));
  }
}
