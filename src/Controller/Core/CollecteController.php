<?php

namespace App\Controller\Core;

use App\Entity\Collecte;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use App\Entity\Station;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\Mapping\Entity;

/**
 * Collecte controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("collecte")]
class CollecteController extends EntityController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all collecte entities.
   */
  #[Route("/", name: "collecte_index", methods: ["GET", "POST"])]
  public function indexAction() {
    return $this->render('Core/collecte/index.html.twig');
  }

  #[Route("/search/{q}", requirements: ["q" => ".+"], name: "collecte_search")]
  public function searchAction($q) {
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('collecte.id, collecte.codeCollecte as code')
      ->from('App:Collecte', 'collecte');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(collecte.codeCollecte) like :q' . $i . ')');
    }
    for ($i = 0; $i < count($query); $i++) {
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
   *
   */
  #[Route("/indexjson", name: "collecte_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'collecte.dateMaj' => 'desc',
      'collecte.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;

    // initializes the searchPhrase variable
    $where = 'LOWER(collecte.codeCollecte) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND collecte.stationFk  = ' . $request->get('idFk');
    }
    // Search the list to show
    $tab_toshow = [];
    $qb = $this->getRepository(Collecte::class)
      ->createQueryBuilder('collecte');
    if ($searchPhrase) {
      $qb = $qb->where('LOWER(collecte.codeCollecte) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($searchPhrase) . '%');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $qb->where('collecte.stationFk = :station')
        ->setParameter('station', $request->get('idFk'));
    }

    $query_total = clone $qb;
    $total = $query_total->select('count(collecte.id)')
      ->getQuery()
      ->getSingleScalarResult();

    $entities_toshow = $qb
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->getQuery()
      ->getResult();

    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateCollecte = ($entity->getDateCollecte() !== null)
        ? $entity->getDateCollecte()->format('Y-m-d') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
        ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      // search for material associated with a sampling
      $query = $this->entityManager->createQuery(
        'SELECT lot.id FROM App:LotMateriel lot WHERE lot.collecteFk = ' . $id
      )->getResult();
      $linkLotmaterielFk = (count($query) > 0) ? $id : '';
      // search for external material associated with a sampling
      $query = $this->entityManager->createQuery(
        'SELECT lotext.id FROM App:LotMaterielExt lotext WHERE lotext.collecteFk = ' . $id
      )->getResult();
      $linkLotmaterielextFk = (count($query) > 0) ? $id : '';
      // search for external sequence associated with a sampling
      $query = $this->entityManager->createQuery(
        'SELECT sqcext.id FROM App:SequenceAssembleeExt sqcext WHERE sqcext.collecteFk = ' . $id
      )->getResult();
      $linkSequenceassembleeextFk = (count($query) > 0) ? $id : '';
      // Search for the concatenated list of targeted taxa
      $query = $this->entityManager->createQuery(
        'SELECT rt.taxname as taxname FROM App:ACibler ac JOIN ac.referentielTaxonFk rt WHERE ac.collecteFk = ' . $id
      )->getResult();
      $arrayTaxonsCibler = array();
      foreach ($query as $taxon) {
        $arrayTaxonsCibler[] = $taxon['taxname'];
      }
      $listeTaxonsCibler = implode(", ", $arrayTaxonsCibler);

      $tab_toshow[] = array(
        "id" => $id,
        "collecte.id" => $id,
        "collecte.codeCollecte" => $entity->getCodeCollecte(),
        "station.codeStation" => $entity->getStationFk()->getCodeStation(),
        "pays.nomPays" => $entity->getStationFk()->getPaysFk()->getNomPays(),
        "commune.codeCommune" => $entity->getStationFk()->getCommuneFk()->getCodeCommune(),
        "collecte.legVocFk" => $entity->getLegVocFk()->getCode(),
        "collecte.dateCollecte" => $DateCollecte,
        "collecte.aFaire" => $entity->getAfaire(),
        "collecte.dateCre" => $DateCre,
        "collecte.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "collecte.userCre" => $service->GetUserCreUserfullname($entity),
        "collecte.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkLotmateriel" => $linkLotmaterielFk,
        "linkLotmaterielext" => $linkLotmaterielextFk,
        "linkSequenceassembleeext" => $linkSequenceassembleeextFk,
        "listeTaxonsCibler" => $listeTaxonsCibler,
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $total,
    ]);
  }

  /**
   * Creates a new collecte entity.
   */
  #[Route("/new", name: "collecte_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function newAction(Request $request) {
    $collecte = new Collecte();
    // check if the relational Entity (Station) is given
    if ($site_id = $request->get('idFk')) {
      // set the RelationalEntityFk for the new Entity
      $site = $this->getRepository(Station::class)->find($site_id);
      $collecte->setStationFk($site);
    }

    $form = $this->createForm('App\Form\CollecteType', $collecte, [
      'action_type' => Action::create->value,
    ]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($collecte);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/collecte/index.html.twig', [
          'exception_message' => explode("\n", $exception_message)[0],
        ]);
      }
      return $this->redirectToRoute('collecte_edit', [
        'id' => $collecte->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    // Initial form render or form invalid
    return $this->render('Core/collecte/edit.html.twig', array(
      'collecte' => $collecte,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a collecte entity.
   */
  #[Route("/{id}", name: "collecte_show", methods: ["GET"])]
  public function showAction(Collecte $collecte) {
    $deleteForm = $this->createDeleteForm($collecte);
    $showForm = $this->createForm('App\Form\CollecteType', $collecte, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/collecte/edit.html.twig', [
      'collecte' => $collecte,
      'edit_form' => $showForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing collecte entity.
   */
  #[Route("/{id}/edit", name: "collecte_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, Collecte $collecte, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $collecte->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $originalAPourSamplingMethods = $service->setArrayCollection('APourSamplingMethods', $collecte);
    $originalAPourFixateurs = $service->setArrayCollection('APourFixateurs', $collecte);
    $originalEstFinancePars = $service->setArrayCollection('EstFinancePars', $collecte);
    $originalEstEffectuePars = $service->setArrayCollection('EstEffectuePars', $collecte);
    $originalACiblers = $service->setArrayCollection('ACiblers', $collecte);

    // editAction
    $deleteForm = $this->createDeleteForm($collecte);
    $editForm = $this->createForm('App\Form\CollecteType', $collecte, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection('APourSamplingMethods', $collecte, $originalAPourSamplingMethods);
      $service->DelArrayCollection('APourFixateurs', $collecte, $originalAPourFixateurs);
      $service->DelArrayCollection('EstFinancePars', $collecte, $originalEstFinancePars);
      $service->DelArrayCollection('EstEffectuePars', $collecte, $originalEstEffectuePars);
      $service->DelArrayCollection('ACiblers', $collecte, $originalACiblers);

      // flush
      $this->entityManager->persist($collecte);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/collecte/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/collecte/edit.html.twig', array(
        'collecte' => $collecte,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/collecte/edit.html.twig', array(
      'collecte' => $collecte,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a collecte entity.
   */
  #[Route("/{id}", name: "collecte_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, Collecte $collecte) {
    $form = $this->createDeleteForm($collecte);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      try {
        $this->entityManager->remove($collecte);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/collecte/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('collecte_index');
  }

  /**
   * Creates a form to delete a collecte entity.
   *
   * @param Collecte $collecte The collecte entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Collecte $collecte) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('collecte_delete', array('id' => $collecte->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
