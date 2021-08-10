<?php

namespace App\Controller\Core;

use App\Entity\Taxon;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Taxon controller.
 *
 * @Route("taxon")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class TaxonController extends AbstractController {
  /**
   * Lists all taxon entities.
   *
   * @Route("/", name="taxon_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $taxons = $em->getRepository('App:Taxon')->findAll();

    return $this->render('Core/taxon/index.html.twig', array(
      'taxons' => $taxons,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="taxon_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('taxon.dateMaj' => 'desc', 'taxon.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(taxon.taxname) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Taxon")
      ->createQueryBuilder('taxon')
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
        "id" => $id, "taxon.id" => $id,
        "taxon.taxname" => $entity->getTaxname(),
        "taxon.rank" => $entity->getRank(),
        "taxon.family" => $entity->getFamily(),
        "taxon.validity" => $entity->getValidity(),
        "taxon.code" => $entity->getCode(),
        "taxon.clade" => $entity->getClade(),
        "taxon.dateCre" => $DateCre,
        "taxon.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "taxon.userCre" => $service->GetUserCreUserfullname($entity),
        "taxon.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new taxon entity.
   *
   * @Route("/new", name="taxon_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $taxon = new Taxon();
    $form = $this->createForm('App\Form\TaxonType', $taxon, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($taxon);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/taxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('taxon_edit', array(
        'id' => $taxon->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/taxon/edit.html.twig', array(
      'taxon' => $taxon,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a taxon entity.
   *
   * @Route("/{id}", name="taxon_show", methods={"GET"})
   */
  public function showAction(Taxon $taxon) {
    $deleteForm = $this->createDeleteForm($taxon);
    $editForm = $this->createForm(
      'App\Form\TaxonType',
      $taxon,
      ['action_type' => Action::show()]
    );

    return $this->render('Core/taxon/edit.html.twig', array(
      'taxon' => $taxon,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing taxon entity.
   *
   * @Route("/{id}/edit", name="taxon_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, Taxon $taxon) {
    $deleteForm = $this->createDeleteForm($taxon);
    $editForm = $this->createForm(
      'App\Form\TaxonType',
      $taxon,
      ['action_type' => Action::edit()]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/taxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/taxon/edit.html.twig', array(
        'taxon' => $taxon,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/taxon/edit.html.twig', array(
      'taxon' => $taxon,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a taxon entity.
   *
   * @Route("/{id}", name="taxon_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, Taxon $taxon) {
    $form = $this->createDeleteForm($taxon);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($taxon);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/taxon/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('taxon_index');
  }

  /**
   * Creates a form to delete a taxon entity.
   *
   * @param Taxon $taxon The taxon entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Taxon $taxon) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('taxon_delete', array('id' => $taxon->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * @Route("/json/species-list", name="species-list", methods={"GET"})
   */
  public function listSpecies() {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $query = $qb->select('rt')
    // index results by genus
      ->from('App:Taxon', 'rt')
      ->where('rt.species IS NOT NULL')
      ->orderBy('rt.genus, rt.species')
      ->getQuery();
    return new JsonResponse($query->getArrayResult());
  }
}
