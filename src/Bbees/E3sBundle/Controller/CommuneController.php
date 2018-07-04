<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Commune;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Commune controller.
 *
 * @Route("commune")
 */
class CommuneController extends Controller
{
    /**
     * Lists all commune entities.
     *
     * @Route("/", name="commune_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $communes = $em->getRepository('BbeesE3sBundle:Commune')->findAll();

        return $this->render('commune/index.html.twig', array(
            'communes' => $communes,
        ));
    }
    
     /**
     * Retourne au format json un ensemble de champs à afficher tab_station_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="commune_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();

        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('commune.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(commune.codeCommune) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Commune")->createQueryBuilder('commune')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Pays', 'pays', 'WITH', 'commune.paysFk = pays.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);          
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id, "commune.id" => $id, 
                "commune.codeCommune" => $entity->getCodeCommune(),
                "commune.nomCommune" => $entity->getNomCommune(),
                "commune.nomRegion" => $entity->getNomRegion(),
                "pays.codePays" => $entity->getPaysFk()->getCodePays(),
                "commune.dateCre" => $DateCre, "commune.dateMaj" => $DateMaj,
             );
        }                
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "total"    => $nb // total data array				
            ) ) );
        // Si il s’agit d’un SUBMIT via une requete Ajax : renvoie le contenu au format json
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    }
    
    /**
     * Creates a new commune entity.
     *
     * @Route("/new", name="commune_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $commune = new Commune();
        $form = $this->createForm('Bbees\E3sBundle\Form\CommuneType', $commune);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {           
            $em = $this->getDoctrine()->getManager();
            $em->persist($commune);
            try {
                $flush = $em->flush();
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('commune/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }  
            return $this->redirectToRoute('commune_edit', array('id' => $commune->getId(), 'valid' => 1));              
        }

        return $this->render('commune/edit.html.twig', array(
            'commune' => $commune,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new commune entity for modal windows
     *
     * @Route("/newmodal", name="commune_newmodal")
     * @Method({"GET", "POST"})
     */
    public function newmodalAction(Request $request, $id_pays = null)
    {
        $commune = new Commune();
        $form = $this->createForm('Bbees\E3sBundle\Form\CommuneType', $commune, array('id_pays' => $id_pays,));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // flush des données du formulaire
            $em = $this->getDoctrine()->getManager();
            $em->persist($commune);
            
            try {
                $flush = $em->flush();
                // mémorise le id et le name du Site créé
                $select_id = $commune->getId();
                $select_name = $commune->getCodeCommune();
                // recree une entité Site vide
                $commune_new = new Commune();
                $form = $this->createForm('Bbees\E3sBundle\Form\CommuneType',$commune_new, array('id_pays' => $id_pays,));           
                //renvoie un formulaire vide et les paramètres du nouvel enregistrement créé
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'commune', 'form' => $form->createView()))->getContent(),
                    'select_id' => $select_id,
                    'select_name' => $select_name,
                    'exception_message' => "",
                    'entityname' => 'personne',
                    ) ) );	
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message = strval($e);
                // recree une entité Site vide
                $commune_new = new Commune();
                $form = $this->createForm('Bbees\E3sBundle\Form\CommuneType',$commune_new, array('id_pays' => $id_pays,));   
                //renvoie un formulaire avec le message d'erreur 
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'commune', 'form' => $form->createView()))->getContent(),
                    'select_id' => 0,
                    'select_name' => "",
                    'exception_message' => $exception_message,
                    'entityname' => 'personne',
                    ) ) );	
                }   
            
            //var_dump($select_id); var_dump($select_name);  var_dump($response); exit;
            If ($request->isXmlHttpRequest()){
                // Si il s’agit d’un SUBMIT via une requete Ajax : renvoie le contenu au format json
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                var_dump("l appel a la fonction newmodalAction du controller CommuneController n est pas de type XmlHttpRequest"); exit;
            }
        }

        return $this->render('modal.html.twig', array(
            'entityname' => 'commune',
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a commune entity.
     *
     * @Route("/{id}", name="commune_show")
     * @Method("GET")
     */
    public function showAction(Commune $commune)
    {
        $deleteForm = $this->createDeleteForm($commune);
        
        $editForm = $this->createForm('Bbees\E3sBundle\Form\CommuneType', $commune);
        return $this->render('show.html.twig', array(
            'commune' => $commune,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing commune entity.
     *
     * @Route("/{id}/edit", name="commune_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Commune $commune)
    {
        $deleteForm = $this->createDeleteForm($commune);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\CommuneType', $commune);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {            
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('commune/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('commune/edit.html.twig', array(
            'commune' => $commune,
            'edit_form' => $editForm->createView(),
            'valid' => 1,
            ));
        }

        return $this->render('commune/edit.html.twig', array(
            'commune' => $commune,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a commune entity.
     *
     * @Route("/{id}", name="commune_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Commune $commune)
    {
        $form = $this->createDeleteForm($commune);
        $form->handleRequest($request);
       
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($commune);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('commune/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
        }

        return $this->redirectToRoute('commune_index');
    }

    /**
     * Creates a form to delete a commune entity.
     *
     * @param Commune $commune The commune entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commune $commune)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commune_delete', array('id' => $commune->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
