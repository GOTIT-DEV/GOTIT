<?php

namespace App\Controller\Core;

use App\Entity\Dna;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dna controller.
 *
 * @Route("dna")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class DnaController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all adn entities.
   *
   * @Route("/", name="adn_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $items = $em->getRepository('App:Dna')->findAll();

    return $this->render('Core/dna/index.html.twig', ['adns' => $items]);
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="adn_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('dna.id, dna.codeAdn as code')->from('App:Dna', 'dna');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(dna.codeAdn) like :q' . $i . ')')
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
   *
   * @Route("/indexjson", name="adn_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $em = $this->getDoctrine()->getManager();

    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'dna.dateMaj' => 'desc',
      'dna.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $where = 'LOWER(dna.codeAdn) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND dna.specimenFk = ' . $request->get('idFk');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Dna")->createQueryBuilder('dna')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin('App:Specimen', 'specimen', 'WITH', 'dna.specimenFk = specimen.id')
      ->leftJoin('App:Store', 'store', 'WITH', 'dna.storeFk = store.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateAdn = $entity->getDateAdn()
      ? $entity->getDateAdn()->format('Y-m-d') : null;
      $codeBoite = $entity->getStoreFk()
      ? $entity->getStoreFk()->getCodeBoite() : null;
      $DateMaj = $entity->getDateMaj()
      ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = $entity->getDateCre()
      ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;

      // search the PCRs from the DNA
      $query = $em->createQuery(
        'SELECT pcr.id FROM App:Pcr pcr WHERE pcr.adnFk = ' . $id
      )->getResult();
      $linkPcr = (count($query) > 0) ? $id : '';

      // concatenated list of people
      $query = $em->createQuery(
        'SELECT p.nomPersonne as nom
        FROM App:DnaExtraction erp
        JOIN erp.personFk p
        WHERE erp.adnFk = ' . $id
      )->getResult();
      $arrayListePerson = array();
      foreach ($query as $taxon) {
        $arrayListePerson[] = $taxon['nom'];
      }
      $listePerson = implode(", ", $arrayListePerson);

      $tab_toshow[] = array(
        "id" => $id,
        "adn.id" => $id,
        "specimen.codeIndBiomol" => $entity->getSpecimenFk()->getCodeIndBiomol(),
        "adn.codeAdn" => $entity->getCodeAdn(),
        "listePerson" => $listePerson,
        "adn.dateAdn" => $DateAdn,
        "store.codeBoite" => $codeBoite,
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
      "total" => $nb, // total data array
    ]);
  }

  /**
   * Creates a new adn entity.
   *
   * @Route("/new", name="adn_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $adn = new Dna();
    $em = $this->getDoctrine()->getManager();

    if ($specimen_id = $request->get('idFk')) {
      $specimen = $em->getRepository('App:Specimen')->find($specimen_id);
      $adn->setSpecimenFk($specimen);
    }

    $form = $this->createForm('App\Form\DnaType', $adn, [
      'action_type' => Action::create(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $em->persist($adn);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->redirectToRoute('adn_edit', array(
        'id' => $adn->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/dna/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a adn entity.
   *
   * @Route("/{id}", name="adn_show", methods={"GET"})
   */
  public function showAction(Dna $adn) {
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\DnaType', $adn, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/dna/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing adn entity.
   *
   * @Route("/{id}/edit", name="adn_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Dna $adn, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $adn->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $dnaExtractions = $service->setArrayCollection('DnaExtractions', $adn);
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\DnaType', $adn, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $service->DelArrayCollection('DnaExtractions', $adn, $dnaExtractions);
      $em = $this->getDoctrine()->getManager();
      $em->persist($adn);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/dna/edit.html.twig', array(
        'adn' => $adn,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/dna/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a adn entity.
   *
   * @Route("/{id}", name="adn_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Dna $adn) {
    $form = $this->createDeleteForm($adn);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($adn);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('adn_index');
  }

  /**
   * Creates a form to delete a adn entity.
   *
   * @param Dna $adn The adn entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Dna $adn) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('adn_delete', ['id' => $adn->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }
}
