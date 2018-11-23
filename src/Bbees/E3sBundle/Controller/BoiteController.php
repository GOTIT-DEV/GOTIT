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

use Bbees\E3sBundle\Entity\Boite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Bbees\E3sBundle\Entity\Voc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Boite controller.
 *
 * @Route("boite")
 * @Security("has_role('ROLE_INVITED')")
 */
class BoiteController extends Controller
{
    /**
     * Lists all boite entities.
     *
     * @Route("/", name="boite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $boites = $em->getRepository('BbeesE3sBundle:Boite')->findAll();

        return $this->render('boite/index.html.twig', array(
            'boites' => $boites,
        ));
    }

     /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="boite_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('boite.dateMaj' => 'desc', 'boite.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(boite.codeBoite) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('typeBoite') !== null && $request->get('typeBoite') !== '' ) {
            $where .= " AND vocTypeBoite.code LIKE '".$request->get('typeBoite')."'";
        }
        // Search for the list to show EstAligneEtTraite
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocCodeCollection', 'WITH', 'boite.codeCollectionVocFk = vocCodeCollection.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocTypeBoite', 'WITH', 'boite.typeBoiteVocFk = vocTypeBoite.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        $lastTaxname = '';
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;       
            //
            $tab_toshow[] = array("id" => $id, "boite.id" => $id, 
             "boite.codeBoite" => $entity->getCodeBoite(),
             "vocCodeCollection.code" => $entity->getCodeCollectionVocFk()->getCode(),   
             "boite.libelleBoite" => $entity->getLibelleBoite(),
             "vocCodeCollection.libelle" => $entity->getCodeCollectionVocFk()->getLibelle(),
             "boite.dateCre" => $DateCre, "boite.dateMaj" => $DateMaj, 
             "userCreId" => $service->GetUserCreId($entity), "boite.userCre" => $service->GetUserCreUsername($entity) ,"boite.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new boite entity.
     *
     * @Route("/new", name="boite_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $boite = new Boite();
        $form = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();            
            $em->persist($boite);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('boite/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            return $this->redirectToRoute('boite_edit', array('id' => $boite->getId(), 'valid' => 1, 'typeBoite' => $request->get('typeBoite')));                     
        } 

        return $this->render('boite/edit.html.twig', array(
            'boite' => $boite,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a boite entity.
     *
     * @Route("/{id}", name="boite_show")
     * @Method("GET")
     */
    public function showAction(Boite $boite)
    {
        $deleteForm = $this->createDeleteForm($boite);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);

        return $this->render('show.html.twig', array(
            'boite' => $boite,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing boite entity.
     *
     * @Route("/{id}/edit", name="boite_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Boite $boite)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $boite->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load the Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        $deleteForm = $this->createDeleteForm($boite);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($boite);
            // flush
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('boite/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            $editForm = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);
            
           return $this->render('boite/edit.html.twig', array(
                'boite' => $boite,
                'edit_form' => $editForm->createView(),
                'valid' => 1,
                ));
        }
        
        return $this->render('boite/edit.html.twig', array(
             'boite' => $boite,
             'edit_form' => $editForm->createView(),
             'delete_form' => $deleteForm->createView(),
             ));
    }

    /**
     * Deletes a boite entity.
     *
     * @Route("/{id}", name="boite_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Boite $boite)
    {
        $form = $this->createDeleteForm($boite);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($boite);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('boite/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('boite_index', array('typeBoite' => $request->get('typeBoite')));
    }

    /**
     * Creates a form to delete a boite entity.
     *
     * @param Boite $boite The boite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Boite $boite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boite_delete', array('id' => $boite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
