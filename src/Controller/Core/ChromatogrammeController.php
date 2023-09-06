<?php

namespace App\Controller\Core;

use App\Entity\Chromatogramme;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use App\Entity\Adn;
use App\Entity\EstAligneEtTraite;
use App\Entity\Individu;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Pcr;
use App\Entity\SequenceAssemblee;
use App\Entity\Voc;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Chromatogramme controller.
 */
#[Route("chromatogramme")]
class ChromatogrammeController extends EntityController {
  /**
   * Lists all chromatogramme entities.
   */
  #[Route("/", name: "chromatogramme_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/chromatogramme/index.html.twig');
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   */
  #[Route("/indexjson", name: "chromatogramme_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'chromato.dateMaj' => 'desc', 'chromato.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }

    $tab_toshow = [];

    $qb = $this->getRepository(Chromatogramme::class)
      ->createQueryBuilder('chromato');

    if ($searchPhrase) {
      $qb = $qb
        ->where('LOWER(chromato.codeChromato) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($searchPhrase) . '%');
    }

    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $qb = $qb->andWhere('chromato.pcrFk = :pcrFk')
        ->setParameter('pcrFk', $request->get('idFk'));
    }

    $query_total = clone $qb;
    $total = $query_total->select('count(chromato.id)')->getQuery()->getSingleScalarResult();

    // $qb = $qb
    //   ->select("chromato, chrom_quality.code as quality,
    //   pcr.codePcr, pcr.numPcr, pcr_gene.code as gene,
    //   dna.codeAdn, specimen.codeIndBiomol,
    //   last_seq.codeSqcAlignement,
    //   last_seq.codeSqcAss,
    //   last_seq.dateCreationSqcAss")
    //   // seq_status.code as last_seq_status,
    //   ->join(Voc::class, "chrom_quality", "WITH", "chrom_quality = chromato.qualiteChromatoVocFk")
    //   ->join(Pcr::class, "pcr")
    //   ->join(Voc::class, "pcr_gene", "WITH", "pcr_gene = pcr.geneVocFk")
    //   ->join(Adn::class, "dna", "WITH", "dna = pcr.adnFk")
    //   ->join(Individu::class, "specimen", "WITH", "specimen = dna.individuFk")
    //   ->leftJoin(EstAligneEtTraite::class, "assembly", "WITH", "assembly.chromatogrammeFk = chromato")
    //   ->join(
    //     SequenceAssemblee::class,
    //     "last_seq",
    //     "WITH",
    //     "assembly.sequenceAssembleeFk = last_seq.id and last_seq.dateCreationSqcAss = (
    //       SELECT MAX(all_seq.dateCreationSqcAss) FROM \App\Entity\SequenceAssemblee all_seq
    //       WHERE all_seq.id = assembly.sequenceAssembleeFk
    //     )"
    //   );
    // ->join(Voc::class, "seq_status", "WITH", "seq_status.id = last_seq.statutSqcAssVocFk");

    $entities_toshow = $qb
      ->select("chromato as chrom, chrom_quality.code as quality,
      pcr.codePcr, pcr.numPcr, pcr_gene.code as gene,
      dna.codeAdn, specimen.codeIndBiomol,
      seq_status.code as last_seq_status,
      last_seq.codeSqcAlignement,
      last_seq.codeSqcAss,
      last_seq.dateCreationSqcAss")
      ->join(Voc::class, "chrom_quality", "WITH", "chrom_quality = chromato.qualiteChromatoVocFk")
      ->join(Pcr::class, "pcr", "WITH", "pcr = chromato.pcrFk")
      ->join(Voc::class, "pcr_gene", "WITH", "pcr_gene = pcr.geneVocFk")
      ->join(Adn::class, "dna", "WITH", "dna = pcr.adnFk")
      ->join(Individu::class, "specimen", "WITH", "specimen = dna.individuFk")
      ->leftJoin(EstAligneEtTraite::class, "assemb", "WITH", "assemb.chromatogrammeFk = chromato")
      ->join(
        SequenceAssemblee::class,
        "last_seq",
        "WITH",
        "assemb.sequenceAssembleeFk = last_seq.id and last_seq.dateCreationSqcAss = (
          SELECT MAX(all_seq.dateCreationSqcAss) FROM \App\Entity\SequenceAssemblee all_seq
          WHERE all_seq.id = assemb.sequenceAssembleeFk
        )"
      )
      ->join(Voc::class, "seq_status", "WITH", "seq_status.id = last_seq.statutSqcAssVocFk")
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->getQuery()
      ->getResult();



    foreach ($entities_toshow as $key => $row) {
      $entity = $row["chrom"];
      $linkSqcAss = count($entity->getAssemblages()) > 0
        ? strval($entity->getId()) : '';
      $tab_toshow[] = array(
        "id" => $entity->getId(),
        "chromato.id" => $entity->getId(),
        "specimen.codeIndBiomol" => $row["codeIndBiomol"],
        "dna.codeAdn" => $row["codeAdn"],
        "chromato.codeChromato" => $entity->getCodeChromato(),
        "gene" => $row["gene"],
        "pcr.codePcr" => $row["codePcr"],
        "pcr.numPcr" => $row["numPcr"],
        "quality" => $row["quality"],
        "chromato.dateCre" => $entity->getDateCre()?->format('Y-m-d H:i:s'),
        "chromato.dateMaj" => $entity->getDateMaj()?->format('Y-m-d H:i:s'),
        "creation_user_name" => $entity->getUserCre(),
        "user_cre.user_full_name" =>
        $service->GetUserCreUserfullname($entity),
        "user_maj.user_full_name" =>
        $service->GetUserMajUserfullname($entity),
        "last_seq.codeSqcAss" => $row["codeSqcAss"],
        "last_seq_status" => $row["last_seq_status"],
        "last_seq.codeSqcAlignement" => $row["codeSqcAlignement"],
        "last_seq.dateCreationSqcAss" => $row["dateCreationSqcAss"]?->format('Y-m-d'),
        "linkSequenceassemblee" => $linkSqcAss,
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $total, // total data array
    ]);
  }

  /**
   * Creates a new chromatogramme entity.
   */
  #[IsGranted("ROLE_COLLABORATION")]
  #[Route("/new", name: "chromatogramme_new", methods: ["GET", "POST"])]
  public function newAction(Request $request) {
    $chromatogramme = new Chromatogramme();
    // check if the relational Entity (Pcr) is given and set the RelationalEntityFk for the new Entity
    if ($pcr_id = $request->get('idFk')) {
      $pcr = $this->getRepository(Pcr::class)->find($pcr_id);
      $chromatogramme->setPcrFk($pcr);
    }
    $form = $this->createForm('App\Form\ChromatogrammeType', $chromatogramme, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $this->entityManager->persist($chromatogramme);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogramme/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('chromatogramme_edit', array(
        'id' => $chromatogramme->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/chromatogramme/edit.html.twig', array(
      'chromatogramme' => $chromatogramme,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a chromatogramme entity.
   */
  #[Route("/{id}", name: "chromatogramme_show", methods: ["GET"])]
  public function showAction(Chromatogramme $chromatogramme) {
    $deleteForm = $this->createDeleteForm($chromatogramme);
    $editForm = $this->createForm('App\Form\ChromatogrammeType', $chromatogramme, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/chromatogramme/edit.html.twig', array(
      'chromatogramme' => $chromatogramme,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing chromatogramme entity.
   */
  #[Route("/{id}/edit", name: "chromatogramme_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, Chromatogramme $chromatogramme) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $chromatogramme->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    //
    $deleteForm = $this->createDeleteForm($chromatogramme);
    $editForm = $this->createForm('App\Form\ChromatogrammeType', $chromatogramme, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {

      $this->entityManager->persist($chromatogramme);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogramme/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/chromatogramme/edit.html.twig', array(
        'chromatogramme' => $chromatogramme,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/chromatogramme/edit.html.twig', array(
      'chromatogramme' => $chromatogramme,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a chromatogramme entity.
   */
  #[Route("/{id}", name: "chromatogramme_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, Chromatogramme $chromatogramme) {
    $form = $this->createDeleteForm($chromatogramme);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      try {
        $this->entityManager->remove($chromatogramme);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/chromatogramme/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('chromatogramme_index');
  }

  /**
   * Creates a form to delete a chromatogramme entity.
   *
   * @param Chromatogramme $chromatogramme The chromatogramme entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Chromatogramme $chromatogramme) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('chromatogramme_delete', array('id' => $chromatogramme->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
