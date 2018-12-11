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

use Bbees\E3sBundle\Entity\LotMateriel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Lotmateriel controller.
 *
 * @Route("lotmateriel")
 * @Security("has_role('ROLE_INVITED')")
 */
class LotMaterielController extends Controller
{
    /**
     * Lists all lotMateriel entities.
     *
     * @Route("/", name="lotmateriel_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotMateriels = $em->getRepository('BbeesE3sBundle:LotMateriel')->findAll();

        return $this->render('lotmateriel/index.html.twig', array(
            'lotMateriels' => $lotMateriels,
        ));
    }
    
    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="lotmateriel_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('lotMateriel.dateMaj' => 'desc', 'lotMateriel.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(lotMateriel.codeLotMateriel) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND lotMateriel.collecteFk = '.$request->get('idFk');
        }
        // Search for the list to show
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:LotMateriel")->createQueryBuilder('lotMateriel')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Collecte', 'collecte', 'WITH', 'lotMateriel.collecteFk = collecte.id')
            ->leftJoin('BbeesE3sBundle:Station', 'station', 'WITH', 'collecte.stationFk = station.id')
            ->leftJoin('BbeesE3sBundle:Pays', 'pays', 'WITH', 'station.paysFk = pays.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        $lastTaxname = '';
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $codeStation = $entity->getCollecteFk()->getStationFk()->getCodeStation();
            $DateLot = ($entity->getDateLotMateriel() !== null) ?  $entity->getDateLotMateriel()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // search for specimen associated to a material
            $query = $em->createQuery('SELECT ind.id FROM BbeesE3sBundle:Individu ind WHERE ind.lotMaterielFk = '.$id.'')->getResult();
            $linkIndividu = (count($query) > 0) ? $id : '';
            // load the first identified taxon            
            $query = $em->createQuery('SELECT ei.id, ei.dateIdentification, rt.taxname as taxname, voc.libelle as codeIdentification FROM BbeesE3sBundle:EspeceIdentifiee ei JOIN ei.referentielTaxonFk rt JOIN ei.critereIdentificationVocFk voc WHERE ei.lotMaterielFk = '.$id.' ORDER BY ei.id DESC')->getResult(); 
            $lastTaxname = ($query[0]['taxname'] !== NULL) ? $query[0]['taxname'] : NULL;
            $lastdateIdentification = ($query[0]['dateIdentification']  !== NULL) ? $query[0]['dateIdentification']->format('Y-m-d') : NULL;
            $codeIdentification = ($query[0]['codeIdentification'] !== NULL) ? $query[0]['codeIdentification'] : NULL;
            //  concatenated list of people
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM BbeesE3sBundle:LotMaterielEstRealisePar lmerp JOIN lmerp.personneFk p WHERE lmerp.lotMaterielFk = '.$id.'')->getResult();            
            $arrayListePersonne = array();
            foreach($query as $taxon) {
                 $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne= implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array("id" => $id, "lotMateriel.id" => $id, "lotMateriel.codeLotMateriel" => $entity->getCodeLotMateriel(),
             "lotMateriel.commentaireConseilSqc" => $entity->getCommentaireConseilSqc(),"listePersonne" => $listePersonne, "collecte.codeCollecte" => $entity->getCollecteFk()->getCodeCollecte(),
             "lotMateriel.dateLotMateriel" => $DateLot ,"lotMateriel.dateCre" => $DateCre, "lotMateriel.dateMaj" => $DateMaj,
             "lastTaxname" => $lastTaxname, "lastdateIdentification" => $lastdateIdentification , "codeIdentification" => $codeIdentification ,
             "pays.nomPays" => $entity->getCollecteFk()->getStationFk()->getpaysFk()->getNomPays(),
             "lotMateriel.aFaire" => $entity->getAfaire(),   
             "commune.codeCommune" => $entity->getCollecteFk()->getStationFk()->getCommuneFk()->getCodeCommune(),
             "userCreId" => $service->GetUserCreId($entity), "lotMateriel.userCre" => $service->GetUserCreUsername($entity) ,"lotMateriel.userMaj" => $service->GetUserMajUsername($entity),
             "linkIndividu" => $linkIndividu, "linkIndividu_codestation" => "%|".$codeStation."_%",);
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
     * Creates a new lotMateriel entity.
     *
     * @Route("/new", name="lotmateriel_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $lotMateriel = new Lotmateriel();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotMaterielType', $lotMateriel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotMateriel);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmateriel/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('lotmateriel_edit', array('id' => $lotMateriel->getId(), 'valid' => 1));                       
        }

        return $this->render('lotmateriel/edit.html.twig', array(
            'lotMateriel' => $lotMateriel,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotMateriel entity.
     *
     * @Route("/{id}", name="lotmateriel_show")
     * @Method("GET")
     */
    public function showAction(LotMateriel $lotMateriel)
    {
        $deleteForm = $this->createDeleteForm($lotMateriel);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielType', $lotMateriel);

        return $this->render('show.html.twig', array(
            'lotMateriel' => $lotMateriel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing lotMateriel entity.
     *
     * @Route("/{id}/edit", name="lotmateriel_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, LotMateriel $lotMateriel)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $lotMateriel->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');
        
        // store ArrayCollection       
        $compositionLotMateriels = $service->setArrayCollection('CompositionLotMateriels',$lotMateriel);
        $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$lotMateriel);
        $lotEstPublieDanss = $service->setArrayCollection('LotEstPublieDanss',$lotMateriel);
        $lotMaterielEstRealisePars = $service->setArrayCollection('LotMaterielEstRealisePars',$lotMateriel);
        
        // 
        $deleteForm = $this->createDeleteForm($lotMateriel);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielType', $lotMateriel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollection('CompositionLotMateriels',$lotMateriel, $compositionLotMateriels);
            $service->DelArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$lotMateriel, $especeIdentifiees);
            $service->DelArrayCollection('LotEstPublieDanss',$lotMateriel, $lotEstPublieDanss);
            $service->DelArrayCollection('LotMaterielEstRealisePars',$lotMateriel, $lotMaterielEstRealisePars);
            // flush
            $this->getDoctrine()->getManager()->persist($lotMateriel);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmateriel/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('lotmateriel/edit.html.twig', array(
                'lotMateriel' => $lotMateriel,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('lotmateriel/edit.html.twig', array(
            'lotMateriel' => $lotMateriel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotMateriel entity.
     *
     * @Route("/{id}", name="lotmateriel_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, LotMateriel $lotMateriel)
    {
        $form = $this->createDeleteForm($lotMateriel);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($lotMateriel);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmateriel/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('lotmateriel_index');
    }

    /**
     * Creates a form to delete a lotMateriel entity.
     *
     * @param LotMateriel $lotMateriel The lotMateriel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotMateriel $lotMateriel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotmateriel_delete', array('id' => $lotMateriel->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
