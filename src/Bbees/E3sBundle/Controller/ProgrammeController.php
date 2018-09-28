<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Programme;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Programme controller.
 *
 * @Route("programme")
 * @Security("has_role('ROLE_INVITED')")
 */
class ProgrammeController extends Controller
{
    /**
     * Lists all programme entities.
     *
     * @Route("/", name="programme_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $programmes = $em->getRepository('BbeesE3sBundle:Programme')->findAll();

        return $this->render('programme/index.html.twig', array(
            'programmes' => $programmes,
        ));
    }   

    
    /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="programme_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // recuperation des services
        $service = $this->get('bbees_e3s.generic_function_e3s');         
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('programme.dateMaj' => 'desc', 'programme.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(programme.codeProgramme) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Programme")->createQueryBuilder('programme')
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
            $tab_toshow[] = array("id" => $id, "programme.id" => $id, 
             "programme.codeProgramme" => $entity->getCodeProgramme(),
             "programme.typeFinanceur" => $entity->getTypeFinanceur(),
             "programme.nomProgramme" => $entity->getNomProgramme(),
             "programme.nomsResponsables" => $entity->getNomsResponsables(),
             "programme.anneeDebut" => $entity->getAnneeDebut(),
             "programme.anneeFin" => $entity->getAnneeFin(),
             "programme.dateCre" => $DateCre, "programme.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "programme.userCre" => $service->GetUserCreUsername($entity) ,"programme.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new programme entity.
     *
     * @Route("/new", name="programme_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function newAction(Request $request)
    {
        $programme = new Programme();
        $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            try {
                $flush = $em->flush();
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('programme_edit', array('id' => $programme->getId(), 'valid' => 1)); 
        }

       return $this->render('programme/edit.html.twig', array(
            'programme' => $programme,
            'edit_form' => $form->createView(),
        ));
                
    }


    /**
     * Creates a new programme entity for modal windows
     *
     * @Route("/newmodal", name="programme_newmodal")
     * @Method({"GET", "POST"})
     */
    public function newmodalAction(Request $request)
    {
        $programme = new Programme();
        $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // flush des données du formulaire
            $em = $this->getDoctrine()->getManager();
            $em->persist($programme);
            
            try {
                $flush = $em->flush();
                // mémorise le id et le name du Site créé
                $select_id = $programme->getId();
                $select_name = $programme->getCodeProgramme();
                // recree une entité Site vide
                $programme_new = new Programme();
                $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType',$programme_new);           
                //renvoie un formulaire vide et les paramètres du nouvel enregistrement créé
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'programme', 'form' => $form->createView()))->getContent(),
                    'select_id' => $select_id,
                    'select_name' => $select_name,
                    'exception_message' => "",
                    'entityname' => 'programme',
                    ) ) );	
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message = strval($e);
                // recree une entité Site vide
                $programme_new = new Programme();
                $form = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType',$programme_new);   
                //renvoie un formulaire avec le message d'erreur 
                $response = new Response ();
                $response->setContent ( json_encode ( array (
                    'html_form' => $this->render('modal.html.twig', array('entityname' => 'programme', 'form' => $form->createView()))->getContent(),
                    'select_id' => 0,
                    'select_name' => "",
                    'exception_message' => $exception_message,
                    'entityname' => 'programme',
                    ) ) );	
                }   
            
            //var_dump($select_id); var_dump($select_name);  var_dump($response); exit;
            If ($request->isXmlHttpRequest()){
                // Si il s’agit d’un SUBMIT via une requete Ajax : renvoie le contenu au format json
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                var_dump("l appel a la fonction newmodalAction du controller ProgrammeController n est pas de type XmlHttpRequest"); exit;
            }
        }

        return $this->render('modal.html.twig', array(
            'entityname' => 'programme',
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a programme entity.
     *
     * @Route("/{id}", name="programme_show")
     * @Method("GET")
     */
    public function showAction(Programme $programme)
    {
        $deleteForm = $this->createDeleteForm($programme);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);

        return $this->render('show.html.twig', array(
            'programme' => $programme,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing programme entity.
     *
     * @Route("/{id}/edit", name="programme_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function editAction(Request $request, Programme $programme)
    {
        //
        $deleteForm = $this->createDeleteForm($programme);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ProgrammeType', $programme);
        $editForm->handleRequest($request);
        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('programme/edit.html.twig', array(
                'programme' => $programme,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('programme/edit.html.twig', array(
            'programme' => $programme,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a programme entity.
     *
     * @Route("/{id}", name="programme_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_PROJECT')")
     */
    public function deleteAction(Request $request, Programme $programme)
    {
        $form = $this->createDeleteForm($programme);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($programme);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('programme/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('programme_index');
    }

    /**
     * Creates a form to delete a programme entity.
     *
     * @param Programme $programme The programme entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Programme $programme)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('programme_delete', array('id' => $programme->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
