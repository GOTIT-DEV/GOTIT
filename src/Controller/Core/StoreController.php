<?php

namespace App\Controller\Core;

use App\Entity\Store;
use App\Entity\Voc;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Store controller.
 *
 * @Route("store")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class StoreController extends AbstractController {
  /**
   * Lists all store entities.
   *
   * @Route("/", name="store_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $stores = $em->getRepository('App:Store')->findAll();

    return $this->render('Core/store/index.html.twig', array(
      'stores' => $stores,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="store_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('store.metaUpdateDate' => 'desc', 'store.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(store.code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('typeBoite') == 'LAME' || $request->get('typeBoite') == 'ADN' || $request->get('typeBoite') == 'LOT') {
      $where .= " AND vocTypeBoite.code LIKE '" . $request->get('typeBoite') . "'";
    }
    // Search for the list to show InternalSequenceAssembly
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Store")
      ->createQueryBuilder('store')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin(
        'App:Voc',
        'vocCodeCollection',
        'WITH',
        'store.collectionCodeVocFk = vocCodeCollection.id'
      )
      ->leftJoin(
        'App:Voc',
        'vocTypeBoite',
        'WITH',
        'store.storageTypeVocFk = vocTypeBoite.id'
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
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null)
      ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $MetaCreationDate = ($entity->getMetaCreationDate() !== null)
      ? $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "store.id" => $id,
        "store.code" => $entity->getCode(),
        "vocCodeCollection.code" => $entity->getCollectionCodeVocFk()->getCode(),
        "store.label" => $entity->getLabel(),
        "vocCodeCollection.label" => $entity->getCollectionCodeVocFk()->getLabel(),
        "store.metaCreationDate" => $MetaCreationDate,
        "store.metaUpdateDate" => $MetaUpdateDate,
        "metaCreationUserId" => $service->GetMetaCreationUserId($entity),
        "store.metaCreationUser" => $service->GetMetaCreationUserUserfullname($entity),
        "store.metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
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
   * Creates a new store entity.
   *
   * @Route("/new", name="store_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {

    $store = new Store();

    if ($request->get("typeBoite")) {
      $storeTypeRepo = $this->getDoctrine()->getRepository(Voc::class);
      $storeType = $storeTypeRepo->findOneBy([
        'code' => $request->get('typeBoite'),
        'parent' => 'typeBoite',
      ]);
      $store->setStorageTypeVocFk($storeType);
    }

    $form = $this->createForm('App\Form\StoreType', $store, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($store);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/store/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('store_edit', array(
        'id' => $store->getId(),
        'valid' => 1,
        'typeBoite' => $request->get('typeBoite'),
      ));
    }

    return $this->render('Core/store/edit.html.twig', array(
      'store' => $store,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a store entity.
   *
   * @Route("/{id}", name="store_show", methods={"GET"})
   */
  public function showAction(Store $store) {
    $deleteForm = $this->createDeleteForm($store);
    $editForm = $this->createForm('App\Form\StoreType', $store, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/store/edit.html.twig', array(
      'store' => $store,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing store entity.
   *
   * @Route("/{id}/edit", name="store_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Store $store) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() == 'ROLE_COLLABORATION' &&
      $store->getMetaCreationUser() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    // load the Entity Manager
    $em = $this->getDoctrine()->getManager();

    $deleteForm = $this->createDeleteForm($store);
    $editForm = $this->createForm('App\Form\StoreType', $store, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em->persist($store);
      // flush
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/store/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      $editForm = $this->createForm('App\Form\StoreType', $store, [
        'action_type' => Action::edit(),
      ]);

      return $this->render('Core/store/edit.html.twig', array(
        'store' => $store,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/store/edit.html.twig', array(
      'store' => $store,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a store entity.
   *
   * @Route("/{id}", name="store_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Store $store) {
    $form = $this->createDeleteForm($store);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($store);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/store/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('store_index', array(
      'typeBoite' => $request->get('typeBoite'),
    ));
  }

  /**
   * Creates a form to delete a store entity.
   *
   * @param Store $store The store entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Store $store) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'store_delete',
        array('id' => $store->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
