<?php

namespace App\Controller\Core;

use App\Entity\Etablissement;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Etablissement controller.
 *
 * @Route("etablissement")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class EtablissementController extends AbstractController {
   /**
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
       }

  /**
   * Lists all etablissement entities.
   *
   * @Route("/", name="etablissement_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->doctrine->getManager();

    $etablissements = $em->getRepository('App:Etablissement')->findAll();

    return $this->render('Core/etablissement/index.html.twig', array(
      'etablissements' => $etablissements,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="etablissement_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->doctrine->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('etablissement.dateMaj' => 'desc', 'etablissement.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(etablissement.nomEtablissement) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Etablissement")
      ->createQueryBuilder('etablissement')
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
        "id" => $id,
        "etablissement.id" => $id,
        "etablissement.nomEtablissement" => $entity->getNomEtablissement(),
        "etablissement.dateCre" => $DateCre,
        "etablissement.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "etablissement.userCre" => $service->GetUserCreUserfullname($entity),
        "etablissement.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new etablissement entity.
   *
   * @Route("/new", name="etablissement_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function newAction(Request $request) {
    $etablissement = new Etablissement();
    $form = $this->createForm('App\Form\EtablissementType', $etablissement, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->doctrine->getManager();
      $em->persist($etablissement);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/etablissement/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('etablissement_edit', array(
        'id' => $etablissement->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/etablissement/edit.html.twig', array(
      'etablissement' => $etablissement,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a etablissement entity.
   *
   * @Route("/{id}", name="etablissement_show", methods={"GET"})
   */
  public function showAction(Etablissement $etablissement) {
    $deleteForm = $this->createDeleteForm($etablissement);
    $editForm = $this->createForm(
      'App\Form\EtablissementType',
      $etablissement,
      ['action_type' => Action::show->value]
    );

    return $this->render('Core/etablissement/edit.html.twig', array(
      'etablissement' => $etablissement,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing etablissement entity.
   *
   * @Route("/{id}/edit", name="etablissement_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function editAction(Request $request, Etablissement $etablissement) {
    $deleteForm = $this->createDeleteForm($etablissement);
    $editForm = $this->createForm(
      'App\Form\EtablissementType',
      $etablissement,
      ['action_type' => Action::edit->value]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->doctrine->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/etablissement/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/etablissement/edit.html.twig', array(
        'etablissement' => $etablissement,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/etablissement/edit.html.twig', array(
      'etablissement' => $etablissement,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a etablissement entity.
   *
   * @Route("/{id}", name="etablissement_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function deleteAction(Request $request, Etablissement $etablissement) {
    $form = $this->createDeleteForm($etablissement);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->doctrine->getManager();
      try {
        $em->remove($etablissement);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/etablissement/index.html.twig',

          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('etablissement_index');
  }

  /**
   * Creates a form to delete a etablissement entity.
   *
   * @param Etablissement $etablissement The etablissement entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Etablissement $etablissement) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'etablissement_delete',
        array('id' => $etablissement->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
