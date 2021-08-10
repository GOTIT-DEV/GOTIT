<?php

namespace App\Controller\Core;

use App\Entity\ExternalSequence;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ExternalSequence controller.
 *
 * @Route("external_sequence")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ExternalSequenceController extends AbstractController {
  /**
   * Lists all external sequence entities.
   *
   * @Route("/", name="external_sequence_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $sequences = $em->getRepository('App:ExternalSequence')
      ->findAll();

    return $this->render(
      'Core/external_sequence/index.html.twig',
      ['externalSequences' => $sequences]
    );
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="external_sequence_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service, TranslatorInterface $translator) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "sq.date_of_update DESC, sq.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(sq.external_sequence_code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND sq.sampling_fk = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT  sq.id,
        st.site_code,
        sampling.sample_code,
        country.country_name,
        municipality.municipality_code,
        sq.external_sequence_creation_date,
        sq.date_of_creation,
        sq.date_of_update,
        voc_sq_identification_criterion.code as code_sq_identification_criterion,
	      sq.external_sequence_code,
        sq.external_sequence_alignment_code,
        rt_sq.taxon_name as last_taxname_sq,
        ei_sq.identification_date as last_date_identification_sq,
        sq.external_sequence_primary_taxon,
        sq.external_sequence_specimen_number,
        sq.external_sequence_accession_number,
        voc_gene.code as voc_external_sequence_gene_code,
        voc_status.code as voc_external_sequence_status_code,
        voc_date_precision.vocabulary_title as voc_date_precision_title,
        sq.creation_user_name,
        user_cre.user_full_name as user_cre_username,
        user_maj.user_full_name as user_maj_username,
        string_agg(DISTINCT source.source_title , ' ; ') as list_source,
        CASE
            WHEN (count(motu_number.id)=0) THEN 0
            WHEN (count(motu_number.id)>0) THEN 1
        END motu_flag
	  FROM external_sequence sq
    LEFT JOIN user_db user_cre ON user_cre.id = sq.creation_user_name
    LEFT JOIN user_db user_maj ON user_maj.id = sq.update_user_name
		JOIN sampling ON sampling.id = sq.sampling_fk
    JOIN site st ON st.id = sampling.site_fk
    LEFT JOIN country ON st.country_fk = country.id
    LEFT JOIN municipality ON st.municipality_fk = municipality.id
    LEFT JOIN external_sequence_is_published_in esip
      ON esip.external_sequence_fk = sq.id
    LEFT JOIN source ON esip.source_fk = source.id
    LEFT JOIN vocabulary voc_gene ON sq.gene_voc_fk = voc_gene.id
    LEFT JOIN vocabulary voc_status
      ON sq.external_sequence_status_voc_fk = voc_status.id
    LEFT JOIN vocabulary voc_date_precision
      ON sq.date_precision_voc_fk = voc_date_precision.id
    LEFT JOIN motu_number ON motu_number.external_sequence_fk = sq.id
		LEFT JOIN identified_species ei_sq ON ei_sq.external_sequence_fk = sq.id
    LEFT JOIN (
      SELECT MAX(ei_sqi.id) AS maxei_sqi
        FROM identified_species ei_sqi
        GROUP BY ei_sqi.external_sequence_fk
      ) ei_sq2
      ON ei_sq.id = ei_sq2.maxei_sqi
    LEFT JOIN taxon rt_sq ON ei_sq.taxon_fk = rt_sq.id
    LEFT JOIN vocabulary voc_sq_identification_criterion
      ON ei_sq.identification_criterion_voc_fk = voc_sq_identification_criterion.id"
      . " WHERE " . $where . "
    GROUP BY sq.id, st.site_code, sampling.sample_code, country.country_name,
      municipality.municipality_code,
      sq.external_sequence_creation_date, sq.date_of_creation, sq.date_of_update,
      voc_sq_identification_criterion.code,
      sq.external_sequence_code, sq.external_sequence_alignment_code,
      rt_sq.taxon_name, ei_sq.identification_date,
      sq.external_sequence_primary_taxon, sq.external_sequence_specimen_number,
      sq.external_sequence_accession_number,
      voc_gene.code, voc_status.code, voc_date_precision.vocabulary_title,
      sq.creation_user_name, user_cre.user_full_name, user_maj.user_full_name"
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
      $tab_toshow[] = array(
        "id" => $val['id'], "sq.id" => $val['id'],
        "sq.external_sequence_alignment_code" => $val['external_sequence_alignment_code'],
        "sq.external_sequence_code" => $val['external_sequence_code'],
        "sq.external_sequence_accession_number" => $val['external_sequence_accession_number'],
        "voc_gene.code" => $val['voc_external_sequence_gene_code'],
        "voc_date_precision.vocabulary_title" => $val['voc_date_precision_title'],
        "sq.external_sequence_creation_date" => $val['external_sequence_creation_date'],
        "sq.external_sequence_primary_taxon" => $val['external_sequence_primary_taxon'],
        "sq.external_sequence_specimen_number" => $val['external_sequence_specimen_number'],
        "voc_status.code" => $val['voc_external_sequence_status_code'],
        "sq.date_of_creation" => $val['date_of_creation'],
        "sq.date_of_update" => $val['date_of_update'],
        "sampling.sample_code" => $val['sample_code'],
        "last_taxname_sq" => $val['last_taxname_sq'],
        "last_date_identification_sq" => $val['last_date_identification_sq'],
        "voc_sq_identification_criterion.code" => $val['code_sq_identification_criterion'],
        "list_source" => $val['list_source'],
        "motu_flag" => $val['motu_flag'],
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
   * Creates a new external sequence entity.
   *
   * @Route("/new", name="external_sequence_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $sequence = new ExternalSequence();
    $em = $this->getDoctrine()->getManager();
    // check if the relational Entity (Sampling) is given and set the RelationalEntityFk for the new Entity
    if ($sampling_id = $request->get('idFk')) {
      $sampling = $em->getRepository('App:Sampling')->find($sampling_id);
      $sequence->setSamplingFk($sampling);
    }
    $form = $this->createForm(
      'App\Form\ExternalSequenceType',
      $sequence,
      [
        'refTaxonLabel' => 'code',
        'action_type' => Action::create(),
      ]
    );
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $em->persist($sequence);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = str_replace(
          ['"', "'"],
          ['\"', "\'"],
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/external_sequence/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('external_sequence_edit', [
        'id' => $sequence->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    return $this->render('Core/external_sequence/edit.html.twig', [
      'externalSequence' => $sequence,
      'edit_form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a external sequence entity.
   *
   * @Route("/{id}", name="external_sequence_show", methods={"GET"})
   */
  public function showAction(ExternalSequence $sequence) {
    $deleteForm = $this->createDeleteForm($sequence);
    $editForm = $this->createForm(
      'App\Form\ExternalSequenceType',
      $sequence,
      ['action_type' => Action::show()]
    );

    return $this->render('Core/external_sequence/edit.html.twig', [
      'externalSequence' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing external sequence entity.
   *
   * @Route("/{id}/edit", name="external_sequence_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, ExternalSequence $sequence, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $sequence->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    // load service  generic_function_e3s
    //
    // load the Entity Manager
    $em = $this->getDoctrine()->getManager();

    // store ArrayCollection
    $taxonIdentifications = $service->setArrayCollectionEmbed(
      'TaxonIdentifications',
      'TaxonCurators',
      $sequence
    );
    $externalSequencePublications = $service->setArrayCollection(
      'ExternalSequencePublications',
      $sequence
    );
    $assemblers = $service->setArrayCollection(
      'Assemblers',
      $sequence
    );

    $deleteForm = $this->createDeleteForm($sequence);
    $editForm = $this->createForm(
      'App\Form\ExternalSequenceType',
      $sequence,
      ['action_type' => Action::edit()]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollectionEmbed(
        'TaxonIdentifications',
        'TaxonCurators',
        $sequence,
        $taxonIdentifications
      );
      $service->DelArrayCollection(
        'ExternalSequencePublications',
        $sequence,
        $externalSequencePublications
      );
      $service->DelArrayCollection(
        'Assemblers',
        $sequence,
        $assemblers
      );

      $em = $this->getDoctrine()->getManager();
      $em->persist($sequence);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/external_sequence/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      $editForm = $this->createForm(
        'App\Form\ExternalSequenceType',
        $sequence,
        ['action_type' => Action::edit()]
      );

      return $this->render('Core/external_sequence/edit.html.twig', array(
        'externalSequence' => $sequence,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/external_sequence/edit.html.twig', array(
      'externalSequence' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a external sequence entity.
   *
   * @Route("/{id}", name="external_sequence_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, ExternalSequence $sequence) {
    $form = $this->createDeleteForm($sequence);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($sequence);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/external_sequence/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('external_sequence_index');
  }

  /**
   * Creates a form to delete a external sequence entity.
   *
   * @param ExternalSequence $sequence The external sequence entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(ExternalSequence $sequence) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('external_sequence_delete', array('id' => $sequence->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * Creates a createCode
   *
   * @param ExternalSequence $internalSequence The internalSequence entity
   *
   */
  private function createCode(ExternalSequence $sequence) {
    $codeSqc = '';
    $em = $this->getDoctrine()->getManager();
    $TaxonIdentifications = $sequence->getTaxonIdentifications();
    $nbTaxonIdentifications = count($TaxonIdentifications);
    if ($nbTaxonIdentifications > 0) {
      // The status of the sequence and the referential Taxon = to the last taxname attributed
      $codeStatutSqcAss = $sequence->getStatus()->getCode();
      $arrayTaxon = array();
      foreach ($TaxonIdentifications as $entityTaxonIdentifications) {
        $arrayTaxon[$entityTaxonIdentifications->getTaxonFk()->getId()] =
        $entityTaxonIdentifications->getTaxonFk()->getCode();
      }
      ksort($arrayTaxon);
      reset($arrayTaxon);
      $firstTaxname = current($arrayTaxon);
      $codeSqc = (substr($codeStatutSqcAss, 0, 5) == 'VALID')
      ? $firstTaxname : $codeStatutSqcAss . '_' . $firstTaxname;
      $codeCollecte = $sequence->getSamplingFk()->getCodeCollecte();
      $numSpecimenSqcAssExt = $sequence->getNumSpecimenSqcAssExt();
      $accessionNumber = $sequence->getAccessionNumber();
      $codeOrigineSqcAssExt = $sequence->getOriginVocFk()->getCode();
      $codeSqc = $codeSqc . '_' . $codeCollecte . '_' . $numSpecimenSqcAssExt .
        '_' . $accessionNumber . '|' . $codeOrigineSqcAssExt;
    } else {
      $codeSqc = 0;
    }
    return $codeSqc;
  }

  /**
   * Creates a createAlignmentCode
   *
   * @param ExternalSequence $internalSequence The internalSequence entity
   *
   */
  private function createAlignmentCode(ExternalSequence $sequence) {
    $alignmentCode = '';
    $em = $this->getDoctrine()->getManager();
    $TaxonIdentifications = $sequence->getTaxonIdentifications();
    $nbTaxonIdentifications = count($TaxonIdentifications);
    if ($nbTaxonIdentifications > 0) {
      // Le statut de la sequence ET le taxon = au derenier taxname attribué
      $codeStatutSqcAss = $sequence->getStatus()->getCode();
      $arrayTaxon = array();
      foreach ($TaxonIdentifications as $entityTaxonIdentifications) {
        $arrayTaxon[$entityTaxonIdentifications->getTaxonFk()->getId()] =
        $entityTaxonIdentifications->getTaxonFk()->getCode();
      }
      ksort($arrayTaxon);
      end($arrayTaxon);
      $lastCode = current($arrayTaxon);
      $alignmentCode = (substr($codeStatutSqcAss, 0, 5) == 'VALID')
      ? $lastCode
      : $codeStatutSqcAss . '_' . $lastCode;
      $codeCollecte = $sequence->getSamplingFk()->getCodeCollecte();
      $numSpecimenSqcAssExt = $sequence->getNumSpecimenSqcAssExt();
      $accessionNumber = $sequence->getAccessionNumber();
      $codeOrigineSqcAssExt = $sequence->getOriginVocFk()->getCode();
      $alignmentCode = $alignmentCode . '_' .
        $codeCollecte . '_' .
        $numSpecimenSqcAssExt . '_' .
        $accessionNumber . '_' . $codeOrigineSqcAssExt;
    } else {
      $alignmentCode = 0;
    }
    return $alignmentCode;
  }
}
