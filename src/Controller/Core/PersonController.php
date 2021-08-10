<?php

namespace App\Controller\Core;

use App\Entity\Person;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Person controller.
 *
 * @Route("person")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class PersonController extends AbstractController {
  /**
   * Lists all person entities.
   *
   * @Route("/", name="person_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $persons = $em->getRepository('App:Person')->findAll();
    return $this->render('Core/person/index.html.twig', [
      'persons' => $persons,
    ]);
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="person_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();

    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('person.dateMaj' => 'desc', 'person.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(person.name) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Person")
      ->createQueryBuilder('person')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin(
        'App:Institution',
        'institution',
        'WITH',
        'person.institutionFk = institution.id'
      )
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
      $Name = ($entity->getInstitutionFk() !== null)
      ? $entity->getInstitutionFk()->getName() : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "person.id" => $id,
        "person.name" => $entity->getName(),
        "person.fullName" => $entity->getFullName(),
        "institution.name" => $Name,
        "person.dateCre" => $DateCre,
        "person.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "person.userCre" => $service->GetUserCreUserfullname($entity),
        "person.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new person entity.
   *
   * @Route("/new", name="person_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $person = new Person();
    $form = $this->createForm('App\Form\PersonType', $person, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($person);
      try {
        $flush = $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/person/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->redirectToRoute('person_edit', array(
        'id' => $person->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/person/edit.html.twig', array(
      'person' => $person,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Creates a new person entity for modal windows
   *
   * @Route("/newmodal", name="person_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction(Request $request) {
    $person = new Person();
    $form = $this->createForm('App\Form\PersonType', $person, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form" => $this->render('modal-form.html.twig', [
            'entityname' => 'person',
            'form' => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $em = $this->getDoctrine()->getManager();
        $em->persist($person);

        try {
          $flush = $em->flush();
          $select_id = $person->getId();
          $select_name = $person->getName();
          // returns the parameters of the new record created
          return new JsonResponse([
            'select_id' => $select_id,
            'select_name' => $select_name,
            'entityname' => 'person',
          ]);
        } catch (\Doctrine\DBAL\DBALException $e) {
          return new JsonResponse([
            'exception' => true,
            'exception_message' => $e->getMessage(),
            'entityname' => 'person',
          ]);
        }
      }
    } else {
      return $this->render('modal.html.twig', array(
        'entityname' => 'person',
        'form' => $form->createView(),
      ));
    }
  }

  /**
   * Finds and displays a person entity.
   *
   * @Route("/{id}", name="person_show", methods={"GET"})
   */
  public function showAction(Person $person) {
    $deleteForm = $this->createDeleteForm($person);
    $editForm = $this->createForm('App\Form\PersonType', $person, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/person/edit.html.twig', array(
      'person' => $person,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing person entity.
   *
   * @Route("/{id}/edit", name="person_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Person $person) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $person->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    $deleteForm = $this->createDeleteForm($person);
    $editForm = $this->createForm('App\Form\PersonType', $person, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/person/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->render('Core/person/edit.html.twig', array(
        'person' => $person,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/person/edit.html.twig', array(
      'person' => $person,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a person entity.
   *
   * @Route("/{id}", name="person_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Person $person) {
    $form = $this->createDeleteForm($person);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($person);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/person/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('person_index');
  }

  /**
   * Creates a form to delete a person entity.
   *
   * @param Person $person The person entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Person $person) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('person_delete', array('id' => $person->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
