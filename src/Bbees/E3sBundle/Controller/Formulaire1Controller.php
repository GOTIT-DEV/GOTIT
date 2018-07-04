<?php

namespace Bbees\E3sBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Bbees\E3sBundle\Services\QueryBuilderService;

use Bbees\E3sBundle\Entity\Voc;
use Bbees\E3sBundle\Entity\Motu;

/**
 * @Route("formulaire1")
 */
class Formulaire1Controller extends Controller {

    /**
     * @Route("/", name="formulaire1")
     */
    public function index()
    {
        $service = $this->get('bbees_e3s.query_builder_e3s');
        $doctrine = $this->getDoctrine();

        # obtention de la liste des genres
        $genus_set = $service->getGenusSet();
        # obtention de la liste des critères d'identification
        $criteres = $doctrine->getRepository(Voc::class)->findByParent('critereIdentification');
        # liste des dates des méthodes de délimitation
        $dates_methode = $doctrine->getRepository(Motu::class)->findAll();

        return $this->render('requetes/formulaire1/index.html.twig', array(
            'genus_set' => $genus_set,
            'criteres' => $criteres,
            'dates_methode' => $dates_methode
        ));
    }



    /**
     * @Route("/methods-in-date", name="methodsindate")
     */
    public function methodsByDate(Request $request){
        $id_date_motu = $request->request->get('date_methode');
        $service = $this->get('bbees_e3s.query_builder_e3s');
        $methodes = $service->getMethodsByDate($id_date_motu);

        return new JsonResponse(array('data' => $methodes));
    }

    /**
     * @Route("/requete", name="requete1")
     */
    public function searchQuery(Request $request)
    {
        $data = $request->request;
        //dump($data);

        $niveau = $data->get('niveau');
        $methodes = $data->get('methodes');
        $criteres = $data->get('criteres');

        $service = $this->get('bbees_e3s.query_builder_e3s');
        $res = $service->getMotuCountList($data);

        //dump($res);
        return new JsonResponse(array(
            'rows' => $res,
            'methodes' => $methodes,
            'niveau' => $niveau,
            'criteres' => $criteres
        ));
    }

    /**
     * @Route("/detailsModal", name="detailsModal")
     */
    public function detailsModal(Request $request)
    {
        $data = $request->request;
        //dump($data);

        $service =  $this->get('bbees_e3s.query_builder_e3s');
        $res = $service->getMotuSeqList($data);

        //dump($res);

        return $this->render('requetes/formulaire1/details.modal.html.twig', array(
            'details' => $res
        ));
    }
}
