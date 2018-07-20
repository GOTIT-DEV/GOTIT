<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\LotMaterielExt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;

/**
 * Lotmaterielext controller.
 *
 * @Route("lotmaterielext")
 */
class LotMaterielExtController extends Controller
{
    /**
     * Lists all lotMaterielExt entities.
     *
     * @Route("/", name="lotmaterielext_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotMaterielExts = $em->getRepository('BbeesE3sBundle:LotMaterielExt')->findAll();

        return $this->render('lotmaterielext/index.html.twig', array(
            'lotMaterielExts' => $lotMaterielExts,
        ));
    }

     /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="lotmaterielext_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
       
        $em = $this->getDoctrine()->getManager();
        
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('lotMaterielExt.dateMaj' => 'desc', 'lotMaterielExt.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(lotMaterielExt.codeLotMaterielExt) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND lotMaterielExt.collecteFk = '.$request->get('idFk');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:LotMaterielExt")->createQueryBuilder('lotMaterielExt')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Collecte', 'collecte', 'WITH', 'lotMaterielExt.collecteFk = collecte.id')
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
            $DateLot = ($entity->getDateCreationLotMaterielExt() !== null) ?  $entity->getDateCreationLotMaterielExt()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // récuparation du premier taxon identifié            
            $query = $em->createQuery('SELECT ei.id, ei.dateIdentification, rt.taxname as taxname, voc.libelle as codeIdentification FROM BbeesE3sBundle:EspeceIdentifiee ei JOIN ei.referentielTaxonFk rt JOIN ei.critereIdentificationVocFk voc WHERE ei.lotMaterielExtFk = '.$id.' ORDER BY ei.id DESC')->getResult(); 
            $lastTaxname = ($query[0]['taxname'] !== NULL) ? $query[0]['taxname'] : NULL;
            $lastdateIdentification = ($query[0]['dateIdentification']  !== NULL) ? $query[0]['dateIdentification']->format('Y-m-d') : NULL;
            $codeIdentification = ($query[0]['codeIdentification'] !== NULL) ? $query[0]['codeIdentification'] : NULL;
            // récuparation de la liste concaténée des personnes  ayant réalisées le lot
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM BbeesE3sBundle:LotMaterielExtEstRealisePar lmerp JOIN lmerp.personneFk p WHERE lmerp.lotMaterielExtFk = '.$id.'')->getResult();            
            $arrayListePersonne = array();
            foreach($query as $taxon) {
                 $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne= implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array("id" => $id, "lotMaterielExt.id" => $id, "lotMaterielExt.codeLotMaterielExt" => $entity->getCodeLotMaterielExt(),
             "listePersonne" => $listePersonne, "collecte.codeCollecte" => $entity->getCollecteFk()->getCodeCollecte(),
             "lotMaterielExt.dateCreationLotMaterielExt" => $DateLot ,"lotMaterielExt.dateCre" => $DateCre, "lotMaterielExt.dateMaj" => $DateMaj,
             "lastTaxname" => $lastTaxname, "lastdateIdentification" => $lastdateIdentification , "codeIdentification" => $codeIdentification ,
             "pays.nomPays" => $entity->getCollecteFk()->getStationFk()->getpaysFk()->getNomPays(),
             "commune.codeCommune" => $entity->getCollecteFk()->getStationFk()->getCommuneFk()->getCodeCommune(),);
        }    
 
        // Reponse Ajax
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "searchPhrase" => $searchPhrase,
            "total"    => $nb // total data array				
            ) ) );
        // Si il s’agit d’un SUBMIT via une requete Ajax : renvoie le contenu au format json
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }

    
    /**
     * Creates a new lotMaterielExt entity.
     *
     * @Route("/new", name="lotmaterielext_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lotMaterielExt = new Lotmaterielext();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtType', $lotMaterielExt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotMaterielExt);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmaterielext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('lotmaterielext_edit', array('id' => $lotMaterielExt->getId(), 'valid' => 1));                       
        }

        return $this->render('lotmaterielext/edit.html.twig', array(
            'lotMaterielExt' => $lotMaterielExt,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotMaterielExt entity.
     *
     * @Route("/{id}", name="lotmaterielext_show")
     * @Method("GET")
     */
    public function showAction(LotMaterielExt $lotMaterielExt)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielExt);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtType', $lotMaterielExt);

        return $this->render('show.html.twig', array(
            'lotMaterielExt' => $lotMaterielExt,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing lotMaterielExt entity.
     *
     * @Route("/{id}/edit", name="lotmaterielext_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LotMaterielExt $lotMaterielExt)
    {
        // recuperation du service generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');
        
        // memorisation des ArrayCollection        
        $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$lotMaterielExt);
        $lotMaterielExtEstReferenceDanss = $service->setArrayCollection('LotMaterielExtEstReferenceDanss',$lotMaterielExt);
        $lotMaterielExtEstRealisePars = $service->setArrayCollection('LotMaterielExtEstRealisePars',$lotMaterielExt);
        
        //
        $deleteForm = $this->createDeleteForm($lotMaterielExt);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtType', $lotMaterielExt);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // suppression des ArrayCollection 
            $service->DelArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$lotMaterielExt, $especeIdentifiees);
            $service->DelArrayCollection('LotMaterielExtEstReferenceDanss',$lotMaterielExt, $lotMaterielExtEstReferenceDanss);
            $service->DelArrayCollection('LotMaterielExtEstRealisePars',$lotMaterielExt, $lotMaterielExtEstRealisePars);
            // flush
            $this->getDoctrine()->getManager()->persist($lotMaterielExt);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmaterielext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            //return $this->redirectToRoute('lotmateriel_edit', array('id' => $lotMateriel->getId()));
            // return $this->redirectToRoute('lotmateriel_index');
           return $this->redirectToRoute('lotmaterielext_edit', array('id' => $lotMaterielExt->getId(), 'valid' => 1)); 

        }

        return $this->render('lotmaterielext/edit.html.twig', array(
            'lotMaterielExt' => $lotMaterielExt,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotMaterielExt entity.
     *
     * @Route("/{id}", name="lotmaterielext_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LotMaterielExt $lotMaterielExt)
    {
        $form = $this->createDeleteForm($lotMaterielExt);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($lotMaterielExt);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('lotmaterielext/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('lotmaterielext_index');
    }

    /**
     * Creates a form to delete a lotMaterielExt entity.
     *
     * @param LotMaterielExt $lotMaterielExt The lotMaterielExt entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotMaterielExt $lotMaterielExt)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotmaterielext_delete', array('id' => $lotMaterielExt->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
