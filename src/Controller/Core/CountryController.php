<?php

namespace App\Controller\Core;

use App\Entity\Country;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Pay controller.
 *
 * @Route("country")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class CountryController extends AbstractController {
  /**
   * Lists all pay entities.
   *
   * @Route("/", name="country_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $country = $em->getRepository('App:Country')->findAll();

    return $this->render('Core/country/index.html.twig', array(
      'country' => $country,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="country_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('country.dateMaj' => 'desc', 'country.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(country.codePays) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Country")->createQueryBuilder('country')
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
        "id" => $id, "country.id" => $id,
        "country.codePays" => $entity->getCodePays(),
        "country.nomPays" => $entity->getNomPays(),
        "country.dateCre" => $DateCre,
        "country.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "country.userCre" => $service->GetUserCreUserfullname($entity),
        "country.userMaj" => $service->GetUserMajUserfullname($entity),
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
   *
   * @Route("/new", name="country_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function newAction(Request $request) {
    $country = new Country();
    $form = $this->createForm('App\Form\CountryType', $country, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($country);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/country/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('country_edit', array(
        'id' => $country->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/country/edit.html.twig', array(
      'country' => $country,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a pay entity.
   *
   * @Route("/{id}", name="country_show", methods={"GET"})
   */
  public function showAction(Country $country) {
    $deleteForm = $this->createDeleteForm($country);
    $editForm = $this->createForm('App\Form\CountryType', $country, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/country/edit.html.twig', array(
      'country' => $country,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing pay entity.
   *
   * @Route("/{id}/edit", name="country_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function editAction(Request $request, Country $country) {
    $deleteForm = $this->createDeleteForm($country);
    $editForm = $this->createForm('App\Form\CountryType', $country, [
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
          'Core/country/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/country/edit.html.twig', array(
        'country' => $country,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/country/edit.html.twig', array(
      'country' => $country,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a pay entity.
   *
   * @Route("/{id}", name="country_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_ADMIN')")
   */
  public function deleteAction(Request $request, Country $country) {
    $form = $this->createDeleteForm($country);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($country);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/country/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('country_index');
  }

  /**
   * Creates a form to delete a country entity.
   *
   * @param Country $country The country entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Country $country) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('country_delete', array('id' => $country->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * List all municipalities in a country
   *
   * @Route("/{id}/municipalities", name="country_municipalities", methods={"GET"})
   */
  public function listMunicipalities(Country $country, SerializerInterface $serializer) {
    $json = $serializer->serialize($country->getMunicipalities(), "json", ['groups' => "own"]);
    return JsonResponse::fromJsonString($json);
  }
}
