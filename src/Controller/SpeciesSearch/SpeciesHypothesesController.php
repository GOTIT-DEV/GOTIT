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

use App\Entity\Motu;
use App\Services\SpeciesSearch\SpeciesHypothesesService;
use App\Services\SpeciesSearch\SpeciesQueryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;

/**
 * Controller for querying motu species hypotheses
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
#[Route("/species-hypotheses")]
class SpeciesHypothesesController extends EntityController {

  /**
   * Index : render query form template
   */
  #[Route("/", name: "species-hypotheses", methods: ["GET"])]
  public function indexAction(SpeciesQueryService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # fetch MOTU datasets
    $datasets = $this->getRepository(Motu::class)->findAll();
    # render form template
    return $this->render('SpeciesSearch/species-hypotheses/index.html.twig', array(
      'datasets' => $datasets,
      'genus_set' => $genus_set,
    ));
  }

  /**
   * Returns a JSON reponse with species hypotheses data
   */
  #[Route("/query", name: "species-hypotheses-query", methods: ["POST"])]
  public function searchQuery(Request $request, SpeciesHypothesesService $service) {
    $result = $service->processQuery($request->request);
    return new JsonResponse($result);
  }
}
