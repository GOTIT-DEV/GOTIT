<?php

namespace App\Controller\Core;

use App\Entity\EstAligneEtTraite;
use App\Entity\SequenceAssemblee;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Sequenceassemblee controller.
 *
 * @Route("sequenceassemblee")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeController extends AbstractController {
  /**
   * @var integer
   */
  private $geneVocFk = null;
  private $individuFk = null;
  /**
   * constante
   */
  const DATEINF_SQCALIGNEMENT_AUTO = '2018-05-01';

   /**
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
       }


  /**
   * Lists all sequenceAssemblee entities.
   *
   * @Route("/", name="sequenceassemblee_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->doctrine->getManager();

    $sequenceAssemblees = $em->getRepository('App:SequenceAssemblee')->findAll();

    return $this->render('Core/sequenceassemblee/index.html.twig', array(
      'sequenceAssemblees' => $sequenceAssemblees,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a column ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="sequenceassemblee_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->doctrine->getManager();
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
      ei_sq.identification_date as last_date_identification_sq,
      voc_sq_identification_criterion.code as code_sq_identification_criterion,
      user_cre.user_full_name as user_cre_username,
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
        LEFT JOIN user_db user_cre ON user_cre.id = sq.creation_user_name
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
        user_cre.user_full_name, user_maj.user_full_name,
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
        "last_date_identification_sq" => $val['last_date_identification_sq'],
        "code_sq_identification_criterion" => $val['code_sq_identification_criterion'],
        "motu_flag" => $val['motu_flag'],
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
      "total" => $nb,
    ]);
  }

  /**
   * Creates a new sequenceAssemblee entity.
   *
   * @Route("/new", name="sequenceassemblee_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {

    $sequence = new Sequenceassemblee();

    $chromatoFk = $request->get('idFk');
    $specimenFk = $request->get('individuFk');
    $geneFk = $request->get('geneVocFk');

    $chromatoRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository("App:Chromatogramme");

    if ($chromatoFk) {
      $chromato = $chromatoRepo->find($chromatoFk);
      $pcr = $chromato->getPcrFk();
      $gene = $pcr->getGeneVocFk();
      $specimen = $pcr->getAdnFk()->getIndividuFk();
    } elseif ($specimenFk && $geneFk) {
      $chromato = $chromatoRepo->createQueryBuilder('chromatogramme')
        ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
        ->leftJoin('App:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
        ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
        ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
        ->andWhere('individu.id = :individuId')
        ->andWhere('vocGene.id = :geneFk')
        ->setParameter(':individuId', $specimenFk)
        ->setParameter(':geneFk', $geneFk)
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
      $pcr = $chromato->getPcrFk();
      $gene = $pcr->getGeneVocFk();
      $specimen = $pcr->getAdnFk()->getIndividuFk();
    } else {
      $gene = null;
      $specimen = null;
      $chromato = null;
    }

    if ($chromato) {
      $processing = new EstAligneEtTraite();
      $processing->setChromatogrammeFk($chromato);
      $sequence->addEstAligneEtTraite($processing);
    }

    $geneSpecimenForm = $this->createForm(
      'App\Form\Type\GeneSpecimenType',
      [
        "geneVocFk" => $gene,
        "individuFk" => $specimen,
      ],
      ["action_type" => ($gene && $specimen) ? Action::show->value : Action::create->value]
    );

    $geneSpecimenForm->handleRequest($request);

    if ($geneSpecimenForm->isSubmitted() && $geneSpecimenForm->isValid()) {
      $gene = $geneSpecimenForm->get('geneVocFk')->getData();
      $specimen = $geneSpecimenForm->get('individuFk')->getData();
      return $this->redirectToRoute('sequenceassemblee_new', [
        'individuFk' => $specimen->getId(),
        'geneVocFk' => $gene->getId(),
      ]);
    }

    // Main form
    $form = $this->createForm('App\Form\SequenceAssembleeType', $sequence, [
      'action_type' => $gene && $specimen ? Action::create->value : Action::show->value,
      'gene' => $gene,
      'specimen' => $specimen,
      'attr' => ['id' => "sequence-form"],
      "action" => $this->generateUrl('sequenceassemblee_new', [
        'geneVocFk' => $gene ? $gene->getId() : null,
        'individuFk' => $specimen ? $specimen->getId() : null,
        'idFk' => $chromato ? $chromato->getId() : null,
      ]),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $sequence->generateAlignmentCode();
      $em = $this->doctrine->getManager();
      $em->persist($sequence);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('sequenceassemblee_edit', array(
        'id' => $sequence->getId(),
        'idFk' => $chromatoFk,
        'valid' => 1,
      ));
    }
    return $this->render('Core/sequenceassemblee/edit.html.twig', array(
      'sequenceAssemblee' => $sequence,
      'edit_form' => $form->createView(),
      'form_gene_indbiomol' => $geneSpecimenForm->createView(),
      'geneVocFk' => $gene,
      'individuFk' => $specimen,
    ));
  }

  /**
   * Finds and displays a sequenceAssemblee entity.
   *
   * @Route("/{id}", name="sequenceassemblee_show", methods={"GET"})
   */
  public function showAction(SequenceAssemblee $sequence) {
    $gene = $sequence->getGeneVocFk();
    $specimen = $sequence->getIndividuFk();

    $deleteForm = $this->createDeleteForm($sequence);
    $editForm = $this->createForm(
      'App\Form\SequenceAssembleeType',
      $sequence,
      [
        'action_type' => Action::show->value,
        'attr' => ['id' => "sequence-form"],
      ]
    );
    $geneSpecimenForm = $this
      ->createForm(
        'App\Form\Type\GeneSpecimenType',
        [
          'geneVocFk' => $gene,
          'individuFk' => $specimen,
        ],
        ["action_type" => Action::show->value]
      );
    return $this->render('Core/sequenceassemblee/edit.html.twig', [
      'sequenceAssemblee' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      'form_gene_indbiomol' => $geneSpecimenForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing sequenceAssemblee entity.
   *
   * @Route("/{id}/edit", name="sequenceassemblee_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(
    Request $request,
    SequenceAssemblee $sequence,
    GenericFunctionE3s $service
  ) {

    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $sequence->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    // Recherche du gene et de l'individu pour la sequence
    $em = $this->doctrine->getManager();
    $id = $sequence->getId();
    $gene = $sequence->getGeneVocFk();
    $specimen = $sequence->getIndividuFk();

    $form_gene_indbiomol = $this
      ->createForm(
        'App\Form\Type\GeneSpecimenType',
        ['geneVocFk' => $gene, 'individuFk' => $specimen],
        ["action_type" => Action::show->value]
      );

    // store ArrayCollection
    $estAligneEtTraites = $service->setArrayCollection(
      'EstAligneEtTraites',
      $sequence
    );
    $especeIdentifiees = $service->setArrayCollectionEmbed(
      'EspeceIdentifiees',
      'EstIdentifiePars',
      $sequence
    );
    $sqcEstPublieDanss = $service->setArrayCollection(
      'SqcEstPublieDanss',
      $sequence
    );
    $sequenceAssembleeEstRealisePars = $service->setArrayCollection(
      'SequenceAssembleeEstRealisePars',
      $sequence
    );

    $editForm = $this->createForm(
      'App\Form\SequenceAssembleeType',
      $sequence,
      [
        'gene' => $gene,
        'specimen' => $specimen,
        'action_type' => Action::edit->value,
      ]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection(
        'EstAligneEtTraites',
        $sequence,
        $estAligneEtTraites
      );
      $service->DelArrayCollectionEmbed(
        'EspeceIdentifiees',
        'EstIdentifiePars',
        $sequence,
        $especeIdentifiees
      );
      $service->DelArrayCollection(
        'SqcEstPublieDanss',
        $sequence,
        $sqcEstPublieDanss
      );
      $service->DelArrayCollection(
        'SequenceAssembleeEstRealisePars',
        $sequence,
        $sequenceAssembleeEstRealisePars
      );
      $em->persist($sequence);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->render('Core/sequenceassemblee/edit.html.twig', array(
        'sequenceAssemblee' => $sequence,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
        'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
      ));
    }

    return $this->render('Core/sequenceassemblee/edit.html.twig', array(
      'sequenceAssemblee' => $sequence,
      'edit_form' => $editForm->createView(),
      'delete_form' => $this->createDeleteForm($sequence)->createView(),
      'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
    ));
  }

  /**
   * Deletes a sequenceAssemblee entity.
   *
   * @Route("/{id}", name="sequenceassemblee_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, SequenceAssemblee $sequenceAssemblee) {
    $form = $this->createDeleteForm($sequenceAssemblee);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->doctrine->getManager();
      try {
        $em->remove($sequenceAssemblee);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('sequenceassemblee_index');
  }

  /**
   * Creates a form to delete a sequenceAssemblee entity.
   *
   * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(SequenceAssemblee $sequenceAssemblee) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'sequenceassemblee_delete',
        array('id' => $sequenceAssemblee->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
