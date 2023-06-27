<?php

namespace App\Controller\Core;

use App\Entity\Pays;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Country controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("pays")]
class PaysController extends EntityController {

  /**
   * Lists all country entities.
   */
  #[Route("/", name: "pays_index", methods: ["GET"])]
  public function indexAction() {
    $pays = $this->getRepository(Pays::class)->findAll();

    return $this->render('Core/pays/index.html.twig', array(
      'pays' => $pays,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "pays_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('pays.dateMaj' => 'desc', 'pays.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(pays.codePays) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $this->getRepository(Pays::class)->createQueryBuilder('pays')
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
        "id" => $id, "pays.id" => $id,
        "pays.codePays" => $entity->getCodePays(),
        "pays.nomPays" => $entity->getNomPays(),
        "pays.dateCre" => $DateCre,
        "pays.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "pays.userCre" => $service->GetUserCreUserfullname($entity),
        "pays.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new pay entity.
   */
  #[Route("/new", name: "pays_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function newAction(Request $request) {
    $pays = new Pays();
    $form = $this->createForm('App\Form\PaysType', $pays, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($pays);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/pays/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('pays_edit', array(
        'id' => $pays->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/pays/edit.html.twig', array(
      'pays' => $pays,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a pay entity.
   */
  #[Route("/{id}", name: "pays_show", methods: ["GET"])]
  public function showAction(Pays $pays) {
    $deleteForm = $this->createDeleteForm($pays);
    $editForm = $this->createForm('App\Form\PaysType', $pays, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/pays/edit.html.twig', array(
      'pays' => $pays,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing pay entity.
   */
  #[Route("/{id}/edit", name: "pays_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function editAction(Request $request, Pays $pays) {
    $deleteForm = $this->createDeleteForm($pays);
    $editForm = $this->createForm('App\Form\PaysType', $pays, [
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
          'Core/pays/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/pays/edit.html.twig', array(
        'pays' => $pays,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/pays/edit.html.twig', array(
      'pays' => $pays,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a pay entity.
   */
  #[Route("/{id}", name: "pays_delete", methods: ["DELETE"])]
  #[IsGranted("ROLE_ADMIN")]
  public function deleteAction(Request $request, Pays $pays) {
    $form = $this->createDeleteForm($pays);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($pays);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/pays/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('pays_index');
  }

  /**
   * Creates a form to delete a pay entity.
   *
   * @param Pays $pay The pay entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Pays $pays) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('pays_delete', array('id' => $pays->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * List all municipalities in a country
   */
  #[Route("/{id}/municipalities", name: "country_municipalities", methods: ["GET"])]
  public function listMunicipalities(Pays $country, SerializerInterface $serializer) {
    $json = $serializer->serialize($country->getCommunes(), "json", ['groups' => "own"]);
    return JsonResponse::fromJsonString($json);
  }
}
