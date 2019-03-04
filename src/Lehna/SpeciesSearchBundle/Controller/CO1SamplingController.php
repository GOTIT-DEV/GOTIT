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

use Bbees\E3sBundle\Entity\ReferentielTaxon;
use Lehna\SpeciesSearchBundle\Services\QueryBuilderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for querying COI sampling coverage
 *
 * @Route("/co1-sampling")
 * @Security("has_role('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class CO1SamplingController extends Controller {

  /**
   * @Route("/", name="co1-sampling")
   * Index : render query form interface
   */
  public function index(QueryBuilderService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # render form template
    return $this->render('@LehnaSpeciesSearch/co1-sampling/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="co1-sampling-query")
   *
   * Returns a JSON response with COI sampling statistics
   */
  public function searchQuery(Request $request, QueryBuilderService $service) {
    # POST parameters 
    $data = $request->request;

    # fetch sampling data
    $all_sta = $service->getSpeciesGeoSummary($data);
    $coi_sta = $service->getSpeciesGeoSummary($data, $coi = true);

    # merge specimen sampling data and COI sampling data 
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

    # return JSON response
    return new JsonResponse(array('rows' => $res));
  }

  /**
   * @Route("/geocoords/", name="co1-geocoords")
   * 
   * Returns a JSON response with sampling geographical coordinates for target species 
   * Used to plot sampling an world map projection in modal pop-up
   */
  public function geoCoords(Request $request, QueryBuilderService $service) {
    # POST parameters 
    $data = $request->request;
    $id   = $data->get('taxon');
    # fetch sampling sites 
    $no_co1   = $service->getSpeciesGeoDetails($id, 0);
    $with_co1 = $service->getSpeciesGeoDetails($id, 1);
    # fetch taxon details
    $taxon = $this->getDoctrine()->getRepository(ReferentielTaxon::class)->find($id);
    # return JSON response
    return new JsonResponse(array(
      'taxname'  => $taxon->getTaxname(),
      'with_co1' => $with_co1,
      'no_co1'   => $no_co1,
    ));
  }
}
