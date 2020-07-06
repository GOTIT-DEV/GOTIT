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

use App\Entity\SequenceAssembleeExt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Core\GenericFunctionE3s;
use App\Entity\Voc;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Translation\TranslatorInterface; 

/**
 * Sequenceassembleeext controller.
 *
 * @Route("sequenceassembleeext")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeExtController extends Controller
{
    /**
     * Lists all sequenceAssembleeExt entities.
     *
     * @Route("/", name="sequenceassembleeext_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sequenceAssembleeExts = $em->getRepository('App:SequenceAssembleeExt')->findAll();

        return $this->render('sequenceassembleeext/index.html.twig', array(
            'sequenceAssembleeExts' => $sequenceAssembleeExts,
        ));
    }

    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="sequenceassembleeext_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service, TranslatorInterface $translator)
    {
        // load Doctrine Manager          
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('sequenceAssembleeExt.dateMaj' => 'desc', 'sequenceAssembleeExt.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(sequenceAssembleeExt.codeSqcAssExt) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND sequenceAssembleeExt.collecteFk = '.$request->get('idFk');
        }
        // Search for the list to show EstAligneEtTraite
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("App:SequenceAssembleeExt")->createQueryBuilder('sequenceAssembleeExt')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('App:Voc', 'vocStatutSqcAss', 'WITH', 'sequenceAssembleeExt.statutSqcAssVocFk = vocStatutSqcAss.id')
            ->leftJoin('App:Voc', 'vocGene', 'WITH', 'sequenceAssembleeExt.geneVocFk = vocGene.id')
            ->leftJoin('App:Collecte', 'collecte', 'WITH', 'sequenceAssembleeExt.collecteFk = collecte.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($entities_toshow);
        $entities_toshow = ($request->get('rowCount') > 0 ) ? array_slice($entities_toshow, $minRecord, $rowCount) : array_slice($entities_toshow, $minRecord);
        $lastTaxname = '';
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            $dateCreationSqcAssExt = ($entity->getdateCreationSqcAssExt() !== null) ?  $entity->getdateCreationSqcAssExt()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;       
            // load the first identified taxon            
            $query = $em->createQuery('SELECT ei.id, ei.dateIdentification, rt.taxname as taxname, voc.code as codeIdentification FROM App:EspeceIdentifiee ei JOIN ei.referentielTaxonFk rt JOIN ei.critereIdentificationVocFk voc WHERE ei.sequenceAssembleeExtFk = '.$id.' ORDER BY ei.id DESC')->getResult(); 
            $lastTaxname = ($query[0]['taxname'] !== NULL) ? $query[0]['taxname'] : NULL;
            $lastdateIdentification = ($query[0]['dateIdentification']  !== NULL) ? $query[0]['dateIdentification']->format('Y-m-d') : NULL; 
            $codeIdentification = ($query[0]['codeIdentification'] !== NULL) ? $query[0]['codeIdentification'] : NULL;
            // search for motu associated to a sequence
            $query = $em->createQuery('SELECT a.id FROM App:Assigne a JOIN a.sequenceAssembleeExtFk sqc  WHERE a.sequenceAssembleeExtFk = '.$id.' ')->getResult();
            $motuAssigne = (count($query) > 0) ? 1 : 0;
            // search for sources associated to a sequence
            $query = $em->createQuery('SELECT s.codeSource as source FROM App:SqcExtEstReferenceDans seerd JOIN seerd.sourceFk s WHERE seerd.sequenceAssembleeExtFk = '.$id.'')->getResult();            
            $arrayListeSource = array();
            foreach($query as $taxon) {
                 $arrayListeSource[] = $taxon['source'];
            }
            $listSource = implode(", ", $arrayListeSource);
            //
            $tab_toshow[] = array("id" => $id, "sequenceAssembleeExt.id" => $id, 
             "sequenceAssembleeExt.codeSqcAssExtAlignement" => $entity->getCodeSqcAssExtAlignement(),
             "sequenceAssembleeExt.codeSqcAssExt" => $entity->getCodeSqcAssExt(),
             "sequenceAssembleeExt.accessionNumberSqcAssExt" => $entity->getAccessionNumberSqcAssExt(),
             "vocGene.code" => $entity->getGeneVocFk()->getCode(), 
             "vocDatePrecision.libelle" => $translator->trans($entity->getDatePrecisionVocFk()->getLibelle()), 
             "vocStatutSqcAss.code" => $entity->getStatutSqcAssVocFk()->getCode(),                 
             "sequenceAssembleeExt.dateCreationSqcAssExt" => $dateCreationSqcAssExt,  
             "sequenceAssembleeExt.taxonOrigineSqcAssExt" => $entity->getTaxonOrigineSqcAssExt(),
             "sequenceAssembleeExt.numIndividuSqcAssExt" => $entity->getNumIndividuSqcAssExt(),
             "vocStatutSqcAss.code"  => $entity->getStatutSqcAssVocFk()->getCode(),
             "collecte.codeCollecte" => $entity->getCollecteFk()->getCodeCollecte(),
             "lastTaxname" => $lastTaxname,   
             "lastdateIdentification" => $lastdateIdentification ,
             "codeIdentification" => $codeIdentification ,
             "listSource" => $listSource, 
             "motuAssigne" => $motuAssigne ,
             "sequenceAssembleeExt.dateCre" => $DateCre, "sequenceAssembleeExt.dateMaj" => $DateMaj,  
             "userCreId" => $service->GetUserCreId($entity), "sequenceAssembleeExt.userCre" => $service->GetUserCreUsername($entity) ,"sequenceAssembleeExt.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new sequenceAssembleeExt entity.
     *
     * @Route("/new", name="sequenceassembleeext_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $sequenceAssembleeExt = new Sequenceassembleeext();
        $em = $this->getDoctrine()->getManager();
        // check if the relational Entity (Collecte) is given and set the RelationalEntityFk for the new Entity
        if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
            $RelEntityId = $request->get('idFk');
            $RelEntity = $em->getRepository('App:Collecte')->find($RelEntityId);
            $sequenceAssembleeExt->setCollecteFk($RelEntity);
        }
        $form = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeExtType', $sequenceAssembleeExt, ['refTaxonLabel' => 'codeTaxon']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // (i) load the id  the relational Entity (Collecte) from typeahead input field and (ii) set the foreign key 
            $RelEntityId = $form->get('collecteId');
            $RelEntity = $em->getRepository('App:Collecte')->find($RelEntityId->getData());
            $sequenceAssembleeExt->setCollecteFk($RelEntity);
            // persist
            $em->persist($sequenceAssembleeExt);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassembleeext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            return $this->redirectToRoute('sequenceassembleeext_edit', array('id' => $sequenceAssembleeExt->getId(), 'valid' => 1, 'idFk' => $request->get('idFk') ));                     
        } 
        
        return $this->render('sequenceassembleeext/edit.html.twig', array(
                            'sequenceAssembleeExt' => $sequenceAssembleeExt,
                            'edit_form' => $form->createView(),
        )); 
    }

    /**
     * Finds and displays a sequenceAssembleeExt entity.
     *
     * @Route("/{id}", name="sequenceassembleeext_show", methods={"GET"})
     */
    public function showAction(SequenceAssembleeExt $sequenceAssembleeExt)
    {
        $deleteForm = $this->createDeleteForm($sequenceAssembleeExt);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeExtType', $sequenceAssembleeExt);

        return $this->render('sequenceassembleeext/edit.html.twig', array(
            'sequenceAssembleeExt' => $sequenceAssembleeExt,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sequenceAssembleeExt entity.
     *
     * @Route("/{id}/edit", name="sequenceassembleeext_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, SequenceAssembleeExt $sequenceAssembleeExt, GenericFunctionE3s $service)
    {      
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $sequenceAssembleeExt->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        //  
        // load the Entity Manager
        $em = $this->getDoctrine()->getManager();
                
        // store ArrayCollection       
        $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$sequenceAssembleeExt);
        $sqcExtEstReferenceDanss = $service->setArrayCollection('SqcExtEstReferenceDanss',$sequenceAssembleeExt);
        $sqcExtEstRealisePars = $service->setArrayCollection('SqcExtEstRealisePars',$sequenceAssembleeExt);
       
        $deleteForm = $this->createDeleteForm($sequenceAssembleeExt);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeExtType', $sequenceAssembleeExt);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$sequenceAssembleeExt, $especeIdentifiees);
            $service->DelArrayCollection('SqcExtEstReferenceDanss',$sequenceAssembleeExt, $sqcExtEstReferenceDanss);
            $service->DelArrayCollection('SqcExtEstRealisePars',$sequenceAssembleeExt, $sqcExtEstRealisePars);
            // (i) load the id of relational Entity (Collecte) from typeahead input field  (ii) set the foreign key
            $em = $this->getDoctrine()->getManager();
            $RelEntityId = $editForm->get('collecteId');;
            $RelEntity = $em->getRepository('App:Collecte')->find($RelEntityId->getData());
            $sequenceAssembleeExt->setCollecteFk($RelEntity);
            // persist
            $em->persist($sequenceAssembleeExt);
            // flush
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassembleeext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeExtType', $sequenceAssembleeExt);
            
           return $this->render('sequenceassembleeext/edit.html.twig', array(
                'sequenceAssembleeExt' => $sequenceAssembleeExt,
                'edit_form' => $editForm->createView(),
                'valid' => 1,
                ));
        }
        
        return $this->render('sequenceassembleeext/edit.html.twig', array(
            'sequenceAssembleeExt' => $sequenceAssembleeExt,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sequenceAssembleeExt entity.
     *
     * @Route("/{id}", name="sequenceassembleeext_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, SequenceAssembleeExt $sequenceAssembleeExt)
    {
        $form = $this->createDeleteForm($sequenceAssembleeExt);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($sequenceAssembleeExt);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassembleeext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('sequenceassembleeext_index');
    }

    /**
     * Creates a form to delete a sequenceAssembleeExt entity.
     *
     * @param SequenceAssembleeExt $sequenceAssembleeExt The sequenceAssembleeExt entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SequenceAssembleeExt $sequenceAssembleeExt)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sequenceassembleeext_delete', array('id' => $sequenceAssembleeExt->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Creates a createCodeSqcAssExt
     *
     * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
     *
     */
    private function createCodeSqcAssExt(SequenceAssembleeExt $sequenceAssembleeExt)
    {  
        $codeSqc = '';
        $em = $this->getDoctrine()->getManager();
        $EspeceIdentifiees =  $sequenceAssembleeExt->getEspeceIdentifiees();
        $nbEspeceIdentifiees = count($EspeceIdentifiees);
        if($nbEspeceIdentifiees > 0) {
            // The status of the sequence and the referential Taxon = to the last taxname attributed
            $codeStatutSqcAss = $sequenceAssembleeExt->getStatutSqcAssVocFk()->getCode();
            $arrayReferentielTaxon = array();
                foreach ($EspeceIdentifiees as $entityEspeceIdentifiees) {
                     $arrayReferentielTaxon[$entityEspeceIdentifiees->getReferentielTaxonFk()->getId()] = $entityEspeceIdentifiees->getReferentielTaxonFk()->getCodeTaxon();
                }
            ksort($arrayReferentielTaxon);
            reset($arrayReferentielTaxon);
            $firstTaxname = current($arrayReferentielTaxon);
            $codeSqc = (substr($codeStatutSqcAss, 0, 5)=='VALID') ? $firstTaxname : $codeStatutSqcAss.'_'.$firstTaxname;              
            $codeCollecte = $sequenceAssembleeExt->getCollecteFk()->getCodeCollecte();
            $numIndividuSqcAssExt = $sequenceAssembleeExt->getNumIndividuSqcAssExt();
            $accessionNumberSqcAssExt = $sequenceAssembleeExt->getAccessionNumberSqcAssExt();
            $codeOrigineSqcAssExt = $sequenceAssembleeExt->getOrigineSqcAssExtVocFk()->getCode();
            $codeSqc = $codeSqc.'_'.$codeCollecte.'_'.$numIndividuSqcAssExt.'_'.$accessionNumberSqcAssExt.'|'.$codeOrigineSqcAssExt;
        } else {
            $codeSqc = 0;
           //var_dump($nbEspeceIdentifiees);var_dump($codeSqc); exit; 
        }
        return $codeSqc;         
    }
    
    /**
     * Creates a createCodeSqcAssExtAlignement
     *
     * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
     *
     */
    private function createCodeSqcAssExtAlignement(SequenceAssembleeExt $sequenceAssembleeExt)
    {  
        $codeSqcAlignement = '';
        $em = $this->getDoctrine()->getManager();
        $EspeceIdentifiees =  $sequenceAssembleeExt->getEspeceIdentifiees();
        $nbEspeceIdentifiees = count($EspeceIdentifiees);
        if($nbEspeceIdentifiees>0) {
            // Le statut de la sequence ET le referentiel Taxon = au derenier taxname attribué
            $codeStatutSqcAss = $sequenceAssembleeExt->getStatutSqcAssVocFk()->getCode();
            $arrayReferentielTaxon = array();
            foreach ($EspeceIdentifiees as $entityEspeceIdentifiees) {
                 $arrayReferentielTaxon[$entityEspeceIdentifiees->getReferentielTaxonFk()->getId()] = $entityEspeceIdentifiees->getReferentielTaxonFk()->getCodeTaxon();
            }
            ksort($arrayReferentielTaxon);
            end($arrayReferentielTaxon);
            $lastCodeTaxon = current($arrayReferentielTaxon);
            $codeSqcAlignement = (substr($codeStatutSqcAss, 0, 5)=='VALID')  ? $lastCodeTaxon : $codeStatutSqcAss.'_'.$lastCodeTaxon;              
            $codeCollecte = $sequenceAssembleeExt->getCollecteFk()->getCodeCollecte();
            $numIndividuSqcAssExt = $sequenceAssembleeExt->getNumIndividuSqcAssExt();
            $accessionNumberSqcAssExt = $sequenceAssembleeExt->getAccessionNumberSqcAssExt();
            $codeOrigineSqcAssExt = $sequenceAssembleeExt->getOrigineSqcAssExtVocFk()->getCode();
            $codeSqcAlignement = $codeSqcAlignement.'_'.$codeCollecte.'_'.$numIndividuSqcAssExt.'_'.$accessionNumberSqcAssExt.'_'.$codeOrigineSqcAssExt;
        }   else {
            $codeSqcAlignement = 0;
        }
        return $codeSqcAlignement;                   
    }
}
