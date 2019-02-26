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

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Individu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Individu controller.
 *
 * @Route("individu")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class IndividuController extends Controller
{
    /**
     * Lists all individu entities.
     *
     * @Route("/", name="individu_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $individus = $em->getRepository('BbeesE3sBundle:Individu')->findAll();

        return $this->render('individu/index.html.twig', array(
            'individus' => $individus,
        ));
    }
    
    
    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="individu_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('individu.dateMaj' => 'desc', 'individu.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(individu.codeIndTriMorpho) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND individu.lotMaterielFk = '.$request->get('idFk');
        }
        // Search for the list to show
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Individu")->createQueryBuilder('individu')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:LotMateriel', 'lot', 'WITH', 'individu.lotMaterielFk = lot.id')
                ->leftJoin('BbeesE3sBundle:Collecte', 'collecte', 'WITH', 'lot.collecteFk = collecte.id')
                    ->leftJoin('BbeesE3sBundle:Station', 'station', 'WITH', 'collecte.stationFk = station.id')
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
            // Search DNA associated to a specimen
            $query = $em->createQuery('SELECT adn.id FROM BbeesE3sBundle:Adn adn WHERE adn.individuFk = '.$id.'')->getResult();
            $linkAdn = (count($query) > 0) ? $id : '';
            // Search the slide associated to a specimen
            $query = $em->createQuery('SELECT lame.id FROM BbeesE3sBundle:IndividuLame lame WHERE lame.individuFk = '.$id.'')->getResult();
            $linkIndividulame = (count($query) > 0) ? $id : '';
            // Search for the first identified taxon         
            $query = $em->createQuery('SELECT ei.id, ei.dateIdentification, rt.taxname as taxname, voc.code as codeIdentification FROM BbeesE3sBundle:EspeceIdentifiee ei JOIN ei.referentielTaxonFk rt JOIN ei.critereIdentificationVocFk voc WHERE ei.individuFk = '.$id.' ORDER BY ei.id DESC')->getResult(); 
            $lastTaxname = ($query[0]['taxname'] !== NULL) ? $query[0]['taxname'] : NULL;
            $lastdateIdentification = ($query[0]['dateIdentification']  !== NULL) ? $query[0]['dateIdentification']->format('Y-m-d') : NULL; 
            $codeIdentification = ($query[0]['codeIdentification'] !== NULL) ? $query[0]['codeIdentification'] : NULL;
            // 
            $tab_toshow[] = array("id" => $id, "individu.id" => $id, 
             "station.codeStation" => $entity->getLotMaterielFk()->getCollecteFk()->getStationFk()->getCodeStation(),
             "individu.codeIndTriMorpho" => $entity->getCodeIndTriMorpho(),
             "individu.codeIndBiomol" => $entity->getCodeIndBiomol(),
             "voc.code" => $entity->getTypeIndividuVocFk()->getCode(),  
             "individu.numIndBiomol" => $entity->getNumIndBiomol(),
             "individu.codeTube" => $entity->getCodeTube(),
             "lastTaxname" => $lastTaxname,
             "lastdateIdentification" => $lastdateIdentification ,
             "codeIdentification" => $codeIdentification ,
             "individu.dateCre" => $DateCre, "individu.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "individu.userCre" => $service->GetUserCreUsername($entity) ,"individu.userMaj" => $service->GetUserMajUsername($entity),
             "linkAdn" => $linkAdn, "linkIndividulame" => $linkIndividulame );
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
     * Creates a new individu entity.
     *
     * @Route("/new", name="individu_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $individu = new Individu();
        $form = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($individu);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('individu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('individu_edit', array('id' => $individu->getId(), 'valid' => 1));                       
        }

        return $this->render('individu/edit.html.twig', array(
            'individu' => $individu,
            'edit_form' => $form->createView(),
        ));

    }

    /**
     * Finds and displays a individu entity.
     *
     * @Route("/{id}", name="individu_show")
     * @Method("GET")
     */
    public function showAction(Individu $individu)
    {
        $deleteForm = $this->createDeleteForm($individu);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu);

        return $this->render('show.html.twig', array(
            'individu' => $individu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing individu entity.
     *
     * @Route("/{id}/edit", name="individu_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Individu $individu)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $individu->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        // load service  generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');    
        // store ArrayCollection       
        $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$individu);
        
        $deleteForm = $this->createDeleteForm($individu);
        if ($individu->getCodeIndBiomol()  === NULL || $individu->getCodeIndBiomol() == '' ) {
            $flag_indbiomol = 1;
            $editForm = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu, ['refTaxonLabel' => 'codeTaxon']);
        } else {
            $flag_indbiomol = 0;
            $editForm = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu);
        }
        $editForm->handleRequest($request);

        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$individu, $especeIdentifiees);
            // flush
            $this->getDoctrine()->getManager()->persist($individu);                       
            try {
                $this->getDoctrine()->getManager()->flush();
                if ($individu->getCodeIndBiomol()  === NULL || $individu->getCodeIndBiomol() == '' ){
                    $flag_indbiomol = 1;
                } else {
                    $flag_indbiomol = 0;
                }
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('individu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('individu/edit.html.twig', array(
                'individu' => $individu,
                'edit_form' => $editForm->createView(),
                'valid' => 1,
                'flag_indbiomol' => $flag_indbiomol,
                ));
        }
        
        return $this->render('individu/edit.html.twig', array(
            'individu' => $individu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'flag_indbiomol' => $flag_indbiomol,
        ));

    }

    /**
     * Deletes a individu entity.
     *
     * @Route("/{id}", name="individu_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Individu $individu)
    {
        $form = $this->createDeleteForm($individu);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($individu);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('individu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }
        
        return $this->redirectToRoute('individu_index');
    }

    /**
     * Creates a form to delete a individu entity.
     *
     * @param Individu $individu The individu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Individu $individu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('individu_delete', array('id' => $individu->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
