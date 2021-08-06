<?php

namespace App\Controller\Core;

use App\Entity\Municipality;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Municipality controller.
 *
 * @Route("municipality")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class MunicipalityController extends AbstractController {
  /**
   * Lists all municipality entities.
   *
   * @Route("/", name="municipality_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $municipalities = $em->getRepository('App:Municipality')->findAll();

    return $this->render('Core/municipality/index.html.twig', array(
      'municipalities' => $municipalities,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="municipality_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('municipality.dateMaj' => 'desc', 'municipality.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(municipality.codeCommune) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Municipality")->createQueryBuilder('municipality')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin('App:Country', 'country', 'WITH', 'municipality.countryFk = country.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateCre = ($entity->getDateCre() !== null)
      ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
      ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id, "municipality.id" => $id,
        "municipality.codeCommune" => $entity->getCodeCommune(),
        "municipality.nomCommune" => $entity->getNomCommune(),
        "municipality.nomRegion" => $entity->getNomRegion(),
        "country.codePays" => $entity->getCountryFk()->getCodePays(),
        "municipality.dateCre" => $DateCre,
        "municipality.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "municipality.userCre" => $service->GetUserCreUserfullname($entity),
        "municipality.userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "total" => $nb, // total data array
    ]);
  }

  /**
   * Creates a new municipality entity.
   *
   * @Route("/new", name="municipality_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $municipality = new Municipality();
    $form = $this->createForm('App\Form\MunicipalityType', $municipality, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($municipality);
      try {
        $flush = $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/municipality/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('municipality_edit', array(
        'id' => $municipality->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/municipality/edit.html.twig', array(
      'municipality' => $municipality,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Creates a new municipality entity for modal windows
   *
   * @Route("/newmodal", name="municipality_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction(Request $request, $country_id = null) {
    $municipality = new Municipality();
    $form = $this->createForm('App\Form\MunicipalityType', $municipality, [
      'country_id' => $country_id,
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return new JsonResponse([
          'valid' => false,
          "form" => $this->render('modal-form.html.twig', [
            'entityname' => 'municipality',
            'form' => $form->createView(),
          ])->getContent(),
        ]);
      } else {
        $em = $this->getDoctrine()->getManager();
        $em->persist($municipality);

        try {
          $flush = $em->flush();
          $select_id = $municipality->getId();
          $select_name = $municipality->getCodeCommune();
          return new JsonResponse([
            'select_id' => $select_id,
            'select_name' => $select_name,
            'entityname' => 'municipality',
          ]);
        } catch (\Doctrine\DBAL\DBALException $e) {
          return new JsonResponse([
            'exception' => true,
            'exception_message' => $e->getMessage(),
            'entityname' => 'municipality',
          ]);
        }
      }
    }

    return $this->render('modal.html.twig', array(
      'entityname' => 'municipality',
      'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a municipality entity.
   *
   * @Route("/{id}", name="municipality_show", methods={"GET"})
   */
  public function showAction(Municipality $municipality) {
    $deleteForm = $this->createDeleteForm($municipality);

    $editForm = $this->createForm('App\Form\MunicipalityType', $municipality, [
      'action_type' => Action::show(),
    ]);
    return $this->render('Core/municipality/edit.html.twig', array(
      'municipality' => $municipality,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing municipality entity.
   *
   * @Route("/{id}/edit", name="municipality_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, Municipality $municipality) {
    $deleteForm = $this->createDeleteForm($municipality);
    $editForm = $this->createForm('App\Form\MunicipalityType', $municipality, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/municipality/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/municipality/edit.html.twig', array(
        'municipality' => $municipality,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/municipality/edit.html.twig', array(
      'municipality' => $municipality,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a municipality entity.
   *
   * @Route("/{id}", name="municipality_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, Municipality $municipality) {
    $form = $this->createDeleteForm($municipality);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($municipality);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/municipality/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('municipality_index');
  }

  /**
   * Creates a form to delete a municipality entity.
   *
   * @param Municipality $municipality The municipality entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Municipality $municipality) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('municipality_delete', array('id' => $municipality->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
