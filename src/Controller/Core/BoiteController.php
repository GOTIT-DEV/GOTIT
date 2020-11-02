<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */


namespace App\Controller\Core;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Services\Core\GenericFunctionE3s;
use App\Form\Enums\Action;
use App\Entity\Voc;
use App\Entity\Boite;

/**
 * Boite controller.
 *
 * @Route("boite")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class BoiteController extends AbstractController
{
  /**
   * Lists all boite entities.
   *
   * @Route("/", name="boite_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $boites = $em->getRepository('App:Boite')->findAll();

    return $this->render('Core/boite/index.html.twig', array(
      'boites' => $boites,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="boite_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service)
  {
    // load Doctrine Manager      
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount')  !== NULL)
      ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort')  !== NULL)
      ? $request->get('sort')
      : array('boite.dateMaj' => 'desc', 'boite.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(boite.codeBoite) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' &&
      $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('typeBoite') !== null && $request->get('typeBoite') !== '') {
      $where .= " AND vocTypeBoite.code LIKE '" . $request->get('typeBoite') . "'";
    }
    // Search for the list to show EstAligneEtTraite
    $tab_toshow = [];
    $entities_toshow = $em
      ->getRepository("App:Boite")
      ->createQueryBuilder('boite')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin(
        'App:Voc',
        'vocCodeCollection',
        'WITH',
        'boite.codeCollectionVocFk = vocCodeCollection.id'
      )
      ->leftJoin(
        'App:Voc',
        'vocTypeBoite',
        'WITH',
        'boite.typeBoiteVocFk = vocTypeBoite.id'
      )
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
        ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
        ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      //
      $tab_toshow[] = array(
        "id" => $id, "boite.id" => $id,
        "boite.codeBoite" => $entity->getCodeBoite(),
        "vocCodeCollection.code" => $entity->getCodeCollectionVocFk()->getCode(),
        "boite.libelleBoite" => $entity->getLibelleBoite(),
        "vocCodeCollection.libelle" => $entity->getCodeCollectionVocFk()->getLibelle(),
        "boite.dateCre" => $DateCre,
        "boite.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "boite.userCre" => $service->GetUserCreUsername($entity),
        "boite.userMaj" => $service->GetUserMajUsername($entity),
      );
    }

    return new JsonResponse([
      "current"    => intval($request->get('current')),
      "rowCount"  => $rowCount,
      "rows"     => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total"    => $nb // total data array				
    ]);
  }


  /**
   * Creates a new boite entity.
   *
   * @Route("/new", name="boite_new", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request)
  {



    $boite = new Boite();

    if ($request->get("typeBoite")) {
      $boxTypeRepo = $this->getDoctrine()->getRepository(Voc::class);
      $boxType = $boxTypeRepo->findOneBy([
        'code' => $request->get('typeBoite'),
        'parent' => 'typeBoite'
      ]);
      $boite->setTypeBoiteVocFk($boxType);
    }

    $form = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::create()
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($boite);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' =>  explode("\n", $exception_message)[0]]
        );
      }
      return $this->redirectToRoute('boite_edit', array(
        'id' => $boite->getId(),
        'valid' => 1,
        'typeBoite' => $request->get('typeBoite')
      ));
    }

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a boite entity.
   *
   * @Route("/{id}", name="boite_show", methods={"GET"})
   */
  public function showAction(Boite $boite)
  {
    $deleteForm = $this->createDeleteForm($boite);
    $editForm = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::show()
    ]);

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing boite entity.
   *
   * @Route("/{id}/edit", name="boite_edit", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Boite $boite)
  {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() ==  'ROLE_COLLABORATION' &&
      $boite->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    // load the Entity Manager
    $em = $this->getDoctrine()->getManager();

    $deleteForm = $this->createDeleteForm($boite);
    $editForm = $this->createForm('App\Form\BoiteType', $boite, [
      'action_type' => Action::edit()
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $em->persist($boite);
      // flush
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' =>  explode("\n", $exception_message)[0]]
        );
      }
      $editForm = $this->createForm('App\Form\BoiteType', $boite, [
        'action_type' => Action::edit()
      ]);

      return $this->render('Core/boite/edit.html.twig', array(
        'boite' => $boite,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/boite/edit.html.twig', array(
      'boite' => $boite,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a boite entity.
   *
   * @Route("/{id}", name="boite_delete", methods={"DELETE"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Boite $boite)
  {
    $form = $this->createDeleteForm($boite);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($boite);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/boite/index.html.twig',

          ['exception_message' =>  explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('boite_index', array(
      'typeBoite' => $request->get('typeBoite')
    ));
  }

  /**
   * Creates a form to delete a boite entity.
   *
   * @param Boite $boite The boite entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Boite $boite)
  {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl(
        'boite_delete',
        array('id' => $boite->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }
}
