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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\Core\GenericFunctionE3s;
use App\Form\Enums\Action;
use App\Entity\Adn;

/**
 * Adn controller.
 *
 * @Route("adn")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class AdnController extends AbstractController
{
  const MAX_RESULTS_TYPEAHEAD   = 20;

  /**
   * Lists all adn entities.
   *
   * @Route("/", name="adn_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $adns = $em->getRepository('App:Adn')->findAll();

    return $this->render('Core/adn/index.html.twig', array(
      'adns' => $adns,
    ));
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="adn_search")
   */
  public function searchAction($q)
  {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('adn.id, adn.codeAdn as code')->from('App:Adn', 'adn');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(adn.codeAdn) like :q' . $i . ')');
    }
    for ($i = 0; $i < count($query); $i++) {
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();
    // Ajax answer
    return $this->json($results);
  }


  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="adn_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service)
  {
    // load Doctrine Manager    
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount')  !== NULL)
      ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort')  !== NULL)
      ? $request->get('sort') : array('adn.dateMaj' => 'desc', 'adn.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(adn.codeAdn) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' &&
      $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
      $where .= ' AND adn.individuFk = ' . $request->get('idFk');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Adn")->createQueryBuilder('adn')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
      ->leftJoin('App:Boite', 'boite', 'WITH', 'adn.boiteFk = boite.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
      ? array_slice($entities_toshow, $minRecord, $rowCount)
      : array_slice($entities_toshow, $minRecord);
    $lastTaxname = '';
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateAdn = ($entity->getDateAdn() !== null)
        ?  $entity->getDateAdn()->format('Y-m-d') : null;
      $codeBoite = ($entity->getBoiteFk() !== null)
        ?  $entity->getBoiteFk()->getCodeBoite() : null;
      $DateMaj = ($entity->getDateMaj() !== null)
        ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
        ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      // search the PCRs from the DNA
      $query = $em->createQuery(
        'SELECT pcr.id FROM App:Pcr pcr WHERE pcr.adnFk = ' . $id
      )->getResult();
      $linkPcr = (count($query) > 0) ? $id : '';
      //  concatenated list of people
      $query = $em->createQuery(
        'SELECT p.nomPersonne as nom FROM App:AdnEstRealisePar erp 
                JOIN erp.personneFk p WHERE erp.adnFk = ' . $id
      )->getResult();
      $arrayListePersonne = array();
      foreach ($query as $taxon) {
        $arrayListePersonne[] = $taxon['nom'];
      }
      $listePersonne = implode(", ", $arrayListePersonne);
      //
      $tab_toshow[] = array(
        "id" => $id,
        "adn.id" => $id,
        "individu.codeIndBiomol" => $entity->getIndividuFk()->getCodeIndBiomol(),
        "adn.codeAdn" => $entity->getCodeAdn(),
        "listePersonne" => $listePersonne,
        "adn.dateAdn" => $DateAdn,
        "boite.codeBoite" => $codeBoite,
        "adn.dateCre" => $DateCre,
        "adn.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "adn.userCre" => $service->GetUserCreUsername($entity),
        "adn.userMaj" => $service->GetUserMajUsername($entity),
        "linkPcr" => $linkPcr,
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
   * Creates a new adn entity.
   *
   * @Route("/new", name="adn_new", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request)
  {
    $adn = new Adn();
    $em = $this->getDoctrine()->getManager();
    // check if the relational Entity (Individu) is given and set the RelationalEntityFk for the new Entity
    if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
      $RelEntityId = $request->get('idFk');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId);
      $adn->setIndividuFk($RelEntity);
    }
    $form = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::create()
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // (i) load the id of relational Entity (Individu) from typeahead input field and (ii) set the foreign key
      $RelEntityId = $form->get('individuId');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId->getData());
      $adn->setIndividuFk($RelEntity);
      // persist
      $em->persist($adn);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render('Core/adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
      }
      return $this->redirectToRoute('adn_edit', array(
        'id' => $adn->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk')
      ));
    }

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a adn entity.
   *
   * @Route("/{id}", name="adn_show", methods={"GET"})
   */
  public function showAction(Adn $adn)
  {
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::show()
    ]);

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing adn entity.
   *
   * @Route("/{id}/edit", name="adn_edit", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Adn $adn, GenericFunctionE3s $service)
  {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() ==  'ROLE_COLLABORATION' && $adn->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    // load service  generic_function_e3s
    //        
    // store ArrayCollection       
    $adnEstRealisePars = $service->setArrayCollection('AdnEstRealisePars', $adn);
    //
    $deleteForm = $this->createDeleteForm($adn);
    $editForm = $this->createForm('App\Form\AdnType', $adn, [
      'action_type' => Action::edit()
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection('AdnEstRealisePars', $adn, $adnEstRealisePars);
      // (i) load the id of relational Entity (Individu) from typeahead input field  (ii) set the foreign key
      $em = $this->getDoctrine()->getManager();
      $RelEntityId = $editForm->get('individuId');
      $RelEntity = $em->getRepository('App:Individu')->find($RelEntityId->getData());
      $adn->setIndividuFk($RelEntity);
      // flush
      $this->getDoctrine()->getManager()->persist($adn);
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render('Core/adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
      }
      return $this->render('Core/adn/edit.html.twig', array(
        'adn' => $adn,
        'edit_form' => $editForm->createView(),
        'valid' => 1
      ));
    }

    return $this->render('Core/adn/edit.html.twig', array(
      'adn' => $adn,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a adn entity.
   *
   * @Route("/{id}", name="adn_delete", methods={"DELETE"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Adn $adn)
  {
    $form = $this->createDeleteForm($adn);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($adn);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render('Core/adn/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
      }
    }

    return $this->redirectToRoute('adn_index');
  }

  /**
   * Creates a form to delete a adn entity.
   *
   * @param Adn $adn The adn entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Adn $adn)
  {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('adn_delete', array('id' => $adn->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
