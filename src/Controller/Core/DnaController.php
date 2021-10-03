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
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class DnaController extends AbstractController {
  /**
   * Lists all dna entities.
   *
   * @Route("/", name="dna_index", methods={"GET"})
   */
  public function indexAction() {
    return $this->render('Core/dna/index.html.twig');
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
      $dna->setSpecimen($specimen);
    }

    $form = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::create(),
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($dna);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );

        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->redirectToRoute('dna_edit', [
        'id' => $dna->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    return $this->render('Core/dna/edit.html.twig', [
      'dna' => $dna,
      'edit_form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a dna entity.
   *
   * @Route("/{id}", name="dna_show", methods={"GET"}, requirements={"id": "\d+"})
   */
  public function showAction(Dna $dna) {
    $editForm = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/dna/edit.html.twig', [
      'dna' => $dna,
      'edit_form' => $editForm->createView(),
    ]);
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
    if ('ROLE_COLLABORATION' == $user->getRole() && $dna->getMetaCreationUser() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $producers = $service->copyArrayCollection($dna->getProducers());
    $editForm = $this->createForm('App\Form\DnaType', $dna, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $service->removeStaleCollection($producers, $dna->getProducers());
      $em->persist($dna);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\Exception $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );

        return $this->render(
          'Core/dna/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->render('Core/dna/edit.html.twig', [
        'dna' => $dna,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ]);
    }

    return $this->render('Core/dna/edit.html.twig', [
      'dna' => $dna,
      'edit_form' => $editForm->createView(),
    ]);
  }

  /**
   * Import CSV file form
   *
   * @Route("/import", name="dna_import", methods={"GET"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function import() {
    return $this->render(
      'import_csv_form.html.twig',
      [
        'template' => 'build/imports/DNA_import_template.csv',
        'component' => 'DnaImport',
      ]
    );
  }
}
