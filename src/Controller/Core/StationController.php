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
use App\Entity\Station;

/**
 * Station controller.
 *
 * @Route("station")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class StationController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all station entities.
   *
   * @Route("/", name="station_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $stations = $em->getRepository('App:Station')->findAll();

    return $this->render('Core/station/index.html.twig', array(
      'stations' => $stations,
    ));
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="station_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('station.id, station.codeStation as code')
      ->from('App:Station', 'station');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(station.codeStation) like :q' . $i . ')');
    }
    for ($i = 0; $i < count($query); $i++) {
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();
    // Ajax answer
    return $this->json(
      $results
    );
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="station_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('station.dateMaj' => 'desc', 'station.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Station")
      ->createQueryBuilder('station')
      ->where('LOWER(station.codeStation) LIKE :criteriaLower')
      ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')) . '%')
      ->leftJoin('App:Pays', 'pays', 'WITH', 'station.paysFk = pays.id')
      ->leftJoin('App:Commune', 'commune', 'WITH', 'station.communeFk = commune.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb_entities = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateCre = ($entity->getDateCre() !== null)
      ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
      ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $query = $em->createQuery(
        'SELECT collecte.id FROM App:Collecte collecte
                WHERE collecte.stationFk = ' . $id
      )->getResult();
      $stationFk = (count($query) > 0) ? $id : '';
      $tab_toshow[] = array(
        "id" => $id,
        "station.id" => $id,
        "station.codeStation" => $entity->getCodeStation(),
        "station.nomStation" => $entity->getNomStation(),
        "commune.codeCommune" => $entity->getCommuneFk()->getCodeCommune(),
        "pays.codePays" => $entity->getPaysFk()->getCodePays(),
        "station.latDegDec" => $entity->getLatDegDec(),
        "station.longDegDec" => $entity->getLongDegDec(),
        "station.dateCre" => $DateCre,
        "station.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "station.userCre" => $service->GetUserCreUserfullname($entity),
        "station.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkCollecte" => $stationFk,
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "total" => $nb_entities, // total data array
    ]);
  }

  /**
   * @Route("/proximity/", name="nearby_stations", methods={"POST"})
   */
  public function geoCoords(Request $request) {
    $data = $request->request;
    $latitude = $data->get('latitude');
    $longitude = $data->get('longitude');
    $radius = $data->get('radius');

    $sqlQuery = "SELECT
      site.id,
      site.site_code AS station_code,
      site.site_name AS name,
      site.longitude,
      site.latitude,
      site.elevation as altitude,
      municipality_name as municipality,
      country_name as country
      FROM site
      JOIN municipality muni on site.municipality_fk = muni.id
      JOIN country on site.country_fk = country.id
      WHERE earth_distance(
        ll_to_earth(latitude, longitude),
        ll_to_earth(:latitude, :longitude)
      ) <= :radius";

    $stmt = $this->getDoctrine()->getManager()
      ->getConnection()
      ->prepare($sqlQuery);

    $stmt->execute(array(
      'longitude' => $longitude,
      'latitude' => $latitude,
      'radius' => $radius,
    ));

    $sites = $stmt->fetchAll();

    return new JsonResponse([
      'sites' => $sites,
      'latitude' => $latitude,
      'longitude' => $longitude,
    ]);
  }

  /**
   * Creates a new station entity.
   *
   * @Route("/new", name="station_new", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $station = new Station();
    $form = $this->createForm('App\Form\StationType', $station, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($station);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/station/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->redirectToRoute('station_edit', array(
        'id' => $station->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/station/edit.html.twig', array(
      'station' => $station,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a station entity.
   *
   * @Route("/{id}", name="station_show", methods={"GET"})
   */
  public function showAction(Station $station) {
    $deleteForm = $this->createDeleteForm($station);

    $editForm = $this->createForm('App\Form\StationType', $station, [
      'action_type' => Action::show(),
    ]);
    return $this->render('Core/station/edit.html.twig', array(
      'station' => $station,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing station entity.
   *
   * @Route("/{id}/edit", name="station_edit", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Station $station) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $station->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $deleteForm = $this->createDeleteForm($station);
    $editForm = $this->createForm('App\Form\StationType', $station, [
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
          'Core/station/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/station/edit.html.twig', array(
        'station' => $station,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/station/edit.html.twig', array(
      'station' => $station,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a station entity.
   *
   * @Route("/{id}", name="station_delete", methods={"DELETE","POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Station $station) {
    $form = $this->createDeleteForm($station);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($station);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/station/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }
    return $this->redirectToRoute('station_index');
  }

  /**
   * Creates a form to delete a station entity.
   *
   * @param Station $station The station entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Station $station) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('station_delete', array('id' => $station->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
