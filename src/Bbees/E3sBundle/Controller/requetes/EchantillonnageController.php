<?php

namespace Bbees\E3sBundle\Controller\requetes;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Bbees\E3sBundle\Services\QueryBuilderService;

use Bbees\E3sBundle\Entity\Voc;
use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Entity\ReferentielTaxon;

/**
 * @Route("/requetes/echantillonnage")
 */
class EchantillonnageController extends Controller {


    /**
     * @Route("/", name="echantillonnage")
     */
    public function index()
    {
        $service = $this->get('bbees_e3s.query_builder_e3s');

        # obtention de la liste des genres
        $genus_set = $service->getGenusSet();

        return $this->render('requetes/echantillonnage/index.html.twig', array(
            'genus_set' => $genus_set
        ));
    }

    /**
     * @Route("/requete3", name="requete3")
     */
    public function searchQuery(Request $request)
    {
        $data = $request->request;
        $niveau = $data->get('niveau');

        $service = $this->get('bbees_e3s.query_builder_e3s');

        $all_sta = $service->getSpeciesGeoSummary($data);
        $coi_sta = $service->getSpeciesGeoSummary($data, $coi=true);

				foreach($all_sta as $id => $sta){
					$all_sta[$id] = array_merge(array(
						'nb_sta_co1' => 0,
						'lmp_co1' => null,
						'mle_co1' => null
					), $all_sta[$id]);
				}
        foreach($coi_sta as $id => $coi){
            if (array_key_exists($id, $all_sta)){
                $all_sta[$id] = array_merge($all_sta[$id], $coi_sta[$id]);
            }else{
                $all_sta[$id] = array_merge(array(
                    'nb_sta' => 0,
                    'lmp' => null,
                    'mle' => null
                ), $coi_sta[$id]);
            }
        }
        $res = array_values($all_sta);
        //dump($res);
        return new JsonResponse(array('rows' => $res));
    }

    /**
     * @Route("/geocoords/", name="geocoords")
     */
    public function geoCoords(Request $request){
        $data = $request->request;
        $id = $data->get('taxon');
        $niveau = $data->get('niveau');
        $service = $this->get('bbees_e3s.query_builder_e3s');
        $no_co1 = $service->getSpeciesGeoDetails($id, 0);
        $with_co1 = $service->getSpeciesGeoDetails($id, 1);

        $taxon = $this->getDoctrine()->getRepository(ReferentielTaxon::class)->find($id);
        //dump($taxon);
        
        return new JsonResponse(array(
            'taxname' => $taxon->getTaxname(),
            'all' => $service->getSpeciesGeoDetails($id, -1),
            'with_co1' => $with_co1,
            'no_co1' => $no_co1
        ));
    }
}
