<?php

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Entity\Pcr;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Adn;

/**
 * Pcr controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("pcr")]
class PcrController extends EntityController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all pcr entities.
   */
  #[Route("/", name: "pcr_index", methods: ["GET"])]
  #[Route("/", name: "pcrchromato_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/pcr/index.html.twig');
  }

  #[Route("/search/{q}", requirements: ["q" => ".+"], name: "pcr_search")]
  public function searchAction($q) {
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('pcr.id, pcr.codePcr as code')
      ->from('App:Pcr', 'pcr');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(pcr.codePcr) like :q' . $i . ')');
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();

    return $this->json($results);
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "pcr_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'pcr.dateMaj' => 'desc', 'pcr.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }

    // Search for the list to show
    $tab_toshow = [];
    $query = $this->getRepository(Pcr::class)
      ->createQueryBuilder('pcr');
    if ($searchPhrase) {
      $query = $query
        ->where('LOWER(individu.codeIndBiomol) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($searchPhrase) . '%');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $query = $query->andWhere('pcr.adnFk = :adnFk')
        ->setParameter('adnFk', $request->get('idFk'));
    }
    $query = $query
      ->leftJoin('App:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
      ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id');

    $query_total = clone $query;
    $total = $query_total->select('count(pcr.id)')->getQuery()->getSingleScalarResult();

    $entities_toshow = $query
      ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
      ->leftJoin('App:Voc', 'vocQualitePcr', 'WITH', 'pcr.qualitePcrVocFk = vocQualitePcr.id')
      ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->getQuery()
      ->getResult();

    // build column content
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DatePcr = ($entity->getDatePcr() !== null)
        ? $entity->getDatePcr()->format('Y-m-d') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
        ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      // Search chromatograms associated to a PCR
      $query = $this->entityManager->createQuery(
        'SELECT chromato.id FROM App:Chromatogramme chromato
                WHERE chromato.pcrFk = ' . $id
      )->getResult();
      $linkChromatogramme = (count($query) > 0) ? $id : '';
      // concatenated list of people
      $query = $this->entityManager->createQuery(
        'SELECT p.nomPersonne as nom FROM App:PcrEstRealisePar erp
                JOIN erp.personneFk p WHERE erp.pcrFk = ' . $id
      )->getResult();
      $arrayListePersonne = array();
      foreach ($query as $taxon) {
        $arrayListePersonne[] = $taxon['nom'];
      }
      $listePersonne = implode(", ", $arrayListePersonne);
      //
      $tab_toshow[] = array(
        "id" => $id, "pcr.id" => $id,
        "individu.codeIndBiomol" => $entity->getAdnFk()->getIndividuFk()->getCodeIndBiomol(),
        "adn.codeAdn" => $entity->getAdnFk()->getCodeAdn(),
        "pcr.codePcr" => $entity->getCodePcr(),
        "pcr.numPcr" => $entity->getNumPcr(),
        "vocGene.code" => $entity->getGeneVocFk()->getCode(),
        "listePersonne" => $listePersonne,
        "pcr.datePcr" => $DatePcr,
        "vocQualitePcr.code" => $entity->getQualitePcrVocFk()->getCode(),
        "vocSpecificite.code" => $entity->getSpecificiteVocFk()->getCode(),
        "pcr.dateCre" => $DateCre,
        "pcr.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "pcr.userCre" => $service->GetUserCreUserfullname($entity),
        "pcr.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkChromatogramme" => $linkChromatogramme,
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
   * Creates a new pcr entity.
   */
  #[Route("/new", name: "pcr_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function newAction(Request $request) {
    $pcr = new Pcr();
    // check if the relational Entity (Adn) is given and set the RelationalEntityFk for the new Entity
    if ($dna_id = $request->get('idFk')) {
      $dna = $this->getRepository(Adn::class)->find($dna_id);
      $pcr->setAdnFk($dna);
    }
    $form = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $this->entityManager->persist($pcr);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('pcr_edit', array(
        'id' => $pcr->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/pcr/edit.html.twig', array(
      'pcr' => $pcr,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a pcr entity.
   */
  #[Route("/{id}", name: "pcr_show", methods: ["GET"])]
  public function showAction(Pcr $pcr) {
    $deleteForm = $this->createDeleteForm($pcr);
    $editForm = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/pcr/edit.html.twig', array(
      'pcr' => $pcr,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing pcr entity.
   */
  #[Route("/{id}/edit", name: "pcr_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, Pcr $pcr, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $pcr->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $pcrEstRealisePars = $service->setArrayCollection('PcrEstRealisePars', $pcr);
    $deleteForm = $this->createDeleteForm($pcr);
    $editForm = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::edit->value,
    ]);

    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {

      $service->DelArrayCollection('PcrEstRealisePars', $pcr, $pcrEstRealisePars);

      $this->entityManager->persist($pcr);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/pcr/edit.html.twig', array(
        'pcr' => $pcr,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/pcr/edit.html.twig', array(
      'pcr' => $pcr,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a pcr entity.
   */
  #[Route("/{id}", name: "pcr_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, Pcr $pcr) {
    $form = $this->createDeleteForm($pcr);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      try {
        $this->entityManager->remove($pcr);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('pcr_index');
  }

  /**
   * Creates a form to delete a pcr entity.
   *
   * @param Pcr $pcr The pcr entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Pcr $pcr) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('pcr_delete', array('id' => $pcr->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
