<?php

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

/**
 * Boite controller.
 *
 * @Route("boite")
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
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="boite_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
       
        $em = $this->getDoctrine()->getManager();
        
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('boite.dateMaj' => 'desc', 'boite.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(boite.codeBoite) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('typeBoite') !== null && $request->get('typeBoite') !== '' ) {
            $where .= " AND vocTypeBoite.code LIKE '".$request->get('typeBoite')."'";
        }
        // Recherche de la liste des lots à montrer EstAligneEtTraite
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
             "boite.libelleCollection" => $entity->getLibelleCollection(),
             "boite.dateCre" => $DateCre, "boite.dateMaj" => $DateMaj,  );
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
     * Creates a new boite entity.
     *
     * @Route("/new", name="boite_new")
     * @Method({"GET", "POST"})
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
     */
    public function editAction(Request $request, Boite $boite)
    {
        // recuperation  de l'Entity Mananger
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
