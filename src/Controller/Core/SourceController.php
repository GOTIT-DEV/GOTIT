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

use App\Entity\Source;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Source controller.
 *
 * @Route("source")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SourceController extends AbstractController
{
    /**
     * Lists all source entities.
     *
     * @Route("/", name="source_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sources = $em->getRepository('App:Source')->findAll();

        return $this->render('Core/source/index.html.twig', array(
            'sources' => $sources,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="source_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service)
    {
        // load Doctrine Manager       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('source.dateMaj' => 'desc', 'source.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(source.codeSource) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPattern') !== null && $request->get('searchPattern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPattern');
        }
        // Search for the list to show
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("App:Source")->createQueryBuilder('source')
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
            $tab_toshow[] = array("id" => $id, "source.id" => $id, 
             "source.codeSource" => $entity->getCodeSource(),
             "source.anneeSource" => $entity->getAnneeSource(),
             "source.libelleSource" => $entity->getLibelleSource(),
             "source.dateCre" => $DateCre, "source.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "source.userCre" => $service->GetUserCreUsername($entity) ,"source.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new source entity.
     *
     * @Route("/new", name="source_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $source = new Source();
        $form = $this->createForm('App\Form\SourceType', $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/source/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('source_edit', array('id' => $source->getId(), 'valid' => 1));                       
        }

        return $this->render('Core/source/edit.html.twig', array(
            'source' => $source,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a source entity.
     *
     * @Route("/{id}", name="source_show", methods={"GET"})
     */
    public function showAction(Source $source)
    {
        $deleteForm = $this->createDeleteForm($source);
        $editForm = $this->createForm('App\Form\SourceType', $source);

        return $this->render('show.html.twig', array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
        
    }

    /**
     * Displays a form to edit an existing source entity.
     *
     * @Route("/{id}/edit", name="source_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Source $source, GenericFunctionE3s $service)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $source->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        //        
        // store ArrayCollection       
        $sourceAEteIntegrePars = $service->setArrayCollection('SourceAEteIntegrePars',$source);
        //
        $deleteForm = $this->createDeleteForm($source);
        $editForm = $this->createForm('App\Form\SourceType', $source);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                // delete ArrayCollection
                $service->DelArrayCollection('SourceAEteIntegrePars',$source, $sourceAEteIntegrePars);
                // flush
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/source/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('Core/source/edit.html.twig', array(
                'source' => $source,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('Core/source/edit.html.twig', array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a source entity.
     *
     * @Route("/{id}", name="source_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Source $source)
    {
        $form = $this->createDeleteForm($source);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($source);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/source/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('source_index');
    }

    /**
     * Creates a form to delete a source entity.
     *
     * @param Source $source The source entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Source $source)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('source_delete', array('id' => $source->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
