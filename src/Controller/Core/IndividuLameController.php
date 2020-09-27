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

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Core\GenericFunctionE3s;
use App\Form\Enums\Action;
use App\Entity\IndividuLame;

/**
 * Individulame controller.
 *
 * @Route("individulame")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class IndividuLameController extends AbstractController
{
  /**
   * Lists all individuLame entities.
   *
   * @Route("/", name="individulame_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $individuLames = $em->getRepository('App:IndividuLame')->findAll();

    return $this->render('Core/individulame/index.html.twig', array(
      'individuLames' => $individuLames,
    ));
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="individulame_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service)
  {
    // load Doctrine Manager      
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount')  !== NULL)
      ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort')  !== NULL)
      ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
      : "ss.date_of_update DESC, ss.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(sp.specimen_morphological_code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' &&
      $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
      $where .= ' AND ss.specimen_fk = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT  ss.id, ss.collection_slide_code, ss.photo_folder_name, 
        ss.slide_date, box.box_code, st.site_code, st.latitude, st.longitude, 
        sampling.sample_code, country.country_name, municipality.municipality_code, 
        st.site_code,sp.specimen_molecular_code, sp.specimen_morphological_code, 
        sp.specimen_molecular_number, sp.tube_code, rt_sp.taxon_name as last_taxname_sp, 
        ei_sp.identification_date as last_date_identification_sp, 
        voc_sp_identification_criterion.code as code_sp_identification_criterion,
        voc_sp_specimen_type.code as voc_sp_specimen_type_code, 
        lot.internal_biological_material_code,
        ss.creation_user_name, ss.date_of_creation, ss.date_of_update,
        user_cre.user_name as user_cre_username , user_maj.user_name as user_maj_username      
	FROM specimen_slide ss
                LEFT JOIN user_db user_cre ON user_cre.id = ss.creation_user_name
                LEFT JOIN user_db user_maj ON user_maj.id = ss.update_user_name 
                LEFT JOIN storage_box box ON box.id = ss.storage_box_fk 
                JOIN specimen sp ON ss.specimen_fk = sp.id
                JOIN internal_biological_material lot 
                    ON sp.internal_biological_material_fk = lot.id
		JOIN sampling ON sampling.id = lot.sampling_fk
			JOIN site st ON st.id = sampling.site_fk
                        LEFT JOIN country ON st.country_fk = country.id
                        LEFT JOIN municipality ON st.municipality_fk = municipality.id 
                LEFT JOIN vocabulary voc_sp_specimen_type 
                    ON sp.specimen_type_voc_fk = voc_sp_specimen_type.id
		LEFT JOIN identified_species ei_sp ON ei_sp.specimen_fk = sp.id
			INNER JOIN (SELECT MAX(ei_spi.id) AS maxei_spi 
				FROM identified_species ei_spi 
				GROUP BY ei_spi.specimen_fk) ei_sp2 ON (ei_sp.id = ei_sp2.maxei_spi)
			LEFT JOIN taxon rt_sp ON ei_sp.taxon_fk = rt_sp.id
                        LEFT JOIN vocabulary voc_sp_identification_criterion 
                            ON ei_sp.identification_criterion_voc_fk = voc_sp_identification_criterion.id
		LEFT JOIN dna ON dna.specimen_fk = sp.id"
      . " WHERE " . $where . " ORDER BY " . $orderBy;
    // execute query and fill tab to show in the bootgrid list (see index.htm)
    $stmt = $em->getConnection()->prepare($rawSql);
    $stmt->bindValue('criteriaLower', strtolower($searchPhrase) . '%');
    $stmt->execute();
    $entities_toshow = $stmt->fetchAll();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
      ? array_slice($entities_toshow, $minRecord, $rowCount)
      : array_slice($entities_toshow, $minRecord);

    foreach ($entities_toshow as $key => $val) {
      $tab_toshow[] = array(
        "id" => $val['id'],
        "ss.id" => $val['id'],
        "ss.collection_slide_code" => $val["collection_slide_code"],
        "ss.photo_folder_name" => $val["photo_folder_name"],
        "ss.slide_date" => $val["slide_date"],
        "box.box_code" => $val["box_code"],
        "lot.internal_biological_material_code" => $val['internal_biological_material_code'],
        "sp.specimen_molecular_code" => $val['specimen_molecular_code'],
        "sp.specimen_morphological_code" => $val['specimen_morphological_code'],
        "voc_sp_specimen_type.code" => $val['voc_sp_specimen_type_code'],
        "sp.specimen_molecular_number" => $val['specimen_molecular_number'],
        "sp.tube_code" => $val['tube_code'],
        "last_taxname_sp" => $val['last_taxname_sp'],
        "last_date_identification_sp" => $val['last_date_identification_sp'],
        "code_sp_identification_criterion" => $val['code_sp_identification_criterion'],
        "ss.date_of_creation" => $val['date_of_creation'],
        "ss.date_of_update" => $val['date_of_update'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_name" => $val['user_cre_username'],
        "user_maj.user_name" => $val['user_maj_username']
      );
    }

    return new JsonResponse([
      "current"    => intval($request->get('current')),
      "rowCount"  => $rowCount,
      "rows"     => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total"    => $nb // total data array				
    ]);
  }

  /**
   * Creates a new individuLame entity.
   *
   * @Route("/new", name="individulame_new", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request)
  {
    $individuLame = new Individulame();
    $em = $this->getDoctrine()->getManager();
    // check if the relational Entity (Individu) is given and set the RelationalEntityFk for the new Entity
    if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
      $RelEntityId = $request->get('idFk');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId);
      $individuLame->setIndividuFk($RelEntity);
    }
    $form = $this->createForm('App\Form\IndividuLameType', $individuLame, [
      'action_type' => Action::create()
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // (i) load the id of relational Entity (Individu) from typeahead input field and (ii) set the foreign key
      $RelEntityId = $form->get('individuId');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId->getData());
      $individuLame->setIndividuFk($RelEntity);
      // persist
      $em->persist($individuLame);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/individulame/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
      }
      return $this->redirectToRoute('individulame_edit', array(
        'id' => $individuLame->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk')
      ));
    }

    return $this->render('Core/individulame/edit.html.twig', array(
      'individuLame' => $individuLame,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a individuLame entity.
   *
   * @Route("/{id}", name="individulame_show", methods={"GET"})
   */
  public function showAction(IndividuLame $individuLame)
  {
    $deleteForm = $this->createDeleteForm($individuLame);
    $editForm = $this->createForm('App\Form\IndividuLameType', $individuLame, [
      'action_type' => Action::show()
    ]);

    return $this->render('Core/individulame/edit.html.twig', array(
      'individuLame' => $individuLame,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing individuLame entity.
   *
   * @Route("/{id}/edit", name="individulame_edit", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function editAction(
    Request $request,
    IndividuLame $individuLame,
    GenericFunctionE3s $service
  ) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() ==  'ROLE_COLLABORATION' &&
      $individuLame->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    // load service  generic_function_e3s
    //        
    // store ArrayCollection       
    $individuLameEstRealisePars = $service->setArrayCollection(
      'IndividuLameEstRealisePars',
      $individuLame
    );
    //
    $deleteForm = $this->createDeleteForm($individuLame);
    $editForm = $this->createForm('App\Form\IndividuLameType', $individuLame, [
      'action_type' => Action::edit()
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection(
        'IndividuLameEstRealisePars',
        $individuLame,
        $individuLameEstRealisePars
      );
      // (i) load the id of relational Entity (Individu) from typeahead input field  (ii) set the foreign key
      $em = $this->getDoctrine()->getManager();
      $RelEntityId = $editForm->get('individuId');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId->getData());
      $individuLame->setIndividuFk($RelEntity);
      // flush
      $this->getDoctrine()->getManager()->persist($individuLame);
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/individulame/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
      }
      return $this->render('Core/individulame/edit.html.twig', array(
        'individuLame' => $individuLame,
        'edit_form' => $editForm->createView(),
        'valid' => 1
      ));
    }

    return $this->render('Core/individulame/edit.html.twig', array(
      'individuLame' => $individuLame,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a individuLame entity.
   *
   * @Route("/{id}", name="individulame_delete", methods={"DELETE"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, IndividuLame $individuLame)
  {
    $form = $this->createDeleteForm($individuLame);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($individuLame);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/individulame/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
      }
    }
    return $this->redirectToRoute('individulame_index');
  }

  /**
   * Creates a form to delete a individuLame entity.
   *
   * @param IndividuLame $individuLame The individuLame entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(IndividuLame $individuLame)
  {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'individulame_delete',
        array('id' => $individuLame->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
