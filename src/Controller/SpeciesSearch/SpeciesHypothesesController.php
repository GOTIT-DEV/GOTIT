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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for querying motu species hypotheses
 *
 * @Route("/species-hypotheses")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class SpeciesHypothesesController extends AbstractController {
    
    /**
     * date of update  : 28/06/2022 
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
       }
       
  /**
   * @Route("/", name="species-hypotheses", methods={"GET"})
   *
   * Index : render query form template
   */
  public function indexAction(SpeciesQueryService $service) {
    # fetch genus set
    $genus_set = $service->getGenusSet();
    # fetch MOTU datasets
    $doctrine = $this->doctrine;
    $datasets = $doctrine->getRepository(Motu::class)->findAll();
    # render form template
    return $this->render('SpeciesSearch/species-hypotheses/index.html.twig', array(
      'datasets' => $datasets,
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="species-hypotheses-query", methods={"POST"})
   *
   * Returns a JSON reponse with species hypotheses data
   */
  public function searchQuery(Request $request, SpeciesHypothesesService $service) {
    $result = $service->processQuery($request->request);
    return new JsonResponse($result);
  }
}