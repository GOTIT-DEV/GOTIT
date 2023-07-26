<?php

namespace App\Controller\Core;

use App\Entity\Station;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Station controller.
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
#[Route("station")]
class StationController extends EntityController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all station entities.
   */
  #[Route("/", name: "station_index", methods: ["GET"])]
  public function indexAction() {
    return $this->render('Core/station/index.html.twig');
  }

  #[Route("/search/{q}", requirements: ["q" => ".+"], name: "station_search")]
  public function searchAction($q) {
    $qb = $this->entityManager->createQueryBuilder();
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
   */
  #[Route("/indexjson", name: "station_indexjson", methods: ["POST"])]
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    $user = $this->getUser();
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
      ? $request->get('sort')
      : array('station.dateMaj' => 'desc', 'station.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    $tab_toshow = [];

    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }

    $qb = $this->getRepository(Station::class)
      ->createQueryBuilder('station');
    if ($searchPhrase) {
      $qb = $qb->where('LOWER(station.codeStation) LIKE :criteriaLower')
        ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')) . '%');
    }
    $entities_toshow = $qb
      ->leftJoin('App:Pays', 'pays', 'WITH', 'station.paysFk = pays.id')
      ->leftJoin('App:Commune', 'commune', 'WITH', 'station.communeFk = commune.id')
      ->setFirstResult($minRecord)
      ->setMaxResults($maxRecord)
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();

    $nb_entities =  $this->getRepository(Station::class)->count([]);

    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateCre = ($entity->getDateCre() !== null)
        ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $query = $this->entityManager->createQuery(
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
        "station.latDegDec" => $entity->getLatitude($user),
        "station.longDegDec" => $entity->getLongitude($user),
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

  #[Route("/proximity/", name: "nearby_stations", methods: ["POST"])]
  public function geoCoords(Request $request) {
    $data = $request->request;
    $latitude = $data->get('latitude');
    $longitude = $data->get('longitude');
    $radius = $data->get('radius');

    $sqlQuery = "SELECT
      site.id as site_id,
      site.site_code,
      site.site_name,
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

    $stmt = $this->entityManager->getConnection()->prepare($sqlQuery);

    $res = $stmt->executeQuery(array(
      'longitude' => $longitude,
      'latitude' => $latitude,
      'radius' => $radius,
    ));

    $sites = $res->fetchAllAssociative();
    if ($this->getUser() === null) {
      foreach ($sites as $i => $site) {
        $sites[$i]["latitude"] = round($site["latitude"], 2);
        $sites[$i]["longitude"] = round($site["longitude"], 2);
      }
    }

    return new JsonResponse([
      'sites' => $sites,
      'latitude' => $latitude,
      'longitude' => $longitude,
    ]);
  }

  /**
   * Creates a new station entity.
   */
  #[Route("/new", name: "station_new", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function newAction(Request $request) {
    $station = new Station();
    $form = $this->createForm('App\Form\StationType', $station, [
      'action_type' => Action::create->value,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->entityManager->persist($station);
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
   */
  #[Route("/{id}", name: "station_show", methods: ["GET"])]
  public function showAction(Station $station) {
    $deleteForm = $this->createDeleteForm($station);

    $editForm = $this->createForm('App\Form\StationType', $station, [
      'action_type' => Action::show->value,
    ]);
    return $this->render('Core/station/edit.html.twig', array(
      'station' => $station,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing station entity.
   */
  #[Route("/{id}/edit", name: "station_edit", methods: ["GET", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function editAction(Request $request, Station $station) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $station->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $deleteForm = $this->createDeleteForm($station);
    $editForm = $this->createForm('App\Form\StationType', $station, [
      'action_type' => Action::edit->value,
    ]);
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      try {
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
   */
  #[Route("/{id}", name: "station_delete", methods: ["DELETE", "POST"])]
  #[IsGranted("ROLE_COLLABORATION")]
  public function deleteAction(Request $request, Station $station) {
    $form = $this->createDeleteForm($station);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      try {
        $this->entityManager->remove($station);
        $this->entityManager->flush();
      } catch (\Exception $e) {
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
