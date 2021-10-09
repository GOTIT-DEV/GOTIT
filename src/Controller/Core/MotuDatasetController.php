<?php

namespace App\Controller\Core;

use App\Entity\MotuDataset;
use App\Form\Enums\Action;
use App\Services\Core\EntityEditionService;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * MotuDataset controller.
 *
 * @Route("motu_dataset")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MotuDatasetController extends AbstractController {
  /**
   * Lists all motu entities.
   *
   * @Route("/", name="motu_dataset_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $motu_datasets = $em->getRepository('App:MotuDataset')->findAll();

    return $this->render('Core/motu_dataset/index.html.twig', array(
      'motu_datasets' => $motu_datasets,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="motu_dataset_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('motu_dataset.metaUpdateDate' => 'desc', 'motu_dataset.id' => 'desc');

    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $toshow = $em->getRepository("App:MotuDataset")->createQueryBuilder('motu_dataset')
      ->where('LOWER(motu_dataset.title) LIKE :criteriaLower')
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($toshow);
    $toshow = array_slice($toshow, $minRecord, $rowCount);
    foreach ($toshow as $entity) {
      $id = $entity->getId();
      $Date = ($entity->getDate() !== null)
      ? $entity->getDate()->format('Y-m-d') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null)
      ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $MetaCreationDate = ($entity->getMetaCreationDate() !== null)
      ? $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      //  concatenated list of people
      $query = $em->createQuery(
        'SELECT p.name as nom FROM App:MotuDelimiter megp
				JOIN megp.personFk p WHERE megp.dataset = ' . $id
      )->getResult();
      $arrayListePerson = array();
      foreach ($query as $taxon) {
        $arrayListePerson[] = $taxon['nom'];
      }
      $listePerson = implode(", ", $arrayListePerson);
      //
      $tab_toshow[] = array(
        "id" => $id, "motu_dataset.id" => $id,
        "motu_dataset.title" => $entity->getTitle(),
        "motu_dataset.filename" => $entity->getFilename(),
        "listePerson" => $listePerson,
        "motu_dataset.comment" => $entity->getComment(),
        "motu_dataset.date" => $Date,
        "motu_dataset.metaCreationDate" => $MetaCreationDate, "motu_dataset.metaUpdateDate" => $MetaUpdateDate,
        "metaCreationUserId" => $service->GetMetaCreationUserId($entity),
        "motu_dataset.metaCreationUser" => $service->GetMetaCreationUserUserfullname($entity),
        "motu_dataset.metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
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
   *
   * @Route("/new", name="motu_dataset_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $motu_dataset = new MotuDataset();
    $form = $this->createForm('App\Form\MotuDatasetType', $motu_dataset, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($motu_dataset);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu_dataset/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute(
        'motu_dataset_edit',
        array('id' => $motu_dataset->getId(), 'valid' => 1)
      );
    }

    return $this->render('Core/motu_dataset/edit.html.twig', array(
      'motu_dataset' => $motu_dataset,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a motu entity.
   *
   * @Route("/{id}", name="motu_dataset_show", methods={"GET"})
   */
  public function showAction(MotuDataset $motu_dataset) {
    $deleteForm = $this->createDeleteForm($motu_dataset);
    $editForm = $this->createForm('App\Form\MotuDatasetType', $motu_dataset, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/motu_dataset/edit.html.twig', array(
      'motu_dataset' => $motu_dataset,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing motu entity.
   *
   * @Route("/{id}/edit", name="motu_dataset_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, MotuDataset $motu_dataset, EntityEditionService $service) {
    // load service  generic_function_e3s
    //

    // store ArrayCollection
    $motuDelimiters = $service->copyArrayCollection($motu_dataset->getMotuDelimiters());

    //
    $deleteForm = $this->createDeleteForm($motu_dataset);
    $editForm = $this->createForm('App\Form\MotuDatasetType', $motu_dataset, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->removeStaleCollection($motuDelimiters, $motu_dataset->getMotuDelimiters());
      // flush
      $this->getDoctrine()->getManager()->persist($motu_dataset);
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu_dataset/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/motu_dataset/edit.html.twig', array(
        'motu_dataset' => $motu_dataset,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }
    return $this->render('Core/motu_dataset/edit.html.twig', array(
      'motu_dataset' => $motu_dataset,
      'edit_form' => $editForm->createView(),
    ));
  }

  /**
   * Deletes a motu entity.
   *
   * @Route("/{id}", name="motu_dataset_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, MotuDataset $motu_dataset) {
    $form = $this->createDeleteForm($motu_dataset);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($motu_dataset);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/motu_dataset/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('motu_dataset_index');
  }

  /**
   * Creates a form to delete a motu entity.
   *
   * @param MotuDataset $motu_dataset The motu entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(MotuDataset $motu_dataset) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('motu_dataset_delete', array('id' => $motu_dataset->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * List available datasets as JSON
   *
   * @Route("/json/list", name="datasets-list", methods={"GET"})
   */
  public function datasetList(SerializerInterface $serializer) {
    $datasets = $this->getDoctrine()->getRepository(MotuDataset::class)->findAll();
    $datasetsSerialized = $serializer->serialize(
      $datasets,
      "json",
      ["groups" => "motu_dataset"]
    );
    return JsonResponse::fromJsonString($datasetsSerialized);
  }

  /**
   * List all datasets and methods
   * @Route("/json/methods", name="methods-list", methods={"GET"})
   */
  public function datasetMethodList() {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $query = $qb
      ->select('v.id method_id, v.code method_code, m.id as dataset_id, m.title as dataset_name')
      ->from('App:MotuDataset', 'm')
      ->join('App:MotuDelimitation', 'a', 'WITH', 'a.dataset=m')
      ->join('App:Voc', 'v', 'WITH', "a.method=v AND v.code != 'HAPLO'")
      ->distinct()
      ->getQuery();

    return new JsonResponse($query->getArrayResult());
  }
}
