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

namespace Lehna\SpeciesSearchBundle\Controller;

use Bbees\E3sBundle\Entity\Motu;
use Lehna\SpeciesSearchBundle\Services\QueryBuilderService;
use Lehna\SpeciesSearchBundle\Services\RearrangementsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for querying motu species hypotheses 
 * 
 * @Route("/species-hypotheses")
 * @Security("has_role('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class SpeciesHypothesesController extends Controller {
  /**
   * @Route("/", name="species-hypotheses")
   *
   * Index : render query form template 
   */
  public function indexAction(QueryBuilderService $service) {
    # fetch genus set 
    $genus_set = $service->getGenusSet();
    # fetch MOTU datasets
    $doctrine = $this->getDoctrine();
    $datasets = $doctrine->getRepository(Motu::class)->findAll();
    # render form template
    return $this->render('@LehnaSpeciesSearch/species-hypotheses/index.html.twig', array(
      'datasets'  => $datasets,
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="species-hypotheses-query")
   *
   * Returns a JSON reponse with species hypotheses data
   */
  public function searchQuery(Request $request, RearrangementsService $service) {
    $result = $service->processQuery($request->request);
    return new JsonResponse($result);
  }
}