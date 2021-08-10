<?php

namespace App\Controller\Core;

use App\Entity\Site;
use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Site controller.
 *
 * @Route("site")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SiteController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all site entities.
   *
   * @Route("/", name="site_index", methods={"GET"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $sites = $em->getRepository('App:Site')->findAll();

    return $this->render('Core/site/index.html.twig', array(
      'sites' => $sites,
    ));
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="site_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('site.id, site.code as code')
      ->from('App:Site', 'site');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(site.code) like :q' . $i . ')');
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
   * @Route("/indexjson", name="site_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = ($request->get('sort') !== NULL)
    ? $request->get('sort')
    : array('site.dateMaj' => 'desc', 'site.id' => 'desc');
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Site")
      ->createQueryBuilder('site')
      ->where('LOWER(site.code) LIKE :criteriaLower')
      ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')) . '%')
      ->leftJoin('App:Country', 'country', 'WITH', 'site.countryFk = country.id')
      ->leftJoin('App:Municipality', 'municipality', 'WITH', 'site.municipalityFk = municipality.id')
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
        'SELECT sampling.id FROM App:Sampling sampling
                WHERE sampling.siteFk = ' . $id
      )->getResult();
      $siteFk = (count($query) > 0) ? $id : '';
      $tab_toshow[] = array(
        "id" => $id,
        "site.id" => $id,
        "site.code" => $entity->getCode(),
        "site.name" => $entity->getName(),
        "municipality.code" => $entity->getMunicipalityFk()->getCode(),
        "country.code" => $entity->getCountryFk()->getCode(),
        "site.latDegDec" => $entity->getLatDegDec(),
        "site.longDegDec" => $entity->getLongDegDec(),
        "site.dateCre" => $DateCre,
        "site.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "site.userCre" => $service->GetUserCreUserfullname($entity),
        "site.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkSampling" => $siteFk,
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
   * @Route("/proximity/", name="nearby_sites", methods={"POST"})
   */
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
   * Creates a new site entity.
   *
   * @Route("/new", name="site_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $site = new Site();
    $form = $this->createForm('App\Form\SiteType', $site, [
      'action_type' => Action::create(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($site);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/site/index.html.twig', array(
          'exception_message' => explode("\n", $exception_message)[0],
        ));
      }
      return $this->redirectToRoute('site_edit', array(
        'id' => $site->getId(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/site/edit.html.twig', array(
      'site' => $site,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a site entity.
   *
   * @Route("/{id}", name="site_show", methods={"GET"})
   */
  public function showAction(Site $site) {
    $deleteForm = $this->createDeleteForm($site);

    $editForm = $this->createForm('App\Form\SiteType', $site, [
      'action_type' => Action::show(),
    ]);
    return $this->render('Core/site/edit.html.twig', array(
      'site' => $site,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing site entity.
   *
   * @Route("/{id}/edit", name="site_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Site $site) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $site->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $deleteForm = $this->createDeleteForm($site);
    $editForm = $this->createForm('App\Form\SiteType', $site, [
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
          'Core/site/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/site/edit.html.twig', array(
        'site' => $site,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/site/edit.html.twig', array(
      'site' => $site,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a site entity.
   *
   * @Route("/{id}", name="site_delete", methods={"DELETE","POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Site $site) {
    $form = $this->createDeleteForm($site);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($site);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/site/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }
    return $this->redirectToRoute('site_index');
  }

  /**
   * Creates a form to delete a site entity.
   *
   * @param Site $site The site entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Site $site) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('site_delete', array('id' => $site->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
