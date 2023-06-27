<?php

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Entity\Personne;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Personne controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("personne")]
class PersonneController extends EntityController {

  /**
   * Lists all personne entities.
   */
  #[Route("/", name: "personne_index", methods: ["GET"])]
  public function indexAction() {
    $personnes = $this->getRepository(Personne::class)->findAll();
    return $this->render('Core/personne/index.html.twig', [
      'personnes' => $personnes,
    ]);
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "personne_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('personne.dateMaj' => 'desc', 'personne.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(personne.nomPersonne) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $this->entityManager
      ->getRepository(Personne::class)
      ->createQueryBuilder('personne')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin(
        'App:Etablissement',
        'etablissement',
        'WITH',
        'personne.etablissementFk = etablissement.id'
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
      $NomEtablissement = ($entity->getEtablissementFk() !== null)
        ? $entity->getEtablissementFk()->getNomEtablissement() : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "personne.id" => $id,
        "personne.nomPersonne" => $entity->getNomPersonne(),
        "personne.nomComplet" => $entity->getNomComplet(),
        "etablissement.nomEtablissement" => $NomEtablissement,
        "personne.dateCre" => $DateCre,
        "personne.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "personne.userCre" => $service->GetUserCreUserfullname($entity),
        "personne.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new personne entity.
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  #[Route("/new", name: "personne_new", methods: ["GET", "POST"])]
  public function newAction(Request $request) {
    $personne = new Personne();
    $form = $this->createForm('App\Form\PersonneType', $personne, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($personne);
      try {
        $flush = $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/personne/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->redirectToRoute('personne_edit', array(
        'id' => $personne->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/personne/edit.html.twig', array(
      'personne' => $personne,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Creates a new personne entity for modal windows
   */
  #[Route("/newmodal", name: "personne_newmodal", methods: ["GET", "POST"])]
  public function newmodalAction(Request $request) {
    $personne = new Personne();
    $form = $this->createForm('App\Form\PersonneType', $personne, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form" => $this->render('modal-form.html.twig', [
            'entityname' => 'personne',
            'form' => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $this->entityManager->persist($personne);

        try {
          $flush = $this->entityManager->flush();
          $select_id = $personne->getId();
          $select_name = $personne->getNomPersonne();
          // returns the parameters of the new record created
          return new JsonResponse([
            'select_id' => $select_id,
            'select_name' => $select_name,
            'entityname' => 'personne',
          ]);
        } catch (\Exception $e) {
          return new JsonResponse([
            'exception' => true,
            'exception_message' => $e->getMessage(),
            'entityname' => 'personne',
          ]);
        }
      }
    } else {
      return $this->render('modal.html.twig', array(
        'entityname' => 'personne',
        'form' => $form->createView(),
      ));
    }
  }

  /**
   * Finds and displays a personne entity.
   */
  #[Route("/{id}", name: "personne_show", methods: ["GET"])]
  public function showAction(Personne $personne) {
    $deleteForm = $this->createDeleteForm($personne);
    $editForm = $this->createForm('App\Form\PersonneType', $personne, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/personne/edit.html.twig', array(
      'personne' => $personne,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing personne entity.
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  #[Route("/{id}/edit", name: "personne_edit", methods: ["GET", "POST"])]
  public function editAction(Request $request, Personne $personne) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $personne->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    $deleteForm = $this->createDeleteForm($personne);
    $editForm = $this->createForm('App\Form\PersonneType', $personne, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/personne/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->render('Core/personne/edit.html.twig', array(
        'personne' => $personne,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/personne/edit.html.twig', array(
      'personne' => $personne,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a personne entity.
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  #[Route("/{id}", name: "personne_delete", methods: ["DELETE"])]
  public function deleteAction(Request $request, Personne $personne) {
    $form = $this->createDeleteForm($personne);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($personne);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/personne/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('personne_index');
  }

  /**
   * Creates a form to delete a personne entity.
   *
   * @param Personne $personne The personne entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Personne $personne) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('personne_delete', array('id' => $personne->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
