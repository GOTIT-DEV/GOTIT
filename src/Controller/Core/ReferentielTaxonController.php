<?php

namespace App\Controller\Core;

use App\Entity\ReferentielTaxon;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Referentieltaxon controller.
 *
 * @Route("referentieltaxon")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ReferentielTaxonController extends AbstractController {
      
   /**
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
       }

  /**
   * Lists all referentielTaxon entities.
   *
   * @Route("/", name="referentieltaxon_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->doctrine->getManager();

    $referentielTaxons = $em->getRepository('App:ReferentielTaxon')->findAll();

    return $this->render('Core/referentieltaxon/index.html.twig', array(
      'referentielTaxons' => $referentielTaxons,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="referentieltaxon_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->doctrine->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('referentielTaxon.dateMaj' => 'desc', 'referentielTaxon.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(referentielTaxon.taxname) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:ReferentielTaxon")
      ->createQueryBuilder('referentielTaxon')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null)
      ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
      ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "referentielTaxon.id" => $id,
        "referentielTaxon.taxname" => $entity->getTaxname(),
        "referentielTaxon.rank" => $entity->getRank(),
        "referentielTaxon.family" => $entity->getFamily(),
        "referentielTaxon.validity" => $entity->getValidity(),
        "referentielTaxon.codeTaxon" => $entity->getCodeTaxon(),
        "referentielTaxon.clade" => $entity->getClade(),
        "referentielTaxon.dateCre" => $DateCre,
        "referentielTaxon.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "referentielTaxon.userCre" => $service->GetUserCreUserfullname($entity),
        "referentielTaxon.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new referentielTaxon entity.
   *
   * @Route("/new", name="referentieltaxon_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $referentielTaxon = new Referentieltaxon();
    $form = $this->createForm('App\Form\ReferentielTaxonType', $referentielTaxon, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->doctrine->getManager();
      $em->persist($referentielTaxon);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/referentieltaxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('referentieltaxon_edit', array(
        'id' => $referentielTaxon->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/referentieltaxon/edit.html.twig', array(
      'referentielTaxon' => $referentielTaxon,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a referentielTaxon entity.
   *
   * @Route("/{id}", name="referentieltaxon_show", methods={"GET"})
   */
  public function showAction(ReferentielTaxon $referentielTaxon) {
    $deleteForm = $this->createDeleteForm($referentielTaxon);
    $editForm = $this->createForm(
      'App\Form\ReferentielTaxonType',
      $referentielTaxon,
      ['action_type' => Action::show()]
    );

    return $this->render('Core/referentieltaxon/edit.html.twig', array(
      'referentielTaxon' => $referentielTaxon,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing referentielTaxon entity.
   *
   * @Route("/{id}/edit", name="referentieltaxon_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, ReferentielTaxon $referentielTaxon) {
    $deleteForm = $this->createDeleteForm($referentielTaxon);
    $editForm = $this->createForm(
      'App\Form\ReferentielTaxonType',
      $referentielTaxon,
      ['action_type' => Action::edit()]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->doctrine->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/referentieltaxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/referentieltaxon/edit.html.twig', array(
        'referentielTaxon' => $referentielTaxon,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/referentieltaxon/edit.html.twig', array(
      'referentielTaxon' => $referentielTaxon,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a referentielTaxon entity.
   *
   * @Route("/{id}", name="referentieltaxon_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, ReferentielTaxon $referentielTaxon) {
    $form = $this->createDeleteForm($referentielTaxon);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->doctrine->getManager();
      try {
        $em->remove($referentielTaxon);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/referentieltaxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('referentieltaxon_index');
  }

  /**
   * Creates a form to delete a referentielTaxon entity.
   *
   * @param ReferentielTaxon $referentielTaxon The referentielTaxon entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(ReferentielTaxon $referentielTaxon) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('referentieltaxon_delete', array('id' => $referentielTaxon->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * @Route("/json/species-list", name="species-list", methods={"GET"})
   */
  public function listSpecies() {
    $qb = $this->doctrine->getManager()->createQueryBuilder();
    $query = $qb->select('rt')
    // index results by genus
      ->from('App:ReferentielTaxon', 'rt')
      ->where('rt.species IS NOT NULL')
      ->orderBy('rt.genus, rt.species')
      ->getQuery();
    return new JsonResponse($query->getArrayResult());
  }
}
