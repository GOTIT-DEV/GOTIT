<?php

namespace App\Controller\Core;

use App\Entity\Individu;
use App\Entity\TaxonIdentification;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Individu controller.
 *
 * @Route("individu")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class IndividuController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all individu entities.
   *
   * @Route("/", name="individu_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $individus = $em->getRepository('App:Individu')->findAll();

    return $this->render('Core/individu/index.html.twig', [
      'individus' => $individus,
    ]);
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="individu_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('ind.id, ind.codeIndBiomol as code')
      ->from('App:Individu', 'ind');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(ind.codeIndBiomol) like :q' . $i . ')');
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();

    return $this->json($results);
  }

  /**
   * @Route("/search_with_gene/{query}/{gene}", name="individu_search_with_gene")
   */
  public function searchWithGeneAction(String $query, int $gene) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('ind.id, ind.codeIndBiomol as code')
      ->from('App:Individu', 'ind')
      ->leftJoin('App:Dna', 'dna', 'WITH', 'dna.individuFk = ind.id')
      ->leftJoin('App:Pcr', 'pcr', 'WITH', 'pcr.adnFk = dna.id')
      ->leftJoin('App:Voc', 'vocgene', 'WITH', 'pcr.geneVocFk = vocgene.id')
      ->andWhere('LOWER(ind.codeIndBiomol) LIKE :searchcode')
      ->andWhere('vocgene.id = :idvocgene ')
      ->setParameter('searchcode', strtolower($query) . '%')
      ->setParameter('idvocgene', (int) $gene)
      ->addOrderBy('code', 'ASC')
      ->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);

    $results = $qb->getQuery()->getResult();

    return $this->json($results);
  }

  /**
   * @Route("/search_by_codeindmorpho/{q}", requirements={"q"=".+"}, name="individu_search_by_codeindmorpho")
   */
  public function searchByCodeIndmorphoAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('ind.id, ind.codeIndTriMorpho as code')
      ->from('App:Individu', 'ind');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    $and = [];
    for ($i = 0; $i < count($query); $i++) {
      $and[] = '(LOWER(ind.codeIndTriMorpho) like :q' . $i . ')';
    }
    $qb->where(implode(' and ', $and));
    for ($i = 0; $i < count($query); $i++) {
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb
      ->addOrderBy('code', 'ASC')
      ->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();
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
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $em = $this->getDoctrine()->getManager();
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort')
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "sp.date_of_update DESC, sp.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = ' LOWER(sp.specimen_morphological_code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND sp.internal_biological_material_fk = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT sp.id,
      st.site_code, st.latitude, st.longitude,
      sampling.sample_code,
      country.country_name,
      municipality.municipality_code,
      st.site_code,
      sp.specimen_molecular_code, sp.specimen_morphological_code,
      sp.specimen_molecular_number, sp.tube_code,
      sp.date_of_creation, sp.date_of_update,
      rt_sp.taxon_name as last_taxname_sp,
      ei_sp.identification_date as last_date_identification_sp,
      voc_sp_identification_criterion.code as code_sp_identification_criterion,
      voc_sp_specimen_type.code as voc_sp_specimen_type_code,
      sp.creation_user_name,
      user_cre.user_full_name as user_cre_username,
      user_maj.user_full_name as user_maj_username,
      string_agg(cast( dna.id as character varying) , ' ;') as list_dna,
      string_agg(cast( specimen_slide.id as character varying) , ' ;') as list_specimen_slide
	  FROM  specimen sp
    LEFT JOIN user_db user_cre ON user_cre.id = sp.creation_user_name
    LEFT JOIN user_db user_maj ON user_maj.id = sp.update_user_name
    JOIN internal_biological_material lot
      ON sp.internal_biological_material_fk = lot.id
		JOIN sampling ON sampling.id = lot.sampling_fk
    JOIN site st ON st.id = sampling.site_fk
    LEFT JOIN country ON st.country_fk = country.id
    LEFT JOIN municipality ON st.municipality_fk = municipality.id
    LEFT JOIN vocabulary voc_sp_specimen_type
      ON sp.specimen_type_voc_fk = voc_sp_specimen_type.id
		LEFT JOIN identified_species ei_sp ON ei_sp.specimen_fk = sp.id
    INNER JOIN (
      SELECT MAX(ei_spi.id) AS maxei_spi
      FROM identified_species ei_spi
      GROUP BY ei_spi.specimen_fk
    ) ei_sp2 ON (ei_sp.id = ei_sp2.maxei_spi)
    LEFT JOIN taxon rt_sp ON ei_sp.taxon_fk = rt_sp.id
    LEFT JOIN vocabulary voc_sp_identification_criterion
      ON ei_sp.identification_criterion_voc_fk = voc_sp_identification_criterion.id
		LEFT JOIN dna ON dna.specimen_fk = sp.id
    LEFT JOIN specimen_slide ON specimen_slide.specimen_fk = sp.id"
      . " WHERE " . $where . "
      GROUP BY sp.id, st.site_code, st.latitude, st.longitude,
        sampling.sample_code, country.country_name,
        municipality.municipality_code, st.site_code,
        sp.specimen_molecular_code, sp.specimen_morphological_code,
        sp.specimen_molecular_number, sp.tube_code,
        sp.date_of_creation, sp.date_of_update,
        rt_sp.taxon_name, ei_sp.identification_date,
        voc_sp_identification_criterion.code, voc_sp_specimen_type.code,
        sp.creation_user_name,
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

    foreach ($entities_toshow as $key => $val) {
      $linkDna = $val['list_dna'] ? strval($val['id']) : '';
      $linkIndividulame = $val['list_specimen_slide'] ? strval($val['id']) : '';
      $tab_toshow[] = array(
        "id" => $val['id'],
        "sp.id" => $val['id'],
        "st.site_code" => $val['site_code'],
        "sp.specimen_molecular_code" => $val['specimen_molecular_code'],
        "sp.specimen_morphological_code" => $val['specimen_morphological_code'],
        "voc_sp_specimen_type.code" => $val['voc_sp_specimen_type_code'],
        "sp.specimen_molecular_number" => $val['specimen_molecular_number'],
        "sp.tube_code" => $val['tube_code'],
        "last_taxname_sp" => $val['last_taxname_sp'],
        "last_date_identification_sp" => $val['last_date_identification_sp'],
        "code_sp_identification_criterion" => $val['code_sp_identification_criterion'],
        "sp.date_of_creation" => $val['date_of_creation'],
        "sp.date_of_update" => $val['date_of_update'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_full_name" => ($val['user_cre_username'] != null) ? $val['user_cre_username'] : 'NA',
        "user_maj.user_full_name" => ($val['user_maj_username'] != null) ? $val['user_maj_username'] : 'NA',
        "linkDna" => $linkDna,
        "linkIndividulame" => $linkIndividulame,
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
   * Creates a new individu entity.
   *
   * @Route("/new", name="individu_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $individu = new Individu();
    $individu->addTaxonIdentification(new TaxonIdentification());

    $em = $this->getDoctrine()->getManager();
    if ($biomat_id = $request->get('idFk')) {
      $biomat = $em->getRepository('App:LotMateriel')->find($biomat_id);
      $individu->setLotMaterielFk($biomat);
    }
    $form = $this->createForm('App\Form\IndividuType', $individu, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($individu);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/individu/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('individu_edit', [
        'id' => $individu->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    return $this->render('Core/individu/edit.html.twig', [
      'individu' => $individu,
      'edit_form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a individu entity.
   *
   * @Route("/{id}", name="individu_show", methods={"GET"})
   */
  public function showAction(Individu $individu) {
    $deleteForm = $this->createDeleteForm($individu);
    $editForm = $this->createForm('App\Form\IndividuType', $individu, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/individu/edit.html.twig', [
      'individu' => $individu,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing individu entity.
   *
   * @Route("/{id}/edit", name="individu_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Individu $individu, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $individu->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $taxonIdentifications = $service->setArrayCollectionEmbed('TaxonIdentifications', 'PersonSpeciesIds', $individu);

    $deleteForm = $this->createDeleteForm($individu);
    if ($individu->getCodeIndBiomol()) {
      $editForm = $this->createForm('App\Form\IndividuType', $individu, [
        'action_type' => Action::edit(),
      ]);
    } else {
      $editForm = $this->createForm('App\Form\IndividuType', $individu, [
        'action_type' => Action::edit(),
      ]);
    }
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $service->DelArrayCollectionEmbed('TaxonIdentifications', 'PersonSpeciesIds', $individu, $taxonIdentifications);
      $this->getDoctrine()->getManager()->persist($individu);
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/individu/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/individu/edit.html.twig', [
        'individu' => $individu,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ]);
    }

    return $this->render('Core/individu/edit.html.twig', [
      'individu' => $individu,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Deletes a individu entity.
   *
   * @Route("/{id}", name="individu_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Individu $individu) {
    $form = $this->createDeleteForm($individu);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($individu);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/individu/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
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
  private function createDeleteForm(Individu $individu) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('individu_delete', ['id' => $individu->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }
}
