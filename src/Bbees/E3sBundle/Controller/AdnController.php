<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Adn;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Adn controller.
 *
 * @Route("adn")
 * @Security("has_role('ROLE_INVITED')")
 */
class AdnController extends Controller
{
    /**
     * Lists all adn entities.
     *
     * @Route("/", name="adn_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adns = $em->getRepository('BbeesE3sBundle:Adn')->findAll();

        return $this->render('adn/index.html.twig', array(
            'adns' => $adns,
        ));
    }

    /**
     * Retourne au format json un ensemble de champs à afficher tab_collecte_toshow avec les critères suivant :  
     * a) 1 critère de recherche ($request->get('searchPhrase')) insensible à la casse appliqué à un champ (ex. codeCollecte)
     * b) le nombre de lignes à afficher ($request->get('rowCount'))
     * c) 1 critère de tri sur un collone  ($request->get('sort'))
     *
     * @Route("/indexjson", name="adn_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // recuperation des services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('adn.dateMaj' => 'desc', 'adn.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initialise la variable searchPhrase suivant les cas et définit la condition du where suivant les conditions sur le parametre d'url idFk
        $where = 'LOWER(adn.codeAdn) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND adn.individuFk = '.$request->get('idFk');
        }
        // Recherche de la liste des lots à montrer
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:Adn")->createQueryBuilder('adn')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
            ->leftJoin('BbeesE3sBundle:Boite', 'boite', 'WITH', 'adn.boiteFk = boite.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        $lastTaxname = '';
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateAdn = ($entity->getDateAdn() !== null) ?  $entity->getDateAdn()->format('Y-m-d') : null;
            $codeBoite = ($entity->getBoiteFk() !== null) ?  $entity->getBoiteFk()->getCodeBoite() : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // recherche du nombre d individu pour le lot  id 
            $query = $em->createQuery('SELECT pcr.id FROM BbeesE3sBundle:Pcr pcr WHERE pcr.adnFk = '.$id.'')->getResult();
            $linkPcr= (count($query) > 0) ? $id : '';
            // récuparation de la liste concaténée des personnes  ayant réalisées le lot
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM BbeesE3sBundle:AdnEstRealisePar erp JOIN erp.personneFk p WHERE erp.adnFk = '.$id.'')->getResult();            
            $arrayListePersonne = array();
            foreach($query as $taxon) {
                 $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne= implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array("id" => $id, "adn.id" => $id, 
             "individu.codeIndBiomol" => $entity->getIndividuFk()->getCodeIndBiomol(),
             "adn.codeAdn" => $entity->getCodeAdn(),
             "listePersonne" => $listePersonne, 
             "adn.dateAdn" => $DateAdn ,
             "boite.codeBoite" => $codeBoite,
             "adn.dateCre" => $DateCre, "adn.dateMaj" => $DateMaj,
             "userCreId" => $service->GetUserCreId($entity), "adn.userCre" => $service->GetUserCreUsername($entity) ,"adn.userMaj" => $service->GetUserMajUsername($entity),
             "linkPcr" => $linkPcr,);
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
     * Creates a new adn entity.
     *
     * @Route("/new", name="adn_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $adn = new Adn();
        $form = $this->createForm('Bbees\E3sBundle\Form\AdnType', $adn);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($adn);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('adn_edit', array('id' => $adn->getId(), 'valid' => 1));                        
        }

        return $this->render('adn/edit.html.twig', array(
            'adn' => $adn,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a adn entity.
     *
     * @Route("/{id}", name="adn_show")
     * @Method("GET")
     */
    public function showAction(Adn $adn)
    {
        $deleteForm = $this->createDeleteForm($adn);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\AdnType', $adn);

        return $this->render('show.html.twig', array(
            'adn' => $adn,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing adn entity.
     *
     * @Route("/{id}/edit", name="adn_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Adn $adn)
    {
        // recuperation du service generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        // memorisation des ArrayCollection        
        $adnEstRealisePars = $service->setArrayCollection('AdnEstRealisePars',$adn);
        //
        $deleteForm = $this->createDeleteForm($adn);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\AdnType', $adn);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // suppression des ArrayCollection 
            $service->DelArrayCollection('AdnEstRealisePars',$adn, $adnEstRealisePars);
            // flush
            $this->getDoctrine()->getManager()->persist($adn);                       
            try {
                $this->getDoctrine()->getManager()->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            //return $this->redirectToRoute('lotmateriel_edit', array('id' => $lotMateriel->getId()));
            // return $this->redirectToRoute('lotmateriel_index');
            return $this->render('adn/edit.html.twig', array(
                'adn' => $adn,
                'edit_form' => $editForm->createView(),
                'valid' => 1));
        }
        
        return $this->render('adn/edit.html.twig', array(
            'adn' => $adn,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a adn entity.
     *
     * @Route("/{id}", name="adn_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, Adn $adn)
    {
        $form = $this->createDeleteForm($adn);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($adn);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('adn_index');
    }

    /**
     * Creates a form to delete a adn entity.
     *
     * @param Adn $adn The adn entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Adn $adn)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adn_delete', array('id' => $adn->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
