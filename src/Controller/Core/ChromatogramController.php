<?php

namespace App\Controller\Core;

use App\Entity\Chromatogram;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Chromatogram controller.
 *
 * @Route("chromatogram")
 * @Security("is_granted('ROLE_INVITED')")
 *
 */
class ChromatogramController extends AbstractController {

  /**
   * Lists all chromatogram entities.
   *
   * @Route("/", name="chromatogram_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $chromatograms = $em->getRepository('App:Chromatogram')->findAll();

    return $this->render('Core/chromatogram/index.html.twig', array(
      'chromatograms' => $chromatograms,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="chromatogram_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount') !== NULL)
    ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "chromato.date_of_update DESC, chromato.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(chromato.chromatogram_code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND chromato.pcr_fk = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT
          chromato.id,
          chromato.chromatogram_code,
          chromato.creation_user_name,
          chromato.date_of_creation,
          chromato.date_of_update,
          sp.specimen_molecular_code,
          dna.dna_code,
          pcr.pcr_code,
          pcr.pcr_number,
          voc_gene.code as code_voc_gene,
          voc_chromato_quality.code as code_voc_chromato_quality,
          array_agg(sq.internal_sequence_code ORDER BY sq.id DESC) as last_internal_sequence_code,

          array_agg(sq.internal_sequence_creation_date ORDER BY sq.id DESC) as last_internal_sequence_creation_date,
          array_agg(sq.internal_sequence_alignment_code ORDER BY sq.id DESC) as last_internal_sequence_alignment_code,

          array_agg(voc_statut_sqc_ass.code ORDER BY sq.id DESC) as last_internal_sequence_status_voc,
          user_cre.user_full_name as user_cre_username,
          user_maj.user_full_name as user_maj_username
          FROM  chromatogram chromato
          LEFT JOIN user_db user_cre ON user_cre.id = chromato.creation_user_name
          LEFT JOIN user_db user_maj ON user_maj.id = chromato.update_user_name
          LEFT JOIN vocabulary voc_chromato_quality ON chromato.chromato_quality_voc_fk = voc_chromato_quality.id
          JOIN pcr ON chromato.pcr_fk = pcr.id
          LEFT JOIN vocabulary voc_gene ON pcr.gene_voc_fk = voc_gene.id
          JOIN dna ON pcr.dna_fk = dna.id
          JOIN specimen sp ON dna.specimen_fk = sp.id
          LEFT JOIN chromatogram_is_processed_to eaet ON eaet.chromatogram_fk = chromato.id
          LEFT JOIN (
            SELECT MAX(eaeti.id) AS maxeaeti
            FROM chromatogram_is_processed_to eaeti
            GROUP BY eaeti.chromatogram_fk
            ) eaet2 ON (eaet.id = eaet2.maxeaeti)
          LEFT JOIN internal_sequence sq ON eaet.internal_sequence_fk = sq.id
          LEFT JOIN vocabulary voc_statut_sqc_ass ON sq.internal_sequence_status_voc_fk = voc_statut_sqc_ass.id"
      . " WHERE " . $where . "
            GROUP BY chromato.id, chromato.creation_user_name,
              chromato.date_of_creation, chromato.date_of_update,
              sp.specimen_molecular_code, dna.dna_code, pcr.pcr_code, pcr.pcr_number,
              voc_gene.code, voc_chromato_quality.code,
              user_cre.user_full_name, user_maj.user_full_name"
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

    $get_code = function ($string) {
      return explode(",", rtrim(ltrim($string, "{"), "}"))[0];
    };

    foreach ($entities_toshow as $key => $val) {
      $linkSqcAss = $get_code($val['last_internal_sequence_code']) !== 'NULL'
      ? strval($val['id']) : '';
      $tab_toshow[] = array(
        "id" => $val['id'],
        "chromato.id" => $val['id'],
        "sp.specimen_molecular_code" => $val['specimen_molecular_code'],
        "dna.dna_code" => $val['dna_code'],
        "chromato.chromatogram_code" => $val['chromatogram_code'],
        "code_voc_gene" => $val['code_voc_gene'],
        "pcr.pcr_code" => $val['pcr_code'],
        "pcr.pcr_number" => $val['pcr_number'],
        "code_voc_chromato_quality" => $val['code_voc_chromato_quality'],
        "chromato.date_of_creation" => $val['date_of_creation'],
        "chromato.date_of_update" => $val['date_of_update'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_full_name" => ($val['user_cre_username'] != null) ? $val['user_cre_username'] : 'NA',
        "user_maj.user_full_name" => ($val['user_maj_username'] != null) ? $val['user_maj_username'] : 'NA',
        "last_internal_sequence_code" => $get_code($val['last_internal_sequence_code']),
        "last_internal_sequence_status_voc" => $get_code($val['last_internal_sequence_status_voc']),
        "last_internal_sequence_alignment_code" => $get_code($val['last_internal_sequence_alignment_code']),
        "last_internal_sequence_creation_date" => $get_code($val['last_internal_sequence_creation_date']),
        "linkInternalSequence" => $linkSqcAss,
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
   * Creates a new chromatogram entity.
   *
   * @Route("/new", name="chromatogram_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $chromatogram = new Chromatogram();
    $em = $this->getDoctrine()->getManager();
    // check if the relational Entity (Pcr) is given and set the RelationalEntityFk for the new Entity
    if ($pcr_id = $request->get('idFk')) {
      $pcr = $em->getRepository('App:Pcr')->find($pcr_id);
      $chromatogram->setPcrFk($pcr);
    }
    $form = $this->createForm('App\Form\ChromatogramType', $chromatogram, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $em->persist($chromatogram);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogram/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('chromatogram_edit', array(
        'id' => $chromatogram->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/chromatogram/edit.html.twig', array(
      'chromatogram' => $chromatogram,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a chromatogram entity.
   *
   * @Route("/{id}", name="chromatogram_show", methods={"GET"})
   */
  public function showAction(Chromatogram $chromatogram) {
    $deleteForm = $this->createDeleteForm($chromatogram);
    $editForm = $this->createForm('App\Form\ChromatogramType', $chromatogram, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/chromatogram/edit.html.twig', array(
      'chromatogram' => $chromatogram,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing chromatogram entity.
   *
   * @Route("/{id}/edit", name="chromatogram_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Chromatogram $chromatogram) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $chromatogram->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    //
    $deleteForm = $this->createDeleteForm($chromatogram);
    $editForm = $this->createForm('App\Form\ChromatogramType', $chromatogram, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {

      $em = $this->getDoctrine()->getManager();
      $em->persist($chromatogram);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogram/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/chromatogram/edit.html.twig', array(
        'chromatogram' => $chromatogram,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/chromatogram/edit.html.twig', array(
      'chromatogram' => $chromatogram,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a chromatogram entity.
   *
   * @Route("/{id}", name="chromatogram_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Chromatogram $chromatogram) {
    $form = $this->createDeleteForm($chromatogram);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($chromatogram);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogram/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('chromatogram_index');
  }

  /**
   * Creates a form to delete a chromatogram entity.
   *
   * @param Chromatogram $chromatogram The chromatogram entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Chromatogram $chromatogram) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('chromatogram_delete', array('id' => $chromatogram->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
