<?php

namespace App\Controller\Core;

use App\Controller\EntityController;
use App\Entity\Voc;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Voc controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("voc")]
class VocController extends EntityController {

  /**
   * Lists all voc entities.
   */
  #[Route("/", name: "voc_index", methods: ["GET"])]
  public function indexAction() {

    $vocs = $this->getRepository(Voc::class)->findAll();

    return $this->render('Core/voc/index.html.twig', array(
      'vocs' => $vocs,
    ));
  }

  /**
   * List voc in a parent category
   */
  #[Route("/parent/{parent}", name: "list_voc", methods: ["GET"])]
  public function listVoc(String $parent, SerializerInterface $serializer) {

    $voc = $this->getRepository(Voc::class)->findByParent($parent);

    return JsonResponse::fromJsonString(
      $serializer->serialize($voc, "json")
    );
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "voc_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service, TranslatorInterface $translator) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('voc.dateMaj' => 'desc', 'voc.id' => 'desc');

    $minRecord = intval($request->get('current') - 1) * $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $this->getRepository(Voc::class)
      ->createQueryBuilder('voc')
      ->where('LOWER(voc.libelle) LIKE :criteriaLower')
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
        "id" => $id, "voc.id" => $id,
        "voc.code" => $entity->getCode(),
        "voc.libelle" => $entity->getLibelle(),
        "voc.libelleSecondLanguage" => $translator->trans($entity->getLibelle()),
        "voc.parent" => $translator->trans('vocParent.' . $entity->getParent()),
        "voc.parentCode" => $entity->getParent(),
        "voc.dateCre" => $DateCre, "voc.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "voc.userCre" => $service->GetUserCreUserfullname($entity),
        "voc.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new voc entity.
   */
  #[Route("/new", name: "voc_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function newAction(Request $request) {
    $voc = new Voc();
    $form = $this->createForm('App\Form\VocType', $voc, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($voc);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/voc/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('voc_edit', array('id' => $voc->getId(), 'valid' => 1));
    }

    return $this->render('Core/voc/edit.html.twig', array(
      'voc' => $voc,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a voc entity.
   */
  #[Route("/{id}", name: "voc_show", methods: ["GET"])]
  public function showAction(Voc $voc) {
    $deleteForm = $this->createDeleteForm($voc);
    $editForm = $this->createForm('App\Form\VocType', $voc, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/voc/edit.html.twig', array(
      'voc' => $voc,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing voc entity.
   */
  #[Route("/{id}/edit", name: "voc_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function editAction(Request $request, Voc $voc) {
    $deleteForm = $this->createDeleteForm($voc);
    $editForm = $this->createForm('App\Form\VocType', $voc, [
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
        return $this->render(
          'Core/voc/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/voc/edit.html.twig', array(
        'voc' => $voc,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/voc/edit.html.twig', array(
      'voc' => $voc,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a voc entity.
   */
  #[Route("/{id}", name: "voc_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function deleteAction(Request $request, Voc $voc) {
    $form = $this->createDeleteForm($voc);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($voc);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/voc/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('voc_index');
  }

  /**
   * Creates a form to delete a voc entity.
   *
   * @param Voc $voc The voc entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Voc $voc) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('voc_delete', array('id' => $voc->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
