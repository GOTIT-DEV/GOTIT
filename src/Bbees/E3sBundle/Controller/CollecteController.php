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

use Bbees\E3sBundle\Entity\Collecte;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Collecte controller.
 *
 * @Route("collecte")
 * @Security("has_role('ROLE_INVITED')")
 */
class CollecteController extends Controller
{
    /**
     * Lists all collecte entities.
     *
     * @Route("/", name="collecte_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {

        //var_dump($request->query->get('searchPatern'));         
        
        $em = $this->getDoctrine()->getManager();
        $collectes = $em->getRepository('BbeesE3sBundle:Collecte')->findAll();
       
        return $this->render('collecte/index.html.twig', array( 
            'collectes' => $collectes,));                

     }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="collecte_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('collecte.dateMaj' => 'desc', 'collecte.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
               
        // initializes the searchPhrase variable
        $where = 'LOWER(collecte.codeCollecte) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND collecte.stationFk  = '.$request->get('idFk');
        }
        // Search the list to show
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Collecte")->createQueryBuilder('collecte')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Station', 'station', 'WITH', 'collecte.stationFk = station.id')
            ->leftJoin('BbeesE3sBundle:Pays', 'pays', 'WITH', 'station.paysFk = pays.id')
            ->leftJoin('BbeesE3sBundle:Commune', 'commune', 'WITH', 'station.communeFk = commune.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'voc', 'WITH', 'collecte.legVocFk = voc.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb_entities  = count($entities_toshow);
        $entities_toshow = array_slice($entities_toshow, $minRecord, $rowCount);       
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            $DateCollecte = ($entity->getDateCollecte() !== null) ?  $entity->getDateCollecte()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // search for material associated with a sampling
            $query = $em->createQuery('SELECT lot.id FROM BbeesE3sBundle:LotMateriel lot WHERE lot.collecteFk = '.$id.'')->getResult();
            $linkLotmaterielFk = (count($query) > 0) ? $id : '';
            // search for external material associated with a sampling 
            $query = $em->createQuery('SELECT lotext.id FROM BbeesE3sBundle:LotMaterielExt lotext WHERE lotext.collecteFk = '.$id.'')->getResult();
            $linkLotmaterielextFk = (count($query) > 0) ? $id : '';
             // search for external sequence associated with a sampling 
            $query = $em->createQuery('SELECT sqcext.id FROM BbeesE3sBundle:SequenceAssembleeExt sqcext WHERE sqcext.collecteFk = '.$id.'')->getResult();
            $linkSequenceassembleeextFk = (count($query) > 0) ? $id : '';
            // Search for the concatenated list of targeted taxa
            $query = $em->createQuery('SELECT rt.taxname as taxname FROM BbeesE3sBundle:ACibler ac JOIN ac.referentielTaxonFk rt WHERE ac.collecteFk = '.$id.'')->getResult();            
            $arrayTaxonsCibler = array();
            foreach($query as $taxon) {
                 $arrayTaxonsCibler[] = $taxon['taxname'];
            }
            $listeTaxonsCibler = implode(", ", $arrayTaxonsCibler);
            //
            $tab_toshow[] = array("id" => $id, "collecte.id" => $id,"collecte.codeCollecte" => $entity->getCodeCollecte(),
             "station.codeStation" => $entity->getStationFk()->getCodeStation(),
             "pays.nomPays" => $entity->getStationFk()->getPaysFk()->getNomPays(),
             "commune.codeCommune" => $entity->getStationFk()->getCommuneFk()->getCodeCommune(),
             "collecte.legVocFk" => $entity->getLegVocFk()->getCode(),
             "collecte.dateCollecte" => $DateCollecte,  "collecte.aFaire" => $entity->getAfaire(),
             "collecte.dateCre" => $DateCre, "collecte.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "collecte.userCre" => $service->GetUserCreUsername($entity) ,"collecte.userMaj" => $service->GetUserMajUsername($entity),
             "linkLotmateriel" => $linkLotmaterielFk,  "linkLotmaterielext" => $linkLotmaterielextFk, "linkSequenceassembleeext" => $linkSequenceassembleeextFk, 
             "listeTaxonsCibler" => $listeTaxonsCibler );
        }     
        // Ajax answer
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "searchPhrase" => $searchPhrase,
            "total"    => $nb_entities  // total data array				
            ) ) );
        // If it is an Ajax request: returns the content in json format
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }
    
    /**
     * Creates a new collecte entity.
     *
     * @Route("/new", name="collecte_new")
     * @Method({"GET", "POST"}) 
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $collecte = new Collecte();
        $form = $this->createForm('Bbees\E3sBundle\Form\CollecteType', $collecte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($collecte);          
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('collecte/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('collecte_edit', array('id' => $collecte->getId(), 'valid' => 1));  
        }

        return $this->render('collecte/edit.html.twig', array(
            'collecte' => $collecte,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a collecte entity.
     *
     * @Route("/{id}", name="collecte_show")
     * @Method("GET")
     */
    public function showAction(Collecte $collecte)
    {
        $deleteForm = $this->createDeleteForm($collecte);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\CollecteType', $collecte);

        return $this->render('show.html.twig', array(
            'collecte' => $collecte,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing collecte entity.
     *
     * @Route("/{id}/edit", name="collecte_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Collecte $collecte)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $collecte->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        // load service  generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');

        // store ArrayCollectionEstFinancePar       
        $originalAPourSamplingMethods = $service->setArrayCollection('APourSamplingMethods',$collecte);
        $originalAPourFixateurs = $service->setArrayCollection('APourFixateurs',$collecte);
        $originalEstFinancePars = $service->setArrayCollection('EstFinancePars',$collecte);
        $originalEstEffectuePars  = $service->setArrayCollection('EstEffectuePars',$collecte);
        $originalACiblers  = $service->setArrayCollection('ACiblers',$collecte);

        // editAction
        $deleteForm = $this->createDeleteForm($collecte);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\CollecteType', $collecte);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollection('APourSamplingMethods',$collecte, $originalAPourSamplingMethods);
            $service->DelArrayCollection('APourFixateurs',$collecte, $originalAPourFixateurs);
            $service->DelArrayCollection('EstFinancePars',$collecte, $originalEstFinancePars);
            $service->DelArrayCollection('EstEffectuePars',$collecte, $originalEstEffectuePars);
            $service->DelArrayCollection('ACiblers',$collecte, $originalACiblers);
            // flush
            $this->getDoctrine()->getManager()->persist($collecte);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('collecte/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('collecte/edit.html.twig', array(
                'collecte' => $collecte,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('collecte/edit.html.twig', array(
            'collecte' => $collecte,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a collecte entity.
     *
     * @Route("/{id}", name="collecte_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Collecte $collecte)
    {
        $form = $this->createDeleteForm($collecte);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($collecte);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('collecte/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('collecte_index');
    }

    /**
     * Creates a form to delete a collecte entity.
     *
     * @param Collecte $collecte The collecte entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Collecte $collecte)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('collecte_delete', array('id' => $collecte->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
