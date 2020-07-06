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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Controller for common querying routines
 * @Route("/common")
 * @Security("has_role('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class CommonController extends Controller {

  /**
   * @Route("/methods-in-date", name="methodsindate", methods={"POST"})
   *
   * Returns the set of methods in a target dataset as JSON
   */
  public function methodsByDate(Request $request, SpeciesQueryService $service) {
    $data = json_decode($request->getContent());
    # target dataset 
    $id_dataset = $data->dataset;
    # fetch methods 
    $methodes = $service->getMethodsByDate($id_dataset);
    # return JSON response
    return new JsonResponse($methodes);
  }
}