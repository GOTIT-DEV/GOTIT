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

use App\Entity\Individu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Core\GenericFunctionE3s;
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
    const MAX_RESULTS_TYPEAHEAD   = 20;
    
    /**
     * Lists all individu entities.
     *
     * @Route("/", name="individu_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $individus = $em->getRepository('App:Individu')->findAll();

        return $this->render('individu/index.html.twig', array(
            'individus' => $individus,
        ));
    }
    
    /**
     * @Route("/search/{q}", requirements={"q"=".+"}, name="individu_search")
     */
    public function searchAction($q)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('ind.id, ind.codeIndBiomol as code')
            ->from('App:Individu', 'ind');
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $and = [];
        for($i=0; $i<count($query); $i++) {
            $and[] = '(LOWER(ind.codeIndBiomol) like :q'.$i.')';
        }
        $qb->where(implode(' and ', $and));
        for($i=0; $i<count($query); $i++) {
            $qb->setParameter('q'.$i, $query[$i].'%');
        }
        $qb->addOrderBy('code', 'ASC');
        $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
        $results = $qb->getQuery()->getResult();       
        // Ajax answer
        return $this->json(
            $results
        );
    }
    
    /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="individu_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service)
    {
        // load Doctrine Manager       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? array_keys($request->get('sort'))[0]." ".array_values($request->get('sort'))[0] : "sp.date_of_update DESC, sp.id DESC";  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = ' LOWER(sp.specimen_morphological_code) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ( $request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND sp.internal_biological_material_fk = '.$request->get('idFk');
        }
        
        // Search for the list to show
        $tab_toshow =[];
        $rawSql = "SELECT  sp.id, st.site_code, st.latitude, st.longitude, sampling.sample_code, country.country_name, municipality.municipality_code, st.site_code,
        sp.specimen_molecular_code, sp.specimen_morphological_code, sp.specimen_molecular_number, sp.tube_code, sp.date_of_creation, sp.date_of_update,
        rt_sp.taxon_name as last_taxname_sp, ei_sp.identification_date as last_date_identification_sp, voc_sp_identification_criterion.code as code_sp_identification_criterion,
        voc_sp_specimen_type.code as voc_sp_specimen_type_code, sp.creation_user_name, user_cre.username as user_cre_username , user_maj.username as user_maj_username,
        string_agg(cast( dna.id as character varying) , ' ;') as list_dna, string_agg(cast( specimen_slide.id as character varying) , ' ;') as list_specimen_slide
	FROM  specimen sp
                LEFT JOIN user_db user_cre ON user_cre.id = sp.creation_user_name
                LEFT JOIN user_db user_maj ON user_maj.id = sp.update_user_name               
                JOIN internal_biological_material lot ON sp.internal_biological_material_fk = lot.id
		JOIN sampling ON sampling.id = lot.sampling_fk
			JOIN site st ON st.id = sampling.site_fk
                        LEFT JOIN country ON st.country_fk = country.id
                        LEFT JOIN municipality ON st.municipality_fk = municipality.id 
                LEFT JOIN vocabulary voc_sp_specimen_type ON sp.specimen_type_voc_fk = voc_sp_specimen_type.id
		LEFT JOIN identified_species ei_sp ON ei_sp.specimen_fk = sp.id
			LEFT JOIN (SELECT MAX(ei_spi.id) AS maxei_spi 
				FROM identified_species ei_spi 
				GROUP BY ei_spi.specimen_fk) ei_sp2 ON (ei_sp.id = ei_sp2.maxei_spi)
			LEFT JOIN taxon rt_sp ON ei_sp.taxon_fk = rt_sp.id
                        LEFT JOIN vocabulary voc_sp_identification_criterion ON ei_sp.identification_criterion_voc_fk = voc_sp_identification_criterion.id
		LEFT JOIN dna ON dna.specimen_fk = sp.id
                LEFT JOIN specimen_slide ON specimen_slide.specimen_fk = sp.id"
        ." WHERE ".$where." 
        GROUP BY sp.id, st.site_code, st.latitude, st.longitude, sampling.sample_code, country.country_name, municipality.municipality_code, st.site_code,
        sp.specimen_molecular_code, sp.specimen_morphological_code, sp.specimen_molecular_number, sp.tube_code, sp.date_of_creation, sp.date_of_update,
        rt_sp.taxon_name, ei_sp.identification_date, voc_sp_identification_criterion.code,
        voc_sp_specimen_type.code, sp.creation_user_name, user_cre.username , user_maj.username" 
        ." ORDER BY ".$orderBy;
        // execute query and fill tab to show in the bootgrid list (see index.htm)
        $stmt = $em->getConnection()->prepare($rawSql);
        $stmt->bindValue('criteriaLower', strtolower($searchPhrase).'%');
        $stmt->execute();
        $entities_toshow = $stmt->fetchAll();
        $nb = count($entities_toshow);
        $entities_toshow = ($request->get('rowCount') > 0 ) ? array_slice($entities_toshow, $minRecord, $rowCount) : array_slice($entities_toshow, $minRecord);

        foreach($entities_toshow as $key => $val){
             $linkAdn = ($val['list_dna'] !== null) ? strval($val['id']) : '';
             $linkIndividulame = ($val['list_specimen_slide'] !== null) ? strval($val['id']) : '';             
             $tab_toshow[] = array("id" => $val['id'], "sp.id" => $val['id'],
                "st.site_code" => $val['site_code'],
                 "sp.specimen_molecular_code" => $val['specimen_molecular_code'],                
                 "sp.specimen_morphological_code" => $val['specimen_morphological_code'],                 
                 "voc_sp_specimen_type.code" => $val['voc_sp_specimen_type_code'],
                 "sp.specimen_molecular_number" => $val['specimen_molecular_number'],
                 "sp.tube_code" => $val['tube_code'],
                "last_taxname_sp" => $val['last_taxname_sp'], 
                 "last_date_identification_sp" => $val['last_date_identification_sp'],  
                 "code_sp_identification_criterion" => $val['code_sp_identification_criterion'],
                 "sp.date_of_creation" => $val['date_of_creation'], "sp.date_of_update" => $val['date_of_update'],
                "creation_user_name" => $val['creation_user_name'], "user_cre.username" => $val['user_cre_username'] ,"user_maj.username" => $val['user_maj_username'],
                "linkAdn" => $linkAdn, "linkIndividulame" => $linkIndividulame
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
     * Creates a new individu entity.
     *
     * @Route("/new", name="individu_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $individu = new Individu();
        $em = $this->getDoctrine()->getManager();
        // check if the relational Entity (LotMateriel) is given and set the RelationalEntityFk for the new Entity
        if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
            $RelEntityId = $request->get('idFk');
            $RelEntity = $em->getRepository('App:LotMateriel')->find($RelEntityId);
            $individu->setLotMaterielFk($RelEntity);
        } 
        $form = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // (i) load the id  the relational Entity (LotMateriel) from typeahead input field and (ii) set the foreign key 
            $RelEntityId = $form->get('lotmaterielId');
            $RelEntity = $em->getRepository('App:LotMateriel')->find($RelEntityId->getData());
            $individu->setLotMaterielFk($RelEntity);
            // persist Entity
            $em->persist($individu);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('individu/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            return $this->redirectToRoute('individu_edit', array('id' => $individu->getId(), 'valid' => 1, 'idFk' => $request->get('idFk') ));                       
        }

        return $this->render('individu/edit.html.twig', array(
            'individu' => $individu,
            'edit_form' => $form->createView(),
        ));

    }

    /**
     * Finds and displays a individu entity.
     *
     * @Route("/{id}", name="individu_show", methods={"GET"})
     */
    public function showAction(Individu $individu)
    {
        $deleteForm = $this->createDeleteForm($individu);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\IndividuType', $individu);

        return $this->render('individu/edit.html.twig', array(
            'individu' => $individu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * Displays a form to edit an existing individu entity.
     *
     * @Route("/{id}/edit", name="individu_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, Individu $individu, GenericFunctionE3s $service)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $individu->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        
        // load service  generic_function_e3s
        //     
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
            // (i) load the id of relational Entity (LotMateriel) from typeahead input field  (ii) set the foreign key
            $em = $this->getDoctrine()->getManager();
            $RelEntityId = $editForm->get('lotmaterielId');;
            $RelEntity = $em->getRepository('App:LotMateriel')->find($RelEntityId->getData());
            $individu->setLotMaterielFk($RelEntity);
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
     * @Route("/{id}", name="individu_delete", methods={"DELETE"})
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
