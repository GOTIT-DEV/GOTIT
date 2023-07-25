<?php

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Entity\Boite;
use App\Entity\Voc;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Boite controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("storage")]
class BoiteController extends EntityController {

  /**
   * Lists all boite entities.
   */
  #[Route("/", name: "boite_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/boite/index.html.twig');
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "boite_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('boite.dateMaj' => 'desc', 'boite.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(boite.codeBoite) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('typeBoite') == 'LAME' || $request->get('typeBoite') == 'ADN' || $request->get('typeBoite') == 'LOT') {
      $where .= " AND vocTypeBoite.code LIKE '" . $request->get('typeBoite') . "'";
    }
    // Search for the list to show EstAligneEtTraite
    $tab_toshow = [];
    $entities_toshow = $this->getRepository(Boite::class)
      ->createQueryBuilder('boite')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin(
        Voc::class,
        'vocCodeCollection',
        'WITH',
        'boite.codeCollectionVocFk = vocCodeCollection.id'
      )
      ->leftJoin(
        Voc::class,
        'vocTypeBoite',
        'WITH',
        'boite.typeBoiteVocFk = vocTypeBoite.id'
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
      //
      $tab_toshow[] = array(
        "id" => $id, "boite.id" => $id,
        "boite.codeBoite" => $entity->getCodeBoite(),
        "vocCodeCollection.code" => $entity->getCodeCollectionVocFk()->getCode(),
        "boite.libelleBoite" => $entity->getLibelleBoite(),
        "vocCodeCollection.libelle" => $entity->getCodeCollectionVocFk()->getLibelle(),
        "boite.dateCre" => $DateCre,
        "boite.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "boite.userCre" => $service->GetUserCreUserfullname($entity),
        "boite.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new boite entity.
   */
  #[IsGranted("ROLE_COLLABORATION")]
  #[Route("/new", name: "boite_new", methods: ["GET", "POST"])]
  public function newAction(Request $request) {

    $boite = new Boite();

    if ($request->get("typeBoite")) {
      $boxTypeRepo = $this->getRepository(Voc::class);
      $boxType = $boxTypeRepo->findOneBy([
        'code' => $request->get('typeBoite'),
        'parent' => 'typeBoite',
      ]);
      $boite->setTypeBoiteVocFk($boxType);
    }

    $form = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($boite);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('boite_edit', array(
        'id' => $boite->getId(),
        'valid' => 1,
        'typeBoite' => $request->get('typeBoite'),
      ));
    }

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a boite entity.
   */
  #[Route("/{id}", name: "boite_show", methods: ["GET"])]
  public function showAction(Boite $boite) {
    $deleteForm = $this->createDeleteForm($boite);
    $editForm = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing boite entity.
   */
  #[Route("/{id}/edit", name: "boite_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, Boite $boite) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $boite->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $deleteForm = $this->createDeleteForm($boite);
    $editForm = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $this->entityManager->persist($boite);
      // flush
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      $editForm = $this->createForm('App\Form\BoiteType', $boite, [
        'action_type' => Action::edit->value,
      ]);

      return $this->render('Core/boite/edit.html.twig', array(
        'boite' => $boite,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a boite entity.
   */
  #[Route("/{id}", name: "boite_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, Boite $boite) {
    $form = $this->createDeleteForm($boite);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($boite);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('boite_index', array(
      'typeBoite' => $request->get('typeBoite'),
    ));
  }

  /**
   * Creates a form to delete a boite entity.
   *
   * @param Boite $boite The boite entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Boite $boite) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'boite_delete',
        array('id' => $boite->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
