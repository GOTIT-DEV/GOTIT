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

use App\Services\SpeciesSearch\SpeciesQueryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for querying species assignment consistency
 * among sequences, individuals and biological materials
 *
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
#[Route("/consistency")]
class TaxonConsistencyController extends AbstractController {

  /**
   *
   * Index : render query form template
   */
  #[Route("/", name: "consistency", methods: ["GET"])]
  public function index(SpeciesQueryService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # render form template
    return $this->render('SpeciesSearch/taxon-consistency/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * Returns a JSON response with species assignment at each identification level
   */
  #[Route("/query", name: "consistency-query", methods: ["POST"])]
  public function searchQuery(Request $request, SpeciesQueryService $service) {
    $data = json_decode($request->getContent(), true);
    $res = $service->getSpeciesAssignment($data);
    return new JsonResponse($res);
  }
}
