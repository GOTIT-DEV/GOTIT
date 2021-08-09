<?php

namespace App\Controller\Core;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * InternalLot controller.
 *
 * @Route("internal_lot")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalLotController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all internal lot entities.
   *
   * @Route("/", name="internal_lot_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $lots = $em->getRepository('App:InternalLot')->findAll();

    return $this->render(
      'Core/internal_lot/index.html.twig',
      ['internalLots' => $lots]
    );
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="internal_lot_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('lot.id, lot.codeLotMateriel as code')
      ->from('App:InternalLot', 'lot')
      ->addOrderBy('code', 'ASC')
      ->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(lot.codeLotMateriel) like :q' . $i . ')');
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }

    $results = $qb->getQuery()->getResult();
    return $this->json($results);
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="internal_lot_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $em = $this->getDoctrine()->getManager();
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "lot.date_of_update DESC, lot.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $where = ' WHERE LOWER(lot.internal_biological_material_code) LIKE :criteriaLower';

    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }

    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND lot.sampling_fk = ' . $request->get('idFk');
    }
    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT
      lot.id,
      st.site_code, st.latitude, st.longitude,
      sampling.sample_code,
      country.country_name,
      municipality.municipality_code,
      lot.internal_biological_material_status,
      lot.sequencing_advice,
      lot.internal_biological_material_date,
      lot.date_of_creation,
      lot.date_of_update,
      voc_lot_identification_criterion.code as code_lot_identification_criterion,
      lot.internal_biological_material_code,
      rt_lot.taxon_name as last_taxname_lot,
      ei_lot.identification_date as last_date_identification_lot,
      lot.creation_user_name,
      user_cre.user_full_name as user_cre_username,
      user_maj.user_full_name as user_maj_username,
      string_agg(DISTINCT person.person_name , ' ; ') as list_person,
      string_agg(cast( sp.id as character varying) , ' ;') as list_specimen
    FROM internal_biological_material lot
    LEFT JOIN user_db user_cre ON user_cre.id = lot.creation_user_name
    LEFT JOIN user_db user_maj ON user_maj.id = lot.update_user_name
		JOIN sampling ON sampling.id = lot.sampling_fk
    JOIN site st ON st.id = sampling.site_fk
    LEFT JOIN country ON st.country_fk = country.id
    LEFT JOIN municipality ON st.municipality_fk = municipality.id
    LEFT JOIN internal_biological_material_is_treated_by ibmitb
      ON ibmitb.internal_biological_material_fk = lot.id
    LEFT JOIN person ON ibmitb.person_fk = person.id
		LEFT JOIN identified_species ei_lot
      ON ei_lot.internal_biological_material_fk = lot.id
    INNER JOIN (
      SELECT MAX(ei_loti.id) AS maxei_loti
      FROM identified_species ei_loti
      GROUP BY ei_loti.internal_biological_material_fk
    ) ei_lot2
      ON (ei_lot.id = ei_lot2.maxei_loti)
    LEFT JOIN taxon rt_lot ON ei_lot.taxon_fk = rt_lot.id
    LEFT JOIN vocabulary voc_lot_identification_criterion
      ON ei_lot.identification_criterion_voc_fk = voc_lot_identification_criterion.id
		LEFT JOIN specimen sp ON sp.internal_biological_material_fk = lot.id" . $where .
      " GROUP BY
      lot.id, st.site_code, st.latitude, st.longitude,
      sampling.sample_code, country.country_name, municipality.municipality_code,
      lot.internal_biological_material_status, lot.sequencing_advice,
      lot.internal_biological_material_date, lot.date_of_creation,
      lot.date_of_update, voc_lot_identification_criterion.code ,
      lot.internal_biological_material_code,
      rt_lot.taxon_name, ei_lot.identification_date,
      lot.creation_user_name,
      user_cre.user_full_name,
      user_maj.user_full_name"
      . " ORDER BY " . $orderBy;

    // execute query and fill tab to show in the bootgrid list (see index.htm)
    $stmt = $em->getConnection()->prepare($rawSql);
    $stmt->bindValue('criteriaLower', strtolower($searchPhrase) . '%');
    $stmt->execute();
    $entities_toshow = $stmt->fetchAll();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);

    // (count($query) > 0) ? $query[0]['name'] : 'NA'

    foreach ($entities_toshow as $key => $val) {
      $linkSpecimen = ($val['list_specimen'] !== null)
      ? strval($val['id']) : '';
      $tab_toshow[] = array(
        "id" => $val['id'],
        "lot.id" => $val['id'],
        "lot.internal_biological_material_code" => $val['internal_biological_material_code'],
        "lot.internal_biological_material_status" => $val['internal_biological_material_status'],
        "last_taxname_lot" => $val['last_taxname_lot'],
        "last_date_identification_lot" => $val['last_date_identification_lot'],
        "code_lot_identification_criterion" => $val['code_lot_identification_criterion'],
        "lot.sequencing_advice" => $val['sequencing_advice'],
        "lot.internal_biological_material_date" => $val['internal_biological_material_date'],
        "lot.date_of_creation" => $val['date_of_creation'],
        "lot.date_of_update" => $val['date_of_update'],
        "list_person" => $val['list_person'],
        "sampling.sample_code" => $val['sample_code'],
        "country.country_name" => $val['country_name'],
        "municipality.municipality_code" => $val['municipality_code'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_full_name" => ($val['user_cre_username'] != null) ? $val['user_cre_username'] : 'NA',
        "user_maj.user_full_name" => ($val['user_maj_username'] != null) ? $val['user_maj_username'] : 'NA',
        "linkSpecimen" => $linkSpecimen,
        "linkSpecimen_codestation" => "%|" . $val['site_code'] . "_%",
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $nb, // total data array
    ]);
  }

  /**
   * Creates a new internal lot entity.
   *
   * @Route("/new", name="internal_lot_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $lot = new InternalLot();

    $em = $this->getDoctrine()->getManager();
    if ($sampling_id = $request->get('idFk')) {
      $sampling = $em->getRepository('App:Sampling')->find($sampling_id);
      $lot->setSamplingFk($sampling);
    }

    $form = $this->createForm('App\Form\InternalLotType', $lot, [
      'action_type' => Action::create(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($lot);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_lot/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('internal_lot_edit', array(
        'id' => $lot->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }
    return $this->render('Core/internal_lot/edit.html.twig', array(
      'internalLot' => $lot,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a internal lot entity.
   *
   * @Route("/{id}", name="internal_lot_show", methods={"GET"})
   */
  public function showAction(InternalLot $lot) {
    $deleteForm = $this->createDeleteForm($lot);
    $editForm = $this->createForm('App\Form\InternalLotType', $lot, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/internal_lot/edit.html.twig', array(
      'internalLot' => $lot,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing internal lot entity.
   *
   * @Route("/{id}/edit", name="internal_lot_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, InternalLot $lot, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $lot->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    // store ArrayCollection
    $contents = $service->setArrayCollection('Contents', $lot);
    $taxonIdentifications = $service->setArrayCollectionEmbed('TaxonIdentifications', 'PersonSpeciesIds', $lot);
    $publications = $service->setArrayCollection('Publications', $lot);
    $producers = $service->setArrayCollection('Producers', $lot);

    //
    $deleteForm = $this->createDeleteForm($lot);
    $editForm = $this->createForm('App\Form\InternalLotType', $lot, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection('Contents', $lot, $contents);
      $service->DelArrayCollectionEmbed('TaxonIdentifications', 'PersonSpeciesIds', $lot, $taxonIdentifications);
      $service->DelArrayCollection('Publications', $lot, $publications);
      $service->DelArrayCollection('Producers', $lot, $producers);

      $em = $this->getDoctrine()->getManager();
      $em->persist($lot);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_lot/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/internal_lot/edit.html.twig', array(
        'internalLot' => $lot,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/internal_lot/edit.html.twig', array(
      'internalLot' => $lot,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a internal lot entity.
   *
   * @Route("/{id}", name="internal_lot_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, InternalLot $lot) {
    $form = $this->createDeleteForm($lot);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (
      ($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($lot);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_lot/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('internal_lot_index');
  }

  /**
   * Creates a form to delete a internal lot entity.
   *
   * @param InternalLot $lot The internal lot entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(InternalLot $lot) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('internal_lot_delete', ['id' => $lot->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }
}
