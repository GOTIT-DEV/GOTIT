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
 * @Route("/requetes/richesse")
 */
class RichesseController extends Controller {


    /**
     * @Route("/", name="richesse")
     */
    public function index()
    {
        $doctrine = $this->getDoctrine();
        $service = $this->get('bbees_e3s.query_builder_e3s');

        $dates_methode = $doctrine->getRepository(Motu::class)->findAll();
        # obtention de la liste des genres
        $genus_set = $service->getGenusSet();
        $methods_list = $service->listMethodsByDate();

        return $this->render('requetes/richesse/index.html.twig', array(
            'genus_set' => $genus_set,
            'dates_methode' => $dates_methode,
            'methods_list' => $methods_list,
            "with_taxname" => true
        ));
    }

    /**
     * @Route("/requete4", name="requete4")
     */
    public function searchQuery(Request $request)
    {
        $data = $request->request;

        $service = $this->get('bbees_e3s.query_builder_e3s');

        $res = $service->getMotuGeoLocation($data);
        $geo_res = [];
        $methode = [];
        if($data->get('methode')){
            $geo_res = $service->getMotuGeoLocation($data, true);
            $methode = $service->getMethod($data->get('methode'), $data->get('date_methode'));
        }
        //dump($res);
        return new JsonResponse(array(
            'rows' => $res,
            'geo' => $geo_res,
            'methode' => $methode
        ));
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
