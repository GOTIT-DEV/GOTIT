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

use App\Entity\Etablissement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Etablissement controller.
 *
 * @Route("etablissement")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class EtablissementController extends Controller
{
    /**
     * Lists all etablissement entities.
     *
     * @Route("/", name="etablissement_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $etablissements = $em->getRepository('App:Etablissement')->findAll();

        return $this->render('Core/etablissement/index.html.twig', array(
            'etablissements' => $etablissements,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="etablissement_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service)
    {
        // load Doctrine Manager        
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('etablissement.dateMaj' => 'desc', 'etablissement.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(etablissement.nomEtablissement) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Search for the list to show
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("App:Etablissement")->createQueryBuilder('etablissement')
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
            $tab_toshow[] = array("id" => $id, "etablissement.id" => $id, 
             "etablissement.nomEtablissement" => $entity->getNomEtablissement(),
             "etablissement.dateCre" => $DateCre, "etablissement.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "etablissement.userCre" => $service->GetUserCreUsername($entity) ,"etablissement.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new etablissement entity.
     *
     * @Route("/new", name="etablissement_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function newAction(Request $request)
    {
        $etablissement = new Etablissement();
        $form = $this->createForm('App\Form\EtablissementType', $etablissement);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($etablissement);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/etablissement/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('etablissement_edit', array('id' => $etablissement->getId(), 'valid' => 1));                       
        }

        return $this->render('Core/etablissement/edit.html.twig', array(
            'etablissement' => $etablissement,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a etablissement entity.
     *
     * @Route("/{id}", name="etablissement_show", methods={"GET"})
     */
    public function showAction(Etablissement $etablissement)
    {
        $deleteForm = $this->createDeleteForm($etablissement);
        $editForm = $this->createForm('App\Form\EtablissementType', $etablissement);

        return $this->render('show.html.twig', array(
            'etablissement' => $etablissement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing etablissement entity.
     *
     * @Route("/{id}/edit", name="etablissement_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function editAction(Request $request, Etablissement $etablissement)
    {
        $deleteForm = $this->createDeleteForm($etablissement);
        $editForm = $this->createForm('App\Form\EtablissementType', $etablissement);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/etablissement/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('Core/etablissement/edit.html.twig', array(
                'etablissement' => $etablissement,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('Core/etablissement/edit.html.twig', array(
            'etablissement' => $etablissement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a etablissement entity.
     *
     * @Route("/{id}", name="etablissement_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function deleteAction(Request $request, Etablissement $etablissement)
    {
        $form = $this->createDeleteForm($etablissement);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($etablissement);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('Core/etablissement/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('etablissement_index');
    }

    /**
     * Creates a form to delete a etablissement entity.
     *
     * @param Etablissement $etablissement The etablissement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Etablissement $etablissement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('etablissement_delete', array('id' => $etablissement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
