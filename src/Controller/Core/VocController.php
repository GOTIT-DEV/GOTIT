<?php

namespace App\Controller\Core;

use App\Entity\Voc;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Voc controller.
 *
 * @Route("voc")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class VocController extends AbstractController {
  /**
   * Lists all voc entities.
   *
   * @Route("/", name="voc_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $vocs = $em->getRepository('App:Voc')->findAll();

    return $this->render('Core/voc/index.html.twig', array(
      'vocs' => $vocs,
    ));
  }

  /**
   * List voc in a parent category
   * @Route("/parent/{parent}", name="list_voc", methods={"GET"})
   */
  public function listVoc(String $parent, SerializerInterface $serializer) {
    $voc = $this->getDoctrine()
      ->getRepository(Voc::class)
      ->findByParent($parent);
    return JsonResponse::fromJsonString(
      $serializer->serialize($voc, "json")
    );
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="voc_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service, TranslatorInterface $translator) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('voc.metaUpdateDate' => 'desc', 'voc.id' => 'desc');

    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Voc")->createQueryBuilder('voc')
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
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null)
      ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $MetaCreationDate = ($entity->getMetaCreationDate() !== null)
      ? $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "voc.id" => $id,
        "voc.code" => $entity->getCode(),
        "voc.libelle" => $entity->getLibelle(),
        "voc.libelleSecondLanguage" => $translator->trans($entity->getLibelle()),
        "voc.parent" => $translator->trans('vocParent.' . $entity->getParent()),
        "voc.parentCode" => $entity->getParent(),
        "voc.metaCreationDate" => $MetaCreationDate, "voc.metaUpdateDate" => $MetaUpdateDate,
        "metaCreationUserId" => $service->GetMetaCreationUserId($entity),
        "voc.metaCreationUser" => $service->GetMetaCreationUserUserfullname($entity),
        "voc.metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
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
   *
   * @Route("/new", name="voc_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $voc = new Voc();
    $form = $this->createForm('App\Form\VocType', $voc, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($voc);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
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
   *
   * @Route("/{id}", name="voc_show", methods={"GET"})
   */
  public function showAction(Voc $voc) {
    $deleteForm = $this->createDeleteForm($voc);
    $editForm = $this->createForm('App\Form\VocType', $voc, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/voc/edit.html.twig', array(
      'voc' => $voc,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing voc entity.
   *
   * @Route("/{id}/edit", name="voc_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, Voc $voc) {
    $deleteForm = $this->createDeleteForm($voc);
    $editForm = $this->createForm('App\Form\VocType', $voc, [
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
   *
   * @Route("/{id}", name="voc_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, Voc $voc) {
    $form = $this->createDeleteForm($voc);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($voc);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
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
