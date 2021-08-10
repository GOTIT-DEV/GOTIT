<?php

namespace App\Controller\Core;

use App\Entity\Institution;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Institution controller.
 *
 * @Route("institution")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class InstitutionController extends AbstractController {
  /**
   * Lists all institution entities.
   *
   * @Route("/", name="institution_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $institutions = $em->getRepository('App:Institution')->findAll();

    return $this->render('Core/institution/index.html.twig', array(
      'institutions' => $institutions,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="institution_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('institution.metaUpdateDate' => 'desc', 'institution.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(institution.name) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Institution")
      ->createQueryBuilder('institution')
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
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null)
      ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $MetaCreationDate = ($entity->getMetaCreationDate() !== null)
      ? $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      //
      $tab_toshow[] = array(
        "id" => $id,
        "institution.id" => $id,
        "institution.name" => $entity->getName(),
        "institution.metaCreationDate" => $MetaCreationDate,
        "institution.metaUpdateDate" => $MetaUpdateDate,
        "metaCreationUserId" => $service->GetMetaCreationUserId($entity),
        "institution.metaCreationUser" => $service->GetMetaCreationUserUserfullname($entity),
        "institution.metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
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
   * Creates a new institution entity.
   *
   * @Route("/new", name="institution_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function newAction(Request $request) {
    $institution = new Institution();
    $form = $this->createForm('App\Form\InstitutionType', $institution, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($institution);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/institution/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('institution_edit', array(
        'id' => $institution->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/institution/edit.html.twig', array(
      'institution' => $institution,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a institution entity.
   *
   * @Route("/{id}", name="institution_show", methods={"GET"})
   */
  public function showAction(Institution $institution) {
    $deleteForm = $this->createDeleteForm($institution);
    $editForm = $this->createForm(
      'App\Form\InstitutionType',
      $institution,
      ['action_type' => Action::show()]
    );

    return $this->render('Core/institution/edit.html.twig', array(
      'institution' => $institution,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing institution entity.
   *
   * @Route("/{id}/edit", name="institution_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function editAction(Request $request, Institution $institution) {
    $deleteForm = $this->createDeleteForm($institution);
    $editForm = $this->createForm(
      'App\Form\InstitutionType',
      $institution,
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
          'Core/institution/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/institution/edit.html.twig', array(
        'institution' => $institution,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/institution/edit.html.twig', array(
      'institution' => $institution,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a institution entity.
   *
   * @Route("/{id}", name="institution_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function deleteAction(Request $request, Institution $institution) {
    $form = $this->createDeleteForm($institution);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($institution);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/institution/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('institution_index');
  }

  /**
   * Creates a form to delete a institution entity.
   *
   * @param Institution $institution The institution entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Institution $institution) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'institution_delete',
        array('id' => $institution->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
