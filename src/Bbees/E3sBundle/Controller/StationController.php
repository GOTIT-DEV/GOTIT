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
 */

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Bbees\E3sBundle\Services\GenericFunctionService;

/**
 * Station controller.
 *
 * @Route("station")
 * @Security("has_role('ROLE_INVITED')")
 */
class StationController extends Controller
{
    /**
     * Lists all station entities.
     *
     * @Route("/", name="station_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $stations = $em->getRepository('BbeesE3sBundle:Station')->findAll();

        return $this->render('station/index.html.twig', array(
            'stations' => $stations,
        ));
    }

      /**
     * Retourne au format json un ensemble de champs à afficher tab_station_toshow avec les critères suivant :  
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="station_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {   
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('station.dateMaj' => 'desc', 'station.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount;      
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Station")->createQueryBuilder('station')
            ->where('LOWER(station.codeStation) LIKE :criteriaLower')
            ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')).'%')
            ->leftJoin('BbeesE3sBundle:Pays', 'pays', 'WITH', 'station.paysFk = pays.id')
            ->leftJoin('BbeesE3sBundle:Commune', 'commune', 'WITH', 'station.communeFk = commune.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb_entities = count($entities_toshow);
        $entities_toshow = array_slice($entities_toshow, $minRecord, $rowCount); 
        
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $query = $em->createQuery('SELECT collecte.id FROM BbeesE3sBundle:Collecte collecte WHERE collecte.stationFk = '.$id.'')->getResult();
            $stationFk = (count($query) > 0) ? $id : '';
            $tab_toshow[] = array( "id" => $id, "station.id" => $id, "station.codeStation" => $entity->getCodeStation(),
             "station.nomStation" => $entity->getNomStation(),
             "commune.codeCommune" => $entity->getCommuneFk()->getCodeCommune(),
             "pays.codePays" => $entity->getPaysFk()->getCodePays(),
             "station.latDegDec" => $entity->getLatDegDec(), "station.longDegDec" => $entity->getLongDegDec(),
             "station.dateCre" => $DateCre, "station.dateMaj" => $DateMaj, 
             "userCreId" => $service->GetUserCreId($entity), "station.userCre" => $service->GetUserCreUsername($entity) ,"station.userMaj" => $service->GetUserMajUsername($entity),
             "linkCollecte" => $stationFk);
        }                
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "total"    => $nb_entities // total data array				
            ) ) );
        // If it is an Ajax request: returns the content in json format
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }

    /**
     * @Route("/geocoordstations/", name="geocoordstations")
     * @Method("POST")
     */
    public function geoCoords(Request $request){
        $data = $request->request;
        $latitude = $data->get('latitude');
        $latitude = floatval(str_replace(",", ".", $latitude));
        $longitude = $data->get('longitude');
        $longitude = floatval(str_replace(",", ".", $longitude));
        $diffLatitudeLongitude = 1;
        //
        $em = $this->getDoctrine()->getManager();
              
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Station")->createQueryBuilder('station')
            ->where('station.longDegDec < :longMax')
            ->setParameter('longMax', $longitude+$diffLatitudeLongitude)
            ->andWhere('station.longDegDec > :longMin')
            ->setParameter('longMin', $longitude-$diffLatitudeLongitude)
            ->andWhere('station.latDegDec < :latMax')
            ->setParameter('latMax', $latitude+$diffLatitudeLongitude)            
            ->andWhere('station.latDegDec > :latMin')
            ->setParameter('latMin', $latitude-$diffLatitudeLongitude)
            ->getQuery()
            ->getResult();
        $nb_entities = count($entities_toshow);
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            $tab_toshow[] = array("id" => $id, "station.id" => $id, 
            "station.codeStation" => $entity->getCodeStation(),
             "station.nomStation" => $entity->getNomStation(),
             "commune.codeCommune" => $entity->getCommuneFk()->getCodeCommune(),
             "pays.codePays" => $entity->getPaysFk()->getCodePays(),
             "station.latDegDec" => $entity->getLatDegDec(), 
             "station.longDegDec" => $entity->getLongDegDec()
             );
        } 
        
        return new JsonResponse(array(
            'stations' => $tab_toshow,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ));
    }
    
    /**
     * Creates a new station entity.
     *
     * @Route("/new", name="station_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $station = new Station();
        $form = $this->createForm('Bbees\E3sBundle\Form\StationType', $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($station);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('station/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('station_edit', array('id' => $station->getId(), 'valid' => 1));           
        }

        return $this->render('station/edit.html.twig', array(
            'station' => $station,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a station entity.
     *
     * @Route("/{id}", name="station_show")
     * @Method("GET")
     */
    public function showAction(Station $station)
    {
        $deleteForm = $this->createDeleteForm($station);
        
        $editForm = $this->createForm('Bbees\E3sBundle\Form\StationType', $station);
        return $this->render('show.html.twig', array(
            'station' => $station,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing station entity.
     *
     * @Route("/{id}/edit", name="station_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Station $station)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $station->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        $deleteForm = $this->createDeleteForm($station);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\StationType', $station);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {            
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('station/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('station/edit.html.twig', array(
            'station' => $station,
            'edit_form' => $editForm->createView(),
            'valid' => 1,
            ));
        }

        return $this->render('station/edit.html.twig', array(
            'station' => $station,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a station entity.
     *
     * @Route("/{id}", name="station_delete")
     * @Method({"DELETE","POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Station $station)
    {
        $form = $this->createDeleteForm($station);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($station);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('station/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
        }
        return $this->redirectToRoute('station_index');
    }

    /**
     * Creates a form to delete a station entity.
     *
     * @param Station $station The station entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Station $station)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('station_delete', array('id' => $station->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
