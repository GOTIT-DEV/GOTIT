<?php

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Entity\Adn;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Individu;
use App\Entity\Boite;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Adn controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("adn")]
class AdnController extends EntityController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all adn entities.
   */
  #[Route("/", name: "adn_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/adn/index.html.twig');
  }

  #[Route("/search/{q}", requirements: ["q" => ".+"], name: "adn_search")]
  public function searchAction($q) {
    $qb = $this->entityManager->createQueryBuilder();
    $qb->select('adn.id, adn.codeAdn as code')->from(Adn::class, 'adn');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(adn.codeAdn) like :q' . $i . ')')
        ->setParameter('q' . $i, $query[$i] . '%');
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
  #[Route("/indexjson", name: "adn_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {

    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'adn.dateMaj' => 'desc',
      'adn.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    $where = 'LOWER(adn.codeAdn) LIKE :criteriaLower';

    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND adn.individuFk = ' . $request->get('idFk');
    }
    // Search for the list to show
    $tab_toshow = [];
    $qb = $this->getRepository(Adn::class)
      ->createQueryBuilder('adn');
    if ($searchPhrase) {
      $qb = $qb
        ->where('LOWER(adn.codeAdn) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($searchPhrase) . '%');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $qb = $qb->andWhere('adn.individuFk = :individuFk')
        ->setParameter('individuFk', $request->get('idFk'));
    }
    $qb = $qb
      ->leftJoin(Individu::class, 'individu', 'WITH', 'adn.individuFk = individu.id')
      ->leftJoin(Boite::class, 'boite', 'WITH', 'adn.boiteFk = boite.id');

    $query_total = clone $qb;
    $total = $query_total->select('count(adn.id)')->getQuery()->getSingleScalarResult();

    $entities_toshow = $qb
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->getQuery()
      ->getResult();


    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateAdn = $entity->getDateAdn()
        ? $entity->getDateAdn()->format('Y-m-d') : null;
      $codeBoite = $entity->getBoiteFk()
        ? $entity->getBoiteFk()->getCodeBoite() : null;
      $DateMaj = $entity->getDateMaj()
        ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = $entity->getDateCre()
        ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;

      // search the PCRs from the DNA
      $query = $this->entityManager->createQuery(
        'SELECT pcr.id FROM App:Pcr pcr WHERE pcr.adnFk = ' . $id
      )->getResult();
      $linkPcr = (count($query) > 0) ? $id : '';

      // concatenated list of people
      $query = $this->entityManager->createQuery(
        'SELECT p.nomPersonne as nom
        FROM App:AdnEstRealisePar erp
        JOIN erp.personneFk p
        WHERE erp.adnFk = ' . $id
      )->getResult();
      $arrayListePersonne = array();
      foreach ($query as $taxon) {
        $arrayListePersonne[] = $taxon['nom'];
      }
      $listePersonne = implode(", ", $arrayListePersonne);

      $tab_toshow[] = array(
        "id" => $id,
        "adn.id" => $id,
        "individu.codeIndBiomol" => $entity->getIndividuFk()->getCodeIndBiomol(),
        "adn.codeAdn" => $entity->getCodeAdn(),
        "listePersonne" => $listePersonne,
        "adn.dateAdn" => $DateAdn,
        "boite.codeBoite" => $codeBoite,
        "adn.dateCre" => $DateCre,
        "adn.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "adn.userCre" => $service->GetUserCreUserfullname($entity),
        "adn.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkPcr" => $linkPcr,
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
   * Creates a new adn entity.
   */
  #[Route("/new", name: "adn_new", methods: ["GET", "POST"])]
  #[IsGranted('ROLE_COLLABORATION')]
  public function newAction(Request $request) {
    $adn = new Adn();

    if ($specimen_id = $request->get('idFk')) {
      $specimen = $this->getRepository(Individu::class)->find($specimen_id);
      $adn->setIndividuFk($specimen);
    }

    $form = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::create->value,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $this->entityManager->persist($adn);

      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/adn/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->redirectToRoute('adn_edit', array(
        'id' => $adn->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a adn entity.
   */
  #[Route("/{id}", name: "adn_show", methods: ["GET"])]
  public function showAction(Adn $adn) {
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing adn entity.
   *
   */
  #[Route("/{id}/edit", name: "adn_edit", methods: ["GET", "POST"])]
  #[IsGranted('ROLE_COLLABORATION')]
  public function editAction(Request $request, Adn $adn, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $adn->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $adnEstRealisePars = $service->setArrayCollection('AdnEstRealisePars', $adn);
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $service->DelArrayCollection('AdnEstRealisePars', $adn, $adnEstRealisePars);
      $this->entityManager->persist($adn);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/adn/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/adn/edit.html.twig', array(
        'adn' => $adn,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a adn entity.
   */
  #[IsGranted('ROLE_COLLABORATION')]
  #[Route("/{id}", name: "adn_delete", methods: ["DELETE", "POST"])]
  public function deleteAction(Request $request, Adn $adn) {
    $form = $this->createDeleteForm($adn);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      try {
        $this->entityManager->remove($adn);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/adn/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('adn_index');
  }

  /**
   * Creates a form to delete a adn entity.
   *
   * @param Adn $adn The adn entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Adn $adn) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('adn_delete', ['id' => $adn->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }
}
