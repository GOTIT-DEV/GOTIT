<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Motu;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Motu controller.
 *
 * @Route("motu")
 * @Security("has_role('ROLE_INVITED')")
 */
class MotuController extends Controller
{
    /**
     * Lists all motu entities.
     *
     * @Route("/", name="motu_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $motus = $em->getRepository('BbeesE3sBundle:Motu')->findAll();

        return $this->render('motu/index.html.twig', array(
            'motus' => $motus,
        ));
    }

        
    /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="motu_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // recuperation des services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('motu.dateMaj' => 'desc', 'motu.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(motu.libelleMotu) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Motu")->createQueryBuilder('motu')
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
            $DateMotu = ($entity->getDateMotu() !== null) ?  $entity->getDateMotu()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // récuparation de la liste concaténée des personnes  ayant réalisées le lot
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM BbeesE3sBundle:MotuEstGenerePar megp JOIN megp.personneFk p WHERE megp.motuFk = '.$id.'')->getResult();            
            $arrayListePersonne = array();
            foreach($query as $taxon) {
                 $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne= implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array("id" => $id, "motu.id" => $id, 
             "motu.libelleMotu" => $entity->getLibelleMotu(),
             "motu.nomFichierCsv" => $entity->getNomFichierCsv(),
             "listePersonne" => $listePersonne, 
             "motu.commentaireMotu" => $entity->getCommentaireMotu(),
             "motu.dateMotu" => $DateMotu ,
             "motu.dateCre" => $DateCre, "motu.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "motu.userCre" => $service->GetUserCreUsername($entity) ,"motu.userMaj" => $service->GetUserMajUsername($entity),
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
     * Creates a new motu entity.
     *
     * @Route("/new", name="motu_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $motu = new Motu();
        $form = $this->createForm('Bbees\E3sBundle\Form\MotuType', $motu);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($motu);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('motu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('motu_edit', array('id' => $motu->getId(), 'valid' => 1));                       
        }

        return $this->render('motu/edit.html.twig', array(
            'motu' => $motu,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a motu entity.
     *
     * @Route("/{id}", name="motu_show")
     * @Method("GET")
     */
    public function showAction(Motu $motu)
    {
        $deleteForm = $this->createDeleteForm($motu);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\MotuType', $motu);
        
        return $this->render('show.html.twig', array(
            'motu' => $motu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing motu entity.
     *
     * @Route("/{id}/edit", name="motu_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Motu $motu)
    {        
        // recuperation du service generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');
        
        // memorisation des ArrayCollection        
        $motuEstGenerePars = $service->setArrayCollection('MotuEstGenerePars',$motu);
        
        // 
        $deleteForm = $this->createDeleteForm($motu);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\MotuType', $motu);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // suppression des ArrayCollection 
            $service->DelArrayCollection('MotuEstGenerePars',$motu, $motuEstGenerePars);
            // flush
            $this->getDoctrine()->getManager()->persist($motu);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('motu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->render('motu/edit.html.twig', array(
                'motu' => $motu,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        return $this->render('motu/edit.html.twig', array(
        'motu' => $motu,
        'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a motu entity.
     *
     * @Route("/{id}", name="motu_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Motu $motu)
    {
        $form = $this->createDeleteForm($motu);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($motu);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('motu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('motu_index');
    }

    /**
     * Creates a form to delete a motu entity.
     *
     * @param Motu $motu The motu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Motu $motu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('motu_delete', array('id' => $motu->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
