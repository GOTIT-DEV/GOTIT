<?php

namespace App\Controller\Core;

use App\Entity\LotMaterielExt;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Lotmaterielext controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("lotmaterielext")]
class LotMaterielExtController extends EntityController {

  /**
   * Lists all lotMaterielExt entities.
   */
  #[Route("/", name: "lotmaterielext_index", methods: ["GET"])]
  public function indexAction() {

    $lotMaterielExts = $this->getRepository(LotMaterielExt::class)->findAll();

    return $this->render(
      'Core/lotmaterielext/index.html.twig',
      ['lotMaterielExts' => $lotMaterielExts]
    );
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a column ($ request-> get ('sort'))
   *
   */
  #[Route("/indexjson", name: "lotmaterielext_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
      ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
      : "lot.date_of_update DESC, lot.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(lot.external_biological_material_code) LIKE :criteriaLower';
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
      lot.external_biological_material_creation_date,
      lot.date_of_creation,
      lot.date_of_update,
      voc_lot_identification_criterion.code as code_lot_identification_criterion,
	    lot.external_biological_material_code,
      rt_lot.taxon_name as last_taxname_lot,
      ei_lot.identification_date as last_date_identification_lot,
      lot.creation_user_name,
      user_cre.user_full_name as user_cre_username,
      user_maj.user_full_name as user_maj_username,
      string_agg(DISTINCT person.person_name , ' ; ') as list_person
	  FROM external_biological_material lot
    LEFT JOIN user_db user_cre ON user_cre.id = lot.creation_user_name
    LEFT JOIN user_db user_maj ON user_maj.id = lot.update_user_name
		JOIN sampling ON sampling.id = lot.sampling_fk
    JOIN site st ON st.id = sampling.site_fk
    LEFT JOIN country ON st.country_fk = country.id
    LEFT JOIN municipality ON st.municipality_fk = municipality.id
    LEFT JOIN external_biological_material_is_processed_by ebmip
      ON ebmip.external_biological_material_fk = lot.id
    LEFT JOIN person ON ebmip.person_fk = person.id
		LEFT JOIN identified_species ei_lot
      ON ei_lot.external_biological_material_fk = lot.id
    INNER JOIN (
      SELECT MAX(ei_loti.id) AS maxei_loti
      FROM identified_species ei_loti
      GROUP BY ei_loti.external_biological_material_fk
    ) ei_lot2
      ON (ei_lot.id = ei_lot2.maxei_loti)
    LEFT JOIN taxon rt_lot ON ei_lot.taxon_fk = rt_lot.id
    LEFT JOIN vocabulary voc_lot_identification_criterion
      ON ei_lot.identification_criterion_voc_fk = voc_lot_identification_criterion.id"
      . " WHERE " . $where . "
    GROUP BY lot.id, st.site_code, st.latitude, st.longitude,
      sampling.sample_code, country.country_name, municipality.municipality_code,
      lot.external_biological_material_creation_date,
      lot.date_of_creation, lot.date_of_update,
      voc_lot_identification_criterion.code,
      lot.external_biological_material_code,
      rt_lot.taxon_name,
      ei_lot.identification_date,
      lot.creation_user_name,
      user_cre.user_full_name, user_maj.user_full_name"
      . " ORDER BY " . $orderBy;

    // execute query and fill tab to show in the bootgrid list (see index.htm)
    $stmt = $this->entityManager->getConnection()->prepare($rawSql);
    $stmt->bindValue('criteriaLower', strtolower($searchPhrase) . '%');
    $res = $stmt->executeQuery();
    $entities_toshow = $res->fetchAllAssociative();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
      ? array_slice($entities_toshow, $minRecord, $rowCount)
      : array_slice($entities_toshow, $minRecord);

    foreach ($entities_toshow as $key => $val) {
      $tab_toshow[] = array(
        "id" => $val['id'], "lot.id" => $val['id'],
        "lot.external_biological_material_code" => $val['external_biological_material_code'],
        "last_taxname_lot" => $val['last_taxname_lot'],
        "last_date_identification_lot" => $val['last_date_identification_lot'],
        "code_lot_identification_criterion" => $val['code_lot_identification_criterion'],
        "lot.external_biological_material_creation_date" => $val['external_biological_material_creation_date'],
        "lot.date_of_creation" => $val['date_of_creation'],
        "lot.date_of_update" => $val['date_of_update'],
        "list_person" => $val['list_person'],
        "sampling.sample_code" => $val['sample_code'],
        "country.country_name" => $val['country_name'],
        "municipality.municipality_code" => $val['municipality_code'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_full_name" => ($val['user_cre_username'] != null) ? $val['user_cre_username'] : 'NA',
        "user_maj.user_full_name" => ($val['user_maj_username'] != null) ? $val['user_maj_username'] : 'NA',
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
   * Creates a new lotMaterielExt entity.
   */
  #[Route("/new", name: "lotmaterielext_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function newAction(Request $request) {
    $lotMaterielExt = new Lotmaterielext();

    if ($sampling_id = $request->get('idFk')) {
      $sampling = $this->getRepository(Collecte::class)->find($sampling_id);
      $lotMaterielExt->setCollecteFk($sampling);
    }

    $form = $this->createForm(
      'App\Form\LotMaterielExtType',
      $lotMaterielExt,
      ['action_type' => Action::create->value]
    );

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($lotMaterielExt);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/lotmaterielext/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('lotmaterielext_edit', [
        'id' => $lotMaterielExt->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    return $this->render('Core/lotmaterielext/edit.html.twig', [
      'lotMaterielExt' => $lotMaterielExt,
      'edit_form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a lotMaterielExt entity.
   *
   */
  #[Route("/{id}", name: "lotmaterielext_show", methods: ["GET"])]
  public function showAction(LotMaterielExt $lotMaterielExt) {
    $deleteForm = $this->createDeleteForm($lotMaterielExt);
    $editForm = $this->createForm(
      'App\Form\LotMaterielExtType',
      $lotMaterielExt,
      ['action_type' => Action::show->value]
    );

    return $this->render('Core/lotmaterielext/edit.html.twig', [
      'lotMaterielExt' => $lotMaterielExt,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing lotMaterielExt entity.
   */
  #[Route("/{id}/edit", name: "lotmaterielext_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, LotMaterielExt $lotMaterielExt, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $lotMaterielExt->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    // store ArrayCollection
    $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees', 'EstIdentifiePars', $lotMaterielExt);
    $lotMaterielExtEstReferenceDanss = $service->setArrayCollection('LotMaterielExtEstReferenceDanss', $lotMaterielExt);
    $lotMaterielExtEstRealisePars = $service->setArrayCollection('LotMaterielExtEstRealisePars', $lotMaterielExt);

    $deleteForm = $this->createDeleteForm($lotMaterielExt);
    $editForm = $this->createForm(
      'App\Form\LotMaterielExtType',
      $lotMaterielExt,
      ['action_type' => Action::edit->value]
    );

    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollectionEmbed('EspeceIdentifiees', 'EstIdentifiePars', $lotMaterielExt, $especeIdentifiees);
      $service->DelArrayCollection('LotMaterielExtEstReferenceDanss', $lotMaterielExt, $lotMaterielExtEstReferenceDanss);
      $service->DelArrayCollection('LotMaterielExtEstRealisePars', $lotMaterielExt, $lotMaterielExtEstRealisePars);
      $this->entityManager->persist($lotMaterielExt);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/lotmaterielext/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/lotmaterielext/edit.html.twig', [
        'lotMaterielExt' => $lotMaterielExt,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ]);
    }

    return $this->render('Core/lotmaterielext/edit.html.twig', [
      'lotMaterielExt' => $lotMaterielExt,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Deletes a lotMaterielExt entity.
   */
  #[Route("/{id}", name: "lotmaterielext_delete", methods: ["DELETE"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, LotMaterielExt $lotMaterielExt) {
    $form = $this->createDeleteForm($lotMaterielExt);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($lotMaterielExt);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/lotmaterielext/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('lotmaterielext_index');
  }

  /**
   * Creates a form to delete a lotMaterielExt entity.
   *
   * @param LotMaterielExt $lotMaterielExt The lotMaterielExt entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(LotMaterielExt $lotMaterielExt) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('lotmaterielext_delete', ['id' => $lotMaterielExt->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }
}
