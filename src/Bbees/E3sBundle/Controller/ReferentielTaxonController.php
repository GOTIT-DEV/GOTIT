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

use Bbees\E3sBundle\Entity\ReferentielTaxon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Referentieltaxon controller.
 *
 * @Route("referentieltaxon")
 * @Security("has_role('ROLE_INVITED')")
 */
class ReferentielTaxonController extends Controller
{
    /**
     * Lists all referentielTaxon entities.
     *
     * @Route("/", name="referentieltaxon_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $referentielTaxons = $em->getRepository('BbeesE3sBundle:ReferentielTaxon')->findAll();

        return $this->render('referentieltaxon/index.html.twig', array(
            'referentielTaxons' => $referentielTaxons,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="referentieltaxon_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');            
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('referentielTaxon.dateMaj' => 'desc', 'referentielTaxon.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(referentielTaxon.taxname) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Search for the list to show
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:ReferentielTaxon")->createQueryBuilder('referentielTaxon')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            //
            $tab_toshow[] = array("id" => $id, "referentielTaxon.id" => $id, 
             "referentielTaxon.taxname" => $entity->getTaxname(),
             "referentielTaxon.rank" => $entity->getRank(),
             "referentielTaxon.family" => $entity->getFamily(),
             "referentielTaxon.validity" => $entity->getValidity(),
             "referentielTaxon.codeTaxon" => $entity->getCodeTaxon(),
             "referentielTaxon.clade" => $entity->getClade(),
             "referentielTaxon.dateCre" => $DateCre, "referentielTaxon.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "referentielTaxon.userCre" => $service->GetUserCreUsername($entity) ,"referentielTaxon.userMaj" => $service->GetUserMajUsername($entity),
            );
        }     
        // Ajax answer
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "searchPhrase" => $searchPhrase,
            "total"    => $nb // total data array				
            ) ) );
        // If it is an Ajax request: returns the content in json format
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }

    
    /**
     * Creates a new referentielTaxon entity.
     *
     * @Route("/new", name="referentieltaxon_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $referentielTaxon = new Referentieltaxon();
        $form = $this->createForm('Bbees\E3sBundle\Form\ReferentielTaxonType', $referentielTaxon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($referentielTaxon);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('referentieltaxon/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('referentieltaxon_edit', array('id' => $referentielTaxon->getId(), 'valid' => 1));                       
        }
       

        return $this->render('referentieltaxon/edit.html.twig', array(
            'referentielTaxon' => $referentielTaxon,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a referentielTaxon entity.
     *
     * @Route("/{id}", name="referentieltaxon_show")
     * @Method("GET")
     */
    public function showAction(ReferentielTaxon $referentielTaxon)
    {
        $deleteForm = $this->createDeleteForm($referentielTaxon);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ReferentielTaxonType', $referentielTaxon);

        return $this->render('show.html.twig', array(
            'referentielTaxon' => $referentielTaxon,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing referentielTaxon entity.
     *
     * @Route("/{id}/edit", name="referentieltaxon_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, ReferentielTaxon $referentielTaxon)
    {
        $deleteForm = $this->createDeleteForm($referentielTaxon);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ReferentielTaxonType', $referentielTaxon);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('referentieltaxon/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('referentieltaxon/edit.html.twig', array(
                'referentielTaxon' => $referentielTaxon,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('referentieltaxon/edit.html.twig', array(
            'referentielTaxon' => $referentielTaxon,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a referentielTaxon entity.
     *
     * @Route("/{id}", name="referentieltaxon_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, ReferentielTaxon $referentielTaxon)
    {
        $form = $this->createDeleteForm($referentielTaxon);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($referentielTaxon);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('referentieltaxon/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('referentieltaxon_index');
    }

    /**
     * Creates a form to delete a referentielTaxon entity.
     *
     * @param ReferentielTaxon $referentielTaxon The referentielTaxon entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ReferentielTaxon $referentielTaxon)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('referentieltaxon_delete', array('id' => $referentielTaxon->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/species-in-genus", name="species-in-genus")
     */
    public function speciesInGenus(Request $request)
    {
        $genus = $request->request->get('genus');
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();

        $query = $qb->select('rt.species')
            ->from('BbeesE3sBundle:ReferentielTaxon', 'rt')
            ->where('rt.species IS NOT NULL')
            ->andWhere('rt.genus = :genus')
            ->setParameter('genus', $genus)
            ->distinct()
            ->orderBy('rt.species')
            ->getQuery();

        $species_set = $query->getResult();

        return new JsonResponse(array('data' => $species_set));
    }

    /**
     * @Route("/taxname-search", name="taxname-search")
     */
    public function taxnameSearch(Request $request)
    {
        $genus = $request->request->get('genus');
        $species = $request->request->get('species');
        $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();

        $query = $qb->select('rt')
            ->from('BbeesE3sBundle:ReferentielTaxon', 'rt')
            ->where('rt.species IS NOT NULL')
            ->andWhere('rt.genus = :genus AND rt.species = :species')
            ->setParameters([
                'genus'=> $genus,
                'species' => $species
            ])
            ->orderBy('rt.taxname')
            ->getQuery();

        $taxname_set = $query->getArrayResult();

        return new JsonResponse(array('data' => $taxname_set));
    }
}
