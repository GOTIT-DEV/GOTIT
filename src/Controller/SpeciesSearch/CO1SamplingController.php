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

use App\Entity\ReferentielTaxon;
use App\Services\SpeciesSearch\SpeciesQueryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Controller for querying COI sampling coverage
 *
 * @Route("/co1-sampling")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Louis Duchemin <ls.duchemin@gmail.com>
 */
class CO1SamplingController extends AbstractController {

  /**
   * @Route("/", name="co1-sampling", methods={"GET"})
   * Index : render query form interface
   */
  public function index(SpeciesQueryService $service) {

    # fetch genus set
    $genus_set = $service->getGenusSet();
    # render form template
    return $this->render('SpeciesSearch/co1-sampling/index.html.twig', array(
      'genus_set' => $genus_set,
    ));
  }

  /**
   * @Route("/query", name="co1-sampling-query", methods={"POST"})
   *
   * Returns a JSON response with COI sampling statistics
   */
  public function samplingCoverageSummary(Request $request, SpeciesQueryService $service) {
    # POST parameters
    $data = $request->request;

    # fetch sampling data
    $biomat_coverage = $service->getSpeciesGeoSummary($data, $coi = false);
    $co1_coverage = $service->getSpeciesGeoSummary($data, $coi = true);

    # merge specimen sampling data and COI sampling data
    foreach ($biomat_coverage as $id => $species_data) {
      $biomat_coverage[$id] = array_merge(
        ['nb_sta_co1' => 0, 'lmp_co1' => null, 'mle_co1' => null],
        $species_data
      );
    }
    foreach ($co1_coverage as $id => $coi) {
      if (array_key_exists($id, $biomat_coverage)) {
        $biomat_coverage[$id] = array_merge($biomat_coverage[$id], $co1_coverage[$id]);
      } else {
        $biomat_coverage[$id] = array_merge(
          ['nb_sta' => 0, 'lmp' => null, 'mle' => null],
          $co1_coverage[$id]
        );
      }
    }

    $res = json_encode(array_values($biomat_coverage), JSON_NUMERIC_CHECK);
    # return JSON response
    return JsonResponse::fromJsonString($res);
  }

  /**
   * @Route("/species/{id}", name="co1-species-sampling", methods={"GET"})
   *
   * Returns a JSON response with sampling geographical coordinates for target species
   * Used to plot sampling on a world map projection
   */
  public function speciesSamplingCoverage(ReferentielTaxon $taxon,
    SpeciesQueryService $service, SerializerInterface $serializer) {

    # fetch sampling sites
    $sites = $service->getSpeciesSamplingDetails($taxon->getId());

    return new JsonResponse([
      "taxon" => $serializer->normalize($taxon, null),
      "sites" => $sites,
    ]);
  }
}
