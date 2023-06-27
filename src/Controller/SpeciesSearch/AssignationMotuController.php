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
use App\Entity\Voc;
use App\Services\SpeciesSearch\SpeciesQueryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;

/**
 * Controller for querying MOTU assignments
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
#[Route("/assign-motu")]
class AssignationMotuController extends EntityController {

  /**
   * Index page : Query form interface
   */
  #[Route("/", name: "assign-motu", methods: ["GET"])]
  public function index(SpeciesQueryService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # fetch species identification criterions
    $criteres = $this->getRepository(Voc::class)->findByParent('critereIdentification');
    # fetch motu datasets
    $datasets = $this->getRepository(Motu::class)->findAll();

    # render form
    return $this->render('SpeciesSearch/assign-motu/index.html.twig', array(
      'genus_set' => $genus_set,
      'criteres' => $criteres,
      'datasets' => $datasets,
    ));
  }

  /**
   * Returns a JSON response with
   *  - rows : an array of motu count for each method in target dataset
   *  - query : an array of original query parameters
   * (methods, identification level and criterions). Used to query details
   * for a molecular method
   */
  #[Route("/query", name: "motu-query", methods: ["POST"])]
  public function searchQuery(Request $request, SpeciesQueryService $service) {
    $data = $request->request;
    $res = $service->getMotuCountList($data);
    return new JsonResponse(array(
      'rows' => $res,
      'query' => $data->all(),
    ));
  }

  /**
   * Returns a JSON response with
   * - rows : array of sequence MOTU assignments
   * Shown in modal pop-up : details for one molecular method
   */
  #[Route("/detailsModal", name: "motu-modal", methods: ["POST"])]
  public function detailsModal(Request $request, SpeciesQueryService $service) {
    $data = $request->request;
    $res = $service->getMotuSeqList($data);
    return new JsonResponse($res);
  }
}
