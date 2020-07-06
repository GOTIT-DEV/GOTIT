<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
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

namespace App\Controller\Core;

use App\Entity\Programme;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Programme controller.
 *
 * @Route("programme")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ProgrammeController extends Controller
{
    /**
     * Lists all programme entities.
     *
     * @Route("/", name="programme_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $programmes = $em->getRepository('App:Programme')->findAll();

        return $this->render('programme/index.html.twig', array(
            'programmes' => $programmes,
        ));
    }   

    
    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="programme_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service)
    {
        // load Doctrine Manager        
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('programme.dateMaj' => 'desc', 'programme.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(programme.codeProgramme) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Search for the list to show
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("App:Programme")->createQueryBuilder('programme')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($entities_toshow);
        $entities_toshow = ($request->get('rowCount') > 0 ) ? array_slice($entities_toshow, $minRecord, $rowCount) : array_slice($entities_toshow, $minRecord); 
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            //
            $tab_toshow[] = array("id" => $id, "programme.id" => $id, 
             "programme.codeProgramme" => $entity->getCodeProgramme(),
             "programme.typeFinanceur" => $entity->getTypeFinanceur(),
             "programme.nomProgramme" => $entity->getNomProgramme(),
             "programme.nomsResponsables" => $entity->getNomsResponsables(),
             "programme.anneeDebut" => $entity->getAnneeDebut(),
             "programme.anneeFin" => $entity->getAnneeFin(),
             "programme.dateCre" => $DateCre, "programme.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "programme.userCre" => $service->GetUserCreUsername($entity) ,"programme.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new programme entity.
     *
     * @Route("/new", name="programme_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function newAction(Request $request)
    {
        $programme = new Programme();
        $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            try {
                $flush = $em->flush();
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('programme_edit', array('id' => $programme->getId(), 'valid' => 1)); 
        }

       return $this->render('programme/edit.html.twig', array(
            'programme' => $programme,
            'edit_form' => $form->createView(),
        ));
                
    }


    /**
     * Creates a new programme entity for modal windows
     *
     * @Route("/newmodal", name="programme_newmodal", methods={"GET", "POST"})
     */
    public function newmodalAction(Request $request)
    {
        $programme = new Programme();
        $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // flush des données du formulaire
            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            
            try {
                $flush = $em->flush();
                // mémorize the id and the name of the Program 
                $select_id = $programme->getId();
                $select_name = $programme->getCodeProgramme();
                // return an empty Program Entity
                $programme_new = new Programme();
                $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType',$programme_new);           
                //returns an empty form and the parameters of the new record created
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'programme', 'form' => $form->createView()))->getContent(),
                    'select_id' => $select_id,
                    'select_name' => $select_name,
                    'exception_message' => "",
                    'entityname' => 'programme',
                    ) ) );	
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message = strval($e);
                // return an empty Program Entity
                $programme_new = new Programme();
                $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType',$programme_new);   
                // returns a form with the error message
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'programme', 'form' => $form->createView()))->getContent(),
                    'select_id' => 0,
                    'select_name' => "",
                    'exception_message' => $exception_message,
                    'entityname' => 'programme',
                    ) ) );	
                }   
            If ($request->isXmlHttpRequest()){
                // If it is an Ajax request: returns the content in json format
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                var_dump("l appel a la fonction newmodalAction du controller ProgrammeController n est pas de type XmlHttpRequest"); exit;
            }
        }

        return $this->render('modal.html.twig', array(
            'entityname' => 'programme',
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a programme entity.
     *
     * @Route("/{id}", name="programme_show", methods={"GET"})
     */
    public function showAction(Programme $programme)
    {
        $deleteForm = $this->createDeleteForm($programme);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);

        return $this->render('show.html.twig', array(
            'programme' => $programme,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing programme entity.
     *
     * @Route("/{id}/edit", name="programme_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function editAction(Request $request, Programme $programme)
    {
        //
        $deleteForm = $this->createDeleteForm($programme);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('programme/edit.html.twig', array(
                'programme' => $programme,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('programme/edit.html.twig', array(
            'programme' => $programme,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a programme entity.
     *
     * @Route("/{id}", name="programme_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function deleteAction(Request $request, Programme $programme)
    {
        $form = $this->createDeleteForm($programme);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($programme);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('programme_index');
    }

    /**
     * Creates a form to delete a programme entity.
     *
     * @param Programme $programme The programme entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Programme $programme)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('programme_delete', array('id' => $programme->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
