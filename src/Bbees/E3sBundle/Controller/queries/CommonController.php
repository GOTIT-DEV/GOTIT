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
 * Controller for common querying routines
 * @Route("/queries/common")
 * @Security("has_role('ROLE_INVITED')")
 */
class CommonController extends Controller {

  /**
   * @Route("/methods-in-date", name="methodsindate")
   *
   * Returns the set of methods in a target dataset as JSON
   */
  public function methodsByDate(Request $request, QueryBuilderService $service) {
    # target dataset 
    $id_dataset = $request->request->get('dataset');
    # fetch methods 
    $methodes = $service->getMethodsByDate($id_dataset);
    # return JSON response
    return new JsonResponse(array('data' => $methodes));
  }
}