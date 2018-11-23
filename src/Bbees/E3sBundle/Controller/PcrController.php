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

use Bbees\E3sBundle\Entity\Pcr;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Pcr controller.
 *
 * @Route("pcr")
 * @Security("has_role('ROLE_INVITED')")
 * 
 */
class PcrController extends Controller
{
    /**
     * Lists all pcr entities.
     *
     * @Route("/", name="pcr_index")
     * @Route("/", name="pcrchromato_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pcrs = $em->getRepository('BbeesE3sBundle:Pcr')->findAll();

        return $this->render('pcr/index.html.twig', array(
            'pcrs' => $pcrs,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="pcr_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('pcr.dateMaj' => 'desc', 'pcr.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(individu.codeIndBiomol) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND pcr.adnFk = '.$request->get('idFk');
        }
        // Search for the list to show
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Pcr")->createQueryBuilder('pcr')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
            ->leftJoin('BbeesE3sBundle:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocQualitePcr', 'WITH', 'pcr.qualitePcrVocFk = vocQualitePcr.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        $lastTaxname = '';
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DatePcr = ($entity->getDatePcr() !== null) ?  $entity->getDatePcr()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // Search chromatograms associated to a PCR
            $query = $em->createQuery('SELECT chromato.id FROM BbeesE3sBundle:Chromatogramme chromato WHERE chromato.pcrFk = '.$id.'')->getResult();
            $linkChromatogramme= (count($query) > 0) ? $id : '';
            // concatenated list of people 
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM BbeesE3sBundle:PcrEstRealisePar erp JOIN erp.personneFk p WHERE erp.pcrFk = '.$id.'')->getResult();            
            $arrayListePersonne = array();
            foreach($query as $taxon) {
                 $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne= implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array("id" => $id, "pcr.id" => $id, 
             "individu.codeIndBiomol" => $entity->getAdnFk()->getIndividuFk()->getCodeIndBiomol(),
             "adn.codeAdn" => $entity->getAdnFk()->getCodeAdn(),
             "pcr.codePcr" => $entity->getCodePcr(),
             "pcr.numPcr" => $entity->getNumPcr(),
             "vocGene.code" => $entity->getGeneVocFk()->getCode(), 
             "listePersonne" => $listePersonne, 
             "pcr.datePcr" => $DatePcr ,
             "vocQualitePcr.code" => $entity->getQualitePcrVocFk()->getCode(), 
             "vocSpecificite.code" => $entity->getSpecificiteVocFk()->getCode(), 
             "pcr.dateCre" => $DateCre, "pcr.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "pcr.userCre" => $service->GetUserCreUsername($entity) ,"pcr.userMaj" => $service->GetUserMajUsername($entity),
             "linkChromatogramme" => $linkChromatogramme,);
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
     * Creates a new pcr entity.
     *
     * @Route("/new", name="pcr_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $pcr = new Pcr();
        $form = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pcr);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('pcr_edit', array('id' => $pcr->getId(), 'valid' => 1));                       
        }

        return $this->render('pcr/edit.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pcr entity.
     *
     * @Route("/{id}", name="pcr_show")
     * @Method("GET")
     */
    public function showAction(Pcr $pcr)
    {
        $deleteForm = $this->createDeleteForm($pcr);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);

        return $this->render('show.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pcr entity.
     *
     * @Route("/{id}/edit", name="pcr_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     * 
     */
    public function editAction(Request $request, Pcr $pcr)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $pcr->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        // store ArrayCollection       
        $pcrEstRealisePars = $service->setArrayCollection('PcrEstRealisePars',$pcr);
        //
        $deleteForm = $this->createDeleteForm($pcr);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollection('PcrEstRealisePars',$pcr, $pcrEstRealisePars);
            // flush
            $this->getDoctrine()->getManager()->persist($pcr);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            //return $this->redirectToRoute('lotmateriel_edit', array('id' => $lotMateriel->getId()));
            // return $this->redirectToRoute('lotmateriel_index');
            return $this->render('pcr/edit.html.twig', array(
                'pcr' => $pcr,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('pcr/edit.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pcr entity.
     *
     * @Route("/{id}", name="pcr_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     * 
     */
    public function deleteAction(Request $request, Pcr $pcr)
    {
        $form = $this->createDeleteForm($pcr);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($pcr);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('pcr_index');
    }

    /**
     * Creates a form to delete a pcr entity.
     *
     * @param Pcr $pcr The pcr entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pcr $pcr)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pcr_delete', array('id' => $pcr->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
