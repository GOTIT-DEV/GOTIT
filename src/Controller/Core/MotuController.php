<?php

namespace App\Controller\Core;

use App\Entity\Motu;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Motu controller.
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("motu")]
class MotuController extends EntityController {


  /**
   * Lists all motu entities.
   */
  #[Route("/", name: "motu_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/motu/index.html.twig');
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   */
  #[Route("/indexjson", name: "motu_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('motu.dateMaj' => 'desc', 'motu.id' => 'desc');

    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $toshow = $this->getRepository(Motu::class)
      ->createQueryBuilder('motu')
      ->where('LOWER(motu.libelleMotu) LIKE :criteriaLower')
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($toshow);
    $toshow = array_slice($toshow, $minRecord, $rowCount);
    foreach ($toshow as $entity) {
      $id = $entity->getId();
      $DateMotu = ($entity->getDateMotu() !== null)
        ? $entity->getDateMotu()->format('Y-m-d') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
        ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      //  concatenated list of people
      $query = $this->entityManager->createQuery(
        'SELECT p.nomPersonne as nom FROM App:MotuEstGenerePar megp
				JOIN megp.personneFk p WHERE megp.motuFk = ' . $id
      )->getResult();
      $arrayListePersonne = array();
      foreach ($query as $taxon) {
        $arrayListePersonne[] = $taxon['nom'];
      }
      $listePersonne = implode(", ", $arrayListePersonne);
      //
      $tab_toshow[] = array(
        "id" => $id, "motu.id" => $id,
        "motu.libelleMotu" => $entity->getLibelleMotu(),
        "motu.nomFichierCsv" => $entity->getNomFichierCsv(),
        "listePersonne" => $listePersonne,
        "motu.commentaireMotu" => $entity->getCommentaireMotu(),
        "motu.dateMotu" => $DateMotu,
        "motu.dateCre" => $DateCre, "motu.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "motu.userCre" => $service->GetUserCreUserfullname($entity),
        "motu.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new motu entity.
   */
  #[Route("/new", name: "motu_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function newAction(Request $request) {
    $motu = new Motu();
    $form = $this->createForm('App\Form\MotuType', $motu, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($motu);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute(
        'motu_edit',
        array('id' => $motu->getId(), 'valid' => 1)
      );
    }

    return $this->render('Core/motu/edit.html.twig', array(
      'motu' => $motu,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a motu entity.
   */
  #[Route("/{id}", name: "motu_show", methods: ["GET"])]
  public function showAction(Motu $motu) {
    $deleteForm = $this->createDeleteForm($motu);
    $editForm = $this->createForm('App\Form\MotuType', $motu, [
      'action_type' => Action::show->value,
    ]);

    return $this->render('Core/motu/edit.html.twig', array(
      'motu' => $motu,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing motu entity.
   */
  #[Route("/{id}/edit", name: "motu_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function editAction(Request $request, Motu $motu, GenericFunctionE3s $service) {
    // store ArrayCollection
    $motuEstGenerePars = $service->setArrayCollection('MotuEstGenerePars', $motu);

    $deleteForm = $this->createDeleteForm($motu);
    $editForm = $this->createForm('App\Form\MotuType', $motu, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $service->DelArrayCollection('MotuEstGenerePars', $motu, $motuEstGenerePars);
      $this->entityManager->persist($motu);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/motu/edit.html.twig', array(
        'motu' => $motu,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }
    return $this->render('Core/motu/edit.html.twig', array(
      'motu' => $motu,
      'edit_form' => $editForm->createView(),
    ));
  }

  /**
   * Deletes a motu entity.
   */
  #[Route("/{id}", name: "motu_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_ADMIN")]
  public function deleteAction(Request $request, Motu $motu) {
    $form = $this->createDeleteForm($motu);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      try {
        $this->entityManager->remove($motu);
        $this->entityManager->flush();
      } catch (\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('motu_index');
  }

  /**
   * Creates a form to delete a motu entity.
   *
   * @param Motu $motu The motu entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Motu $motu) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('motu_delete', array('id' => $motu->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * List available datasets as JSON
   *
   */
  #[Route("/json/list", name: "datasets-list", methods: ["GET"])]
  public function datasetList(SerializerInterface $serializer) {
    $datasets = $this->getRepository(Motu::class)->findAll();
    $datasetsSerialized = $serializer->serialize(
      $datasets,
      "json",
      ["groups" => "motu"]
    );
    return JsonResponse::fromJsonString($datasetsSerialized);
  }

  /**
   * List all datasets and methods
   */
  #[Route("/json/methods", name: "methods-list", methods: ["GET"])]
  public function datasetMethodList() {
    $qb = $this->entityManager->createQueryBuilder();
    $query = $qb
      ->select('v.id method_id, v.code method_code, m.id as dataset_id, m.libelleMotu as dataset_name')
      ->from('App:Motu', 'm')
      ->join('App:Assigne', 'a', 'WITH', 'a.motuFk=m')
      ->join('App:Voc', 'v', 'WITH', "a.methodeMotuVocFk=v AND v.code != 'HAPLO'")
      ->distinct()
      ->getQuery();

    return new JsonResponse($query->getArrayResult());
  }
}
