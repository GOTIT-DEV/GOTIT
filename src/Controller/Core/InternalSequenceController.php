<?php

namespace App\Controller\Core;

use App\Entity\InternalSequence;
use App\Form\Enums\Action;
use App\Services\Core\EntityEditionService;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * InternalSequence controller.
 *
 * @Route("internal_sequence")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InternalSequenceController extends AbstractController {
  /**
   * @var integer
   */
  private $geneVocFk = null;
  private $specimenFk = null;
  /**
   * constante
   */
  const DATEINF_SQCALIGNEMENT_AUTO = '2018-05-01';

  /**
   * Lists all internal sequence entities.
   *
   * @Route("/", name="internal_sequence_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $sequences = $em->getRepository('App:InternalSequence')->findAll();

    return $this->render('Core/internal_sequence/index.html.twig', array(
      'internalSequences' => $sequences,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a column ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="internal_sequence_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount') !== NULL)
    ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
    : "sq.date_of_update DESC, sq.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(sq.internal_sequence_code) LIKE :criteriaLower';
    $having = ' ';
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' && $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND chromato.id = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql = "SELECT
      sq.id,
      sq.internal_sequence_code,
      sq.internal_sequence_creation_date,
      sq.creation_user_name,
      sq.date_of_creation,
      sq.date_of_update,
      voc_internal_sequence_status.code as code_voc_internal_sequence_status,
      sq.internal_sequence_creation_date,
      sq.internal_sequence_alignment_code,
      sq.internal_sequence_accession_number,
      rt_sq.taxon_name as last_taxname_sq,
      ei_sq.identification_date as last_identification_date_sq,
      voc_sq_identification_criterion.code as code_sq_identification_criterion,
      meta_creation_user.user_full_name as meta_creation_user_username,
      user_maj.user_full_name as user_maj_username,
      voc_gene.code as voc_internal_sequence_gene_code,
      string_agg(DISTINCT sp.specimen_molecular_code, ' ;') as list_specimen_molecular_code,
      string_agg(DISTINCT source.source_title, ' ; ') as list_source,
      string_agg(DISTINCT cast( chromato.id as character varying) ,';') as list_chromato ,
      CASE
          WHEN (count(motu_number.id)=0) THEN 0
          WHEN (count(motu_number.id)>0) THEN 1
      END motu_flag
      FROM  internal_sequence sq
        LEFT JOIN user_db meta_creation_user ON meta_creation_user.id = sq.creation_user_name
        LEFT JOIN user_db user_maj ON user_maj.id = sq.update_user_name
        LEFT JOIN vocabulary voc_internal_sequence_status
          ON sq.internal_sequence_status_voc_fk = voc_internal_sequence_status.id
        LEFT JOIN internal_sequence_is_published_in isip
          ON isip.internal_sequence_fk = sq.id
        LEFT JOIN source ON isip.source_fk = source.id
        LEFT JOIN motu_number ON motu_number.internal_sequence_fk = sq.id
        LEFT JOIN chromatogram_is_processed_to eaet
          ON eaet.internal_sequence_fk = sq.id
        LEFT JOIN chromatogram chromato ON eaet.chromatogram_fk = chromato.id
        JOIN pcr ON chromato.pcr_fk = pcr.id
        LEFT JOIN vocabulary voc_gene ON pcr.gene_voc_fk = voc_gene.id
        JOIN dna ON pcr.dna_fk = dna.id
        JOIN specimen sp ON dna.specimen_fk = sp.id
        LEFT JOIN identified_species ei_sq ON ei_sq.internal_sequence_fk = sq.id
        INNER JOIN (
          SELECT MAX(ei_sqi.id) AS maxei_sqi
          FROM identified_species ei_sqi
          GROUP BY ei_sqi.internal_sequence_fk
        ) ei_sq2 ON (ei_sq.id = ei_sq2.maxei_sqi)
        LEFT JOIN taxon rt_sq ON ei_sq.taxon_fk = rt_sq.id
        LEFT JOIN vocabulary voc_sq_identification_criterion
          ON ei_sq.identification_criterion_voc_fk = voc_sq_identification_criterion.id"
      . " WHERE " . $where . "
        GROUP BY sq.id,sq.internal_sequence_code, internal_sequence_creation_date,
        sq.creation_user_name, sq.date_of_creation, sq.date_of_update,
        voc_internal_sequence_status.code,
        sq.internal_sequence_creation_date, sq.internal_sequence_alignment_code, sq.internal_sequence_accession_number,
        rt_sq.taxon_name, ei_sq.identification_date, voc_sq_identification_criterion.code,
        meta_creation_user.user_full_name, user_maj.user_full_name,
        voc_gene.code"
      . $having
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
        "internal_sequence_code" => $val['internal_sequence_code'],
        "internal_sequence_alignment_code" => $val['internal_sequence_alignment_code'],
        "internal_sequence_accession_number" => $val['internal_sequence_accession_number'],
        "voc_internal_sequence_gene_code" => $val['voc_internal_sequence_gene_code'],
        "voc_internal_sequence_status.code" => $val['code_voc_internal_sequence_status'],
        "sq.internal_sequence_creation_date" => $val['internal_sequence_creation_date'],
        "list_specimen_molecular_code" => $val['list_specimen_molecular_code'],
        "list_source" => $val['list_source'],
        "list_chromato" => $val['list_chromato'],
        "internal_sequence_creation_date" => $val['internal_sequence_creation_date'],
        "sq.date_of_creation" => $val['date_of_creation'],
        "sq.date_of_update" => $val['date_of_update'],
        "last_taxname_sq" => $val['last_taxname_sq'],
        "last_identification_date_sq" => $val['last_identification_date_sq'],
        "code_sq_identification_criterion" => $val['code_sq_identification_criterion'],
        "motu_flag" => $val['motu_flag'],
        "creation_user_name" => $val['creation_user_name'],
        "meta_creation_user.user_full_name" => ($val['meta_creation_user_username'] != null) ? $val['meta_creation_user_username'] : 'NA',
        "user_maj.user_full_name" => ($val['user_maj_username'] != null) ? $val['user_maj_username'] : 'NA',
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $nb,
    ]);
  }

  /**
   * Creates a new internal sequence entity.
   *
   * @Route("/new", name="internal_sequence_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {

    $sequence = new InternalSequence();

    $chromatoFk = $request->get('idFk');
    $specimenFk = $request->get('specimenFk');
    $geneFk = $request->get('geneVocFk');

    $chromatoRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository("App:Chromatogram");

    if ($chromatoFk) {
      $chromato = $chromatoRepo->find($chromatoFk);
      $pcr = $chromato->getPcrFk();
      $gene = $pcr->getGeneVocFk();
      $specimen = $pcr->getDnaFk()->getSpecimenFk();
    } elseif ($specimenFk && $geneFk) {
      $chromato = $chromatoRepo->createQueryBuilder('chromatogram')
        ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogram.pcrFk = pcr.id')
        ->leftJoin('App:Dna', 'dna', 'WITH', 'pcr.dnaFk = dna.id')
        ->leftJoin('App:Specimen', 'specimen', 'WITH', 'dna.specimenFk = specimen.id')
        ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
        ->andWhere('specimen.id = :specimenId')
        ->andWhere('vocGene.id = :geneFk')
        ->setParameter(':specimenId', $specimenFk)
        ->setParameter(':geneFk', $geneFk)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
      $pcr = $chromato->getPcrFk();
      $gene = $pcr->getGeneVocFk();
      $specimen = $pcr->getDnaFk()->getSpecimenFk();
    } else {
      $gene = null;
      $specimen = null;
      $chromato = null;
    }

    if ($chromato) {
      $processing = new InternalSequenceAssembly();
      $processing->setChromatogramFk($chromato);
      $sequence->addAssembly($processing);
    }

    $geneSpecimenForm = $this->createForm(
      'App\Form\Type\GeneSpecimenType',
      [
        "geneVocFk" => $gene,
        "specimenFk" => $specimen,
      ],
      ["action_type" => ($gene && $specimen) ? Action::show() : Action::create()]
    );

    $geneSpecimenForm->handleRequest($request);

    if ($geneSpecimenForm->isSubmitted() && $geneSpecimenForm->isValid()) {
      $gene = $geneSpecimenForm->get('geneVocFk')->getData();
      $specimen = $geneSpecimenForm->get('specimenFk')->getData();
      return $this->redirectToRoute('internal_sequence_new', [
        'specimenFk' => $specimen->getId(),
        'geneVocFk' => $gene->getId(),
      ]);
    }

    // Main form
    $form = $this->createForm('App\Form\InternalSequenceType', $sequence, [
      'action_type' => $gene && $specimen ? Action::create() : Action::show(),
      'gene' => $gene,
      'specimen' => $specimen,
      'attr' => ['id' => "sequence-form"],
      "action" => $this->generateUrl('internal_sequence_new', [
        'geneVocFk' => $gene ? $gene->getId() : null,
        'specimenFk' => $specimen ? $specimen->getId() : null,
        'idFk' => $chromato ? $chromato->getId() : null,
      ]),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $sequence->generateAlignmentCode();
      $em = $this->getDoctrine()->getManager();
      $em->persist($sequence);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_sequence/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('internal_sequence_edit', array(
        'id' => $sequence->getId(),
        'idFk' => $chromatoFk,
        'valid' => 1,
      ));
    }
    return $this->render('Core/internal_sequence/edit.html.twig', array(
      'internalSequence' => $sequence,
      'edit_form' => $form->createView(),
      'form_gene_indbiomol' => $geneSpecimenForm->createView(),
      'geneVocFk' => $gene,
      'specimenFk' => $specimen,
    ));
  }

  /**
   * Finds and displays a internal sequence entity.
   *
   * @Route("/{id}", name="internal_sequence_show", methods={"GET"})
   */
  public function showAction(InternalSequence $sequence) {
    $gene = $sequence->getGeneVocFk();
    $specimen = $sequence->getSpecimenFk();

    $deleteForm = $this->createDeleteForm($sequence);
    $editForm = $this->createForm(
      'App\Form\InternalSequenceType',
      $sequence,
      [
        'action_type' => Action::show(),
        'attr' => ['id' => "sequence-form"],
      ]
    );
    $geneSpecimenForm = $this
      ->createForm(
        'App\Form\Type\GeneSpecimenType',
        [
          'geneVocFk' => $gene,
          'specimenFk' => $specimen,
        ],
        ["action_type" => Action::show()]
      );
    return $this->render('Core/internal_sequence/edit.html.twig', [
      'internalSequence' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      'form_gene_indbiomol' => $geneSpecimenForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing internal sequence entity.
   *
   * @Route("/{id}/edit", name="internal_sequence_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(
    Request $request,
    InternalSequence $sequence,
    EntityEditionService $service
  ) {

    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $sequence->getMetaCreationUser() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    // Recherche du gene et de l'individu pour la sequence
    $em = $this->getDoctrine()->getManager();
    $id = $sequence->getId();
    $gene = $sequence->getGeneVocFk();
    $specimen = $sequence->getSpecimenFk();

    $form_gene_indbiomol = $this
      ->createForm(
        'App\Form\Type\GeneSpecimenType',
        ['geneVocFk' => $gene, 'specimenFk' => $specimen],
        ["action_type" => Action::show()]
      );

    // store ArrayCollection
    $assemblies = $service->copyArrayCollection($sequence->Assemblies());
    $taxonIdentifications = $service->copyArrayCollection(
      $sequence->getTaxonIdentifications()
    );
    $publications = $service->copyArrayCollection($sequence->Publications());
    $assemblers = $service->copyArrayCollection($sequence->Assemblers());

    $editForm = $this->createForm(
      'App\Form\InternalSequenceType',
      $sequence,
      [
        'gene' => $gene,
        'specimen' => $specimen,
        'action_type' => Action::edit(),
      ]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->removeStaleCollection($assemblies, $sequence->getAssemblies());
      $service->removeStaleCollection(
        $taxonIdentifications, $sequence->getTaxonIdentifications(), 'TaxonCurators'
      );
      $service->removeStaleCollection(
        $publications, $sequence->getPublications()
      );
      $service->removeStaleCollection($assemblers, $sequence->getAssemblers());
      $em->persist($sequence);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_sequence/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->render('Core/internal_sequence/edit.html.twig', array(
        'internalSequence' => $sequence,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
        'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
      ));
    }

    return $this->render('Core/internal_sequence/edit.html.twig', array(
      'internalSequence' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $this->createDeleteForm($sequence)->createView(),
      'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
    ));
  }

  /**
   * Deletes a internal sequence entity.
   *
   * @Route("/{id}", name="internal_sequence_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, InternalSequence $sequence) {
    $form = $this->createDeleteForm($sequence);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($sequence);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/internal_sequence/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('internal_sequence_index');
  }

  /**
   * Creates a form to delete a internal sequence entity.
   *
   * @param InternalSequence $sequence The internal sequence entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(InternalSequence $sequence) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'internal_sequence_delete',
        array('id' => $sequence->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
