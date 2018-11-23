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

use Bbees\E3sBundle\Entity\Pays;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Pay controller.
 *
 * @Route("pays")
 * @Security("has_role('ROLE_INVITED')")
 */
class PaysController extends Controller
{
    /**
     * Lists all pay entities.
     *
     * @Route("/", name="pays_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pays = $em->getRepository('BbeesE3sBundle:Pays')->findAll();

        return $this->render('pays/index.html.twig', array(
            'pays' => $pays,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="pays_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');           
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('pays.dateMaj' => 'desc', 'pays.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(pays.codePays) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Search for the list to show
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Pays")->createQueryBuilder('pays')
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
            $tab_toshow[] = array("id" => $id, "pays.id" => $id, 
             "pays.codePays" => $entity->getCodePays(),
             "pays.nomPays" => $entity->getNomPays(),
             "pays.dateCre" => $DateCre, "pays.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "pays.userCre" => $service->GetUserCreUsername($entity) ,"pays.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new pay entity.
     *
     * @Route("/new", name="pays_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $pays = new Pays();
        $form = $this->createForm('Bbees\E3sBundle\Form\PaysType', $pays);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pays);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pays/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('pays_edit', array('id' => $pays->getId(), 'valid' => 1));                       
        }

        return $this->render('pays/edit.html.twig', array(
            'pays' => $pays,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pay entity.
     *
     * @Route("/{id}", name="pays_show")
     * @Method("GET")
     */
    public function showAction(Pays $pays)
    {
        $deleteForm = $this->createDeleteForm($pays);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PaysType', $pays);

        return $this->render('show.html.twig', array(
            'pays' => $pays,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pay entity.
     *
     * @Route("/{id}/edit", name="pays_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Pays $pays)
    {
        $deleteForm = $this->createDeleteForm($pays);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PaysType', $pays);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pays/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('pays/edit.html.twig', array(
                'pays' => $pays,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('pays/edit.html.twig', array(
            'pays' => $pays,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pay entity.
     *
     * @Route("/{id}", name="pays_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Pays $pays)
    {
        $form = $this->createDeleteForm($pays);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($pays);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pays/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('pays_index');
    }

    /**
     * Creates a form to delete a pay entity.
     *
     * @param Pays $pay The pay entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pays $pay)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pays_delete', array('id' => $pay->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
