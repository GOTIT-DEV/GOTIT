<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Personne;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Personne controller.
 *
 * @Route("personne")
 * @Security("has_role('ROLE_INVITED')")
 */
class PersonneController extends Controller
{
    /**
     * Lists all personne entities.
     *
     * @Route("/", name="personne_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $personnes = $em->getRepository('BbeesE3sBundle:Personne')->findAll();

        return $this->render('personne/index.html.twig', array(
            'personnes' => $personnes,
        ));
    }

    
    /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="personne_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // recuperation des services
        $service = $this->get('bbees_e3s.generic_function_e3s');         
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('personne.dateMaj' => 'desc', 'personne.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(personne.nomPersonne) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Personne")->createQueryBuilder('personne')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Etablissement', 'etablissement', 'WITH', 'personne.etablissementFk = etablissement.id')
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
            $NomEtablissement = ($entity->getEtablissementFk() !== null) ?  $entity->getEtablissementFk()->getNomEtablissement() : null;
            //
            $tab_toshow[] = array("id" => $id, "personne.id" => $id, 
             "personne.nomPersonne" => $entity->getNomPersonne(),
             "personne.nomComplet" => $entity->getNomComplet(),
             "etablissement.nomEtablissement" => $NomEtablissement,
             "personne.dateCre" => $DateCre, "personne.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "personne.userCre" => $service->GetUserCreUsername($entity) ,"personne.userMaj" => $service->GetUserMajUsername($entity),
                );
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
     * Creates a new personne entity.
     *
     * @Route("/new", name="personne_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $personne = new Personne();
        $form = $this->createForm('Bbees\E3sBundle\Form\PersonneType', $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($personne);
            try {
                $flush = $em->flush();
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message = strval($e);
                return $this->render('error.delete.html.twig', array('error' => $exception_message, 'route' => 'personne_new'));
            } 
            return $this->redirectToRoute('personne_edit', array('id' => $personne->getId(), 'valid' => 1));  
            //return $this->redirectToRoute('personne_show', array('id' => $personne->getId()));
        }

        return $this->render('personne/edit.html.twig', array(
            'personne' => $personne,
            //'form' => $form->createView(),
            'edit_form' => $form->createView(),
        ));
    }
    
    /**
     * Creates a new personne entity for modal windows
     *
     * @Route("/newmodal", name="personne_newmodal")
     * @Method({"GET", "POST"})
     */
    public function newmodalAction(Request $request)
    {
        $personne = new Personne();
        $form = $this->createForm('Bbees\E3sBundle\Form\PersonneType', $personne);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // flush des données du formulaire
            $em = $this->getDoctrine()->getManager();
            $em->persist($personne);
            
            try {
                $flush = $em->flush();
                // mémorise le id et le name du Site créé
                $select_id = $personne->getId();
                $select_name = $personne->getNomPersonne();
                // recree une entité vide
                $personne_new = new Personne();
                $form = $this->createForm('Bbees\E3sBundle\Form\PersonneType',$personne_new);           
                //renvoie un formulaire vide et les paramètres du nouvel enregistrement créé
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'personne', 'form' => $form->createView()))->getContent(),
                    'select_id' => $select_id,
                    'select_name' => $select_name,
                    'exception_message' => "",
                    'entityname' => 'personne',
                    ) ) );	
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message = strval($e);
                // recree une entité Site vide
                $personne_new = new Personne();
                $form = $this->createForm('Bbees\E3sBundle\Form\PersonneType',$personne_new);   
                //renvoie un formulaire avec le message d'erreur 
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig',array('entityname' => 'personne', 'form' => $form->createView()))->getContent(),
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
                var_dump("l appel a la fonction newmodalAction du controller PersonneController n est pas de type XmlHttpRequest"); exit;
            }
        }

        return $this->render('modal.html.twig', array(
            'entityname' => 'personne',
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a personne entity.
     *
     * @Route("/{id}", name="personne_show")
     * @Method("GET")
     */
    public function showAction(Personne $personne)
    {
        $deleteForm = $this->createDeleteForm($personne);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PersonneType', $personne);

        return $this->render('show.html.twig', array(
            'personne' => $personne,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing personne entity.
     *
     * @Route("/{id}/edit", name="personne_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Personne $personne)
    {
        $deleteForm = $this->createDeleteForm($personne);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PersonneType', $personne);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('personne/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('personne/edit.html.twig', array(
                'personne' => $personne,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('personne/edit.html.twig', array(
            'personne' => $personne,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a personne entity.
     *
     * @Route("/{id}", name="personne_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Personne $personne)
    {
        $form = $this->createDeleteForm($personne);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($personne);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('personne/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('personne_index');
    }

    /**
     * Creates a form to delete a personne entity.
     *
     * @param Personne $personne The personne entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Personne $personne)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('personne_delete', array('id' => $personne->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
