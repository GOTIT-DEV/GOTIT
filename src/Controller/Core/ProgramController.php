<?php

namespace App\Controller\Core;

use App\Entity\Program;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Program controller.
 *
 * @Route("program")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ProgramController extends AbstractController {
  /**
   * Lists all program entities.
   *
   * @Route("/", name="program_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $programs = $em->getRepository('App:Program')->findAll();

    return $this->render('Core/program/index.html.twig', array(
      'programs' => $programs,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="program_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('program.dateMaj' => 'desc', 'program.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(program.codeProgramme) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Program")
      ->createQueryBuilder('program')
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
        "id" => $id, "program.id" => $id,
        "program.codeProgramme" => $entity->getCodeProgramme(),
        "program.typeFinanceur" => $entity->getTypeFinanceur(),
        "program.nomProgramme" => $entity->getNomProgramme(),
        "program.nomsResponsables" => $entity->getNomsResponsables(),
        "program.anneeDebut" => $entity->getAnneeDebut(),
        "program.anneeFin" => $entity->getAnneeFin(),
        "program.dateCre" => $DateCre,
        "program.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "program.userCre" => $service->GetUserCreUserfullname($entity),
        "program.userMaj" => $service->GetUserMajUserfullname($entity),
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
   * Creates a new program entity.
   *
   * @Route("/new", name="program_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function newAction(Request $request) {
    $program = new Program();
    $form = $this->createForm('App\Form\ProgramType', $program, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($program);
      try {
        $flush = $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/program/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->redirectToRoute('program_edit', array(
        'id' => $program->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/program/edit.html.twig', array(
      'program' => $program,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Creates a new program entity for modal windows
   *
   * @Route("/newmodal", name="program_newmodal", methods={"GET", "POST"})
   */
  public function newmodalAction(Request $request) {
    $program = new Program();
    $form = $this->createForm('App\Form\ProgramType', $program, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // flush des données du formulaire
      $em = $this->getDoctrine()->getManager();
      $em->persist($program);

      try {
        $flush = $em->flush();
        // mémorize the id and the name of the Program
        $select_id = $program->getId();
        $select_name = $program->getCodeProgramme();
        // return an empty Program Entity
        $program_new = new Program();
        $form = $this->createForm('App\Form\ProgramType', $program_new, [
          'action_type' => Action::create(),
        ]);
        //returns an empty form and the parameters of the new record created
        return new JsonResponse([
          'html_form' => $this->render('modal.html.twig', array('entityname' => 'program', 'form' => $form->createView()))->getContent(),
          'select_id' => $select_id,
          'select_name' => $select_name,
          'exception_message' => "",
          'entityname' => 'program',
        ]);
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = strval($e);
        // return an empty Program Entity
        $program_new = new Program();
        $form = $this->createForm('App\Form\ProgramType', $program_new);
        // returns a form with the error message
        return new JsonResponse([
          'html_form' => $this->render('modal.html.twig', array(
            'entityname' => 'program',
            'form' => $form->createView(),
          ))->getContent(),
          'select_id' => 0,
          'select_name' => "",
          'exception_message' => $exception_message,
          'entityname' => 'program',
        ]);
      }
    }

    return $this->render('modal.html.twig', array(
      'entityname' => 'program',
      'form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a program entity.
   *
   * @Route("/{id}", name="program_show", methods={"GET"})
   */
  public function showAction(Program $program) {
    $deleteForm = $this->createDeleteForm($program);
    $editForm = $this->createForm('App\Form\ProgramType', $program, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/program/edit.html.twig', array(
      'program' => $program,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing program entity.
   *
   * @Route("/{id}/edit", name="program_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function editAction(Request $request, Program $program) {
    //
    $deleteForm = $this->createDeleteForm($program);
    $editForm = $this->createForm('App\Form\ProgramType', $program, [
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
        return $this->render('Core/program/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->render('Core/program/edit.html.twig', array(
        'program' => $program,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/program/edit.html.twig', array(
      'program' => $program,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a program entity.
   *
   * @Route("/{id}", name="program_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_PROJECT')")
   */
  public function deleteAction(Request $request, Program $program) {
    $form = $this->createDeleteForm($program);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($program);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/program/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
    }

    return $this->redirectToRoute('program_index');
  }

  /**
   * Creates a form to delete a program entity.
   *
   * @param Program $program The program entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Program $program) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'program_delete',
        array('id' => $program->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
