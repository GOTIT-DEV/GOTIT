<?php

namespace App\Controller\Core;

use App\Entity\Dna;
use App\Form\Enums\Action;
use App\Services\Core\EntityEditionService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Dna controller.
 *
 * @Route("dna")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class DnaController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all dna entities.
   *
   * @Route("/", name="dna_index", methods={"GET"})
   */
  public function indexAction() {
    return $this->render('Core/dna/index.html.twig');
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="dna_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('dna.id, dna.code as code')->from('App:Dna', 'dna');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(dna.code) like :q' . $i . ')')
        ->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();
    return $this->json($results);
  }

  /**
   * Creates a new dna entity.
   *
   * @Route("/new", name="dna_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $dna = new Dna();
    $em = $this->getDoctrine()->getManager();

    if ($specimen_id = $request->get('idFk')) {
      $specimen = $em->getRepository('App:Specimen')->find($specimen_id);
      $dna->setSpecimenFk($specimen);
    }

    $form = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::create(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $em->persist($dna);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->redirectToRoute('dna_edit', array(
        'id' => $dna->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ));
    }

    return $this->render('Core/dna/edit.html.twig', array(
      'dna' => $dna,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a dna entity.
   *
   * @Route("/{id}", name="dna_show", methods={"GET"}, requirements={"id"="\d+"})
   */
  public function showAction(Dna $dna) {
    $deleteForm = $this->createDeleteForm($dna);
    $editForm = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/dna/edit.html.twig', array(
      'dna' => $dna,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing dna entity.
   *
   * @Route("/{id}/edit", name="dna_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Dna $dna, EntityEditionService $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $dna->getMetaCreationUser() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $dnaProducers = $service->copyArrayCollection($dna->getDnaProducers());
    $deleteForm = $this->createDeleteForm($dna);
    $editForm = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $service->removeStaleCollection($dnaProducers, $dna->getDnaProducers());
      $em->persist($dna);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/dna/edit.html.twig', array(
        'dna' => $dna,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/dna/edit.html.twig', array(
      'dna' => $dna,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a dna entity.
   *
   * @Route("/{id}", name="dna_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Dna $dna) {
    $form = $this->createDeleteForm($dna);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($dna);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('dna_index');
  }

  /**
   * Creates a form to delete a dna entity.
   *
   * @param Dna $dna The dna entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Dna $dna) {
    return $this->createFormBuilder()
      ->setAction(
        $this->generateUrl('dna_delete', ['id' => $dna->getId()])
      )
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * Import CSV file form
   *
   * @Route("/import", name="dna_import", methods={"GET"})
   */
  public function import() {
    return $this->render(
      'import_csv_form.html.twig',
      [
        'template' => "build/imports/DNA.csv",
        'component' => 'DnaImport',
      ]
    );
  }
}
