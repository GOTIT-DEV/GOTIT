<?php

namespace App\Controller\Core;

use App\Entity\EstAligneEtTraite;
use App\Entity\SequenceAssemblee;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use App\Entity\Adn;
use App\Entity\Assigne;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Chromatogramme;
use App\Entity\EspeceIdentifiee;
use App\Entity\Individu;
use App\Entity\Pcr;
use App\Entity\ReferentielTaxon;
use App\Entity\SqcEstPublieDans;
use App\Entity\Voc;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Sequenceassemblee controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("sequenceassemblee")]
class SequenceAssembleeController extends EntityController {
  const DATEINF_SQCALIGNEMENT_AUTO = '2018-05-01';

  /**
   * Lists all sequenceAssemblee entities.
   */
  #[Route("/", name: "sequenceassemblee_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/sequenceassemblee/index.html.twig');
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a column ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "sequenceassemblee_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'seq.dateMaj' => 'desc',
      'seq.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;

    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' && $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }


    $qb = $this->getRepository(SequenceAssemblee::class)
      ->createQueryBuilder('seq')
      ->select(
        'seq as sequence,
        specimen.codeIndBiomol as specimen_molecular_code,
        last_id.dateIdentification, id_criterion.code as criterion,
        taxon.taxname,
        geneVoc.code as gene, statusVoc.code as status'
      )
      ->join(
        EstAligneEtTraite::class,
        'chrom_process',
        'WITH',
        'chrom_process.sequenceAssembleeFk = seq.id '
      )
      ->join(Chromatogramme::class, 'chromato', 'WITH', 'chrom_process.chromatogrammeFk = chromato.id')
      ->join(
        EspeceIdentifiee::class,
        "last_id",
        "WITH",
        "seq.id = last_id.sequenceAssembleeFk and last_id.dateIdentification = (
        SELECT MAX(all_id.dateIdentification) FROM \App\Entity\EspeceIdentifiee all_id
        WHERE all_id.sequenceAssembleeFk = seq.id
      )"
      );

    if ($searchPhrase) {
      $qb = $qb->where('LOWER(seq.codeSqcAss) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($searchPhrase) . '%');
    }

    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $qb = $qb
        ->andWhere('chromato.id = :chromato')
        ->setParameter('chromato', $request->get('idFk'));
    }

    $query_total = clone $qb;
    $total = $query_total->select('count(distinct(seq))')->getQuery()->getSingleScalarResult();

    $query = $qb
      ->join(Voc::class, "statusVoc", "WITH", "statusVoc.id = seq.statutSqcAssVocFk")
      ->join(Pcr::class, "pcr", "WITH", "chromato.pcrFk = pcr.id")
      ->join(Adn::class, "dna", "WITH", "pcr.adnFk = dna.id")
      ->join(Individu::class, "specimen", "WITH", "dna.individuFk = specimen.id")
      ->join(Voc::class, "id_criterion", "WITH", "last_id.critereIdentificationVocFk = id_criterion.id")
      ->join(ReferentielTaxon::class, "taxon", "WITH", "taxon.id = last_id.referentielTaxonFk")
      ->join(Voc::class, "geneVoc", "WITH", "geneVoc.id = pcr.geneVocFk")
      ->distinct()
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->getQuery();

    $results = $query->getResult();



    // Search for the list to show
    $tab_toshow = [];
    foreach ($results as $key => $row) {

      /** @var SequenceAssemblee */
      $seq = $row["sequence"];

      $sources = $seq->getSqcEstPublieDanss()
        ->map(function (SqcEstPublieDans $rel) {
          return $rel->getSourceFk()->getLibelleSource();
        })
        ->toArray();

      $tab_toshow[] = [
        "id" => $seq->getId(),
        "seq.id" => $seq->getId(),
        "seq.codeSqcAss" => $seq->getCodeSqcAss(),
        "seq.codeSqcAlignement" => $seq->getCodeSqcAlignement(),
        "seq.accessionNumber" => $seq->getAccessionNumber(),
        "gene" => $seq->getGeneVocFk()->getCode(),
        "status" => $seq->getStatutSqcAssVocFk()->getCode(),
        "seq.DateCreationSqcAss" => $seq->getDateCreationSqcAss()?->format('Y-m-d'),
        "specimen_molecular_code" => $row["specimen_molecular_code"],
        "list_source" => implode(', ', $sources),
        "taxon.taxname" => $row["taxname"],
        "dateIdentification" => $row["dateIdentification"]?->format('Y-m-d'),
        "criterion" => $row["criterion"],
        // "motu_count" => $row["motu_count"],
        "motu_count" => $seq->getMotuAssignations()->count(),
        "creation_user_name" => $service->GetUserCreUsername($seq),
        "seq.dateCre" => $seq->getDateCre()?->format('Y-m-d H:i:s'),
        "seq.dateMaj" => $seq->getDateMaj()?->format('Y-m-d H:i:s'),
        "user_cre.user_full_name" => $service->GetUserCreUserfullname($seq),
        "user_maj.user_full_name" => $service->GetUserMajUserfullname($seq),
      ];
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $total
    ]);
  }

  /**
   * Creates a new sequenceAssemblee entity.
   */
  #[Route("/new", name: "sequenceassemblee_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function newAction(Request $request) {

    $sequence = new Sequenceassemblee();

    $chromatoFk = $request->get('idFk');
    $specimenFk = $request->get('individuFk');
    $geneFk = $request->get('geneVocFk');

    $chromatoRepo = $this->getRepository(Chromatogramme::class);

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
      'action_type' => Action::create->value,
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
      $this->entityManager->persist($sequence);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
   */
  #[Route("/{id}", name: "sequenceassemblee_show", methods: ["GET"])]
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
   */
  #[Route("/{id}/edit", name: "sequenceassemblee_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
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
      $this->entityManager->persist($sequence);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
   */
  #[Route("/{id}", name: "sequenceassemblee_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, SequenceAssemblee $sequenceAssemblee) {
    $form = $this->createDeleteForm($sequenceAssemblee);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($sequenceAssemblee);
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
