<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Voc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Voc controller.
 *
 * @Route("voc")
 */
class VocController extends Controller
{
    /**
     * Lists all voc entities.
     *
     * @Route("/", name="voc_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $vocs = $em->getRepository('BbeesE3sBundle:Voc')->findAll();

        return $this->render('voc/index.html.twig', array(
            'vocs' => $vocs,
        ));
    }

    
    /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="voc_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
       
        $em = $this->getDoctrine()->getManager();
        
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('voc.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(voc.libelle) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Voc")->createQueryBuilder('voc')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            //
            $tab_toshow[] = array("id" => $id, "voc.id" => $id, 
             "voc.code" => $entity->getCode(),
             "voc.libelle" => $entity->getLibelle(),
             "voc.parent" => $entity->getParent(),
             "voc.dateCre" => $DateCre, "voc.dateMaj" => $DateMaj,);
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
     * Creates a new voc entity.
     *
     * @Route("/new", name="voc_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $voc = new Voc();
        $form = $this->createForm('Bbees\E3sBundle\Form\VocType', $voc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($voc);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('voc/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('voc_edit', array('id' => $voc->getId(), 'valid' => 1));                       
        }

        return $this->render('voc/edit.html.twig', array(
            'voc' => $voc,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a voc entity.
     *
     * @Route("/{id}", name="voc_show")
     * @Method("GET")
     */
    public function showAction(Voc $voc)
    {
        $deleteForm = $this->createDeleteForm($voc);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\VocType', $voc);

        return $this->render('show.html.twig', array(
            'voc' => $voc,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing voc entity.
     *
     * @Route("/{id}/edit", name="voc_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Voc $voc)
    {
        $deleteForm = $this->createDeleteForm($voc);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\VocType', $voc);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('voc/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('voc/edit.html.twig', array(
                'voc' => $voc,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }

        return $this->render('voc/edit.html.twig', array(
            'voc' => $voc,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a voc entity.
     *
     * @Route("/{id}", name="voc_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Voc $voc)
    {
        $form = $this->createDeleteForm($voc);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($voc);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('voc/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('voc_index');
    }

    /**
     * Creates a form to delete a voc entity.
     *
     * @param Voc $voc The voc entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Voc $voc)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('voc_delete', array('id' => $voc->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}