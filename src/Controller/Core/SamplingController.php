<?php

namespace App\Controller\Core;

use App\Form\Enums\Action;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Sampling controller.
 *
 * @Route("sampling")
 * @Security("is_granted('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SamplingController extends AbstractController {
  const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all sampling entities.
   *
   * @Route("/", name="sampling_index", methods={"GET", "POST"})
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();
    $samplings = $em->getRepository('App:Sampling')->findAll();

    return $this->render('Core/sampling/index.html.twig', array(
      'samplings' => $samplings,
    ));
  }

  /**
   * @Route("/search/{q}", requirements={"q"=".+"}, name="sampling_search")
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('sampling.id, sampling.code as code')
      ->from('App:Sampling', 'sampling');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); $i++) {
      $qb->andWhere('(LOWER(sampling.code) like :q' . $i . ')');
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
   * @Route("/indexjson", name="sampling_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'sampling.dateMaj' => 'desc',
      'sampling.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;

    // initializes the searchPhrase variable
    $where = 'LOWER(sampling.code) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && filter_var($request->get('idFk'), FILTER_VALIDATE_INT) !== false) {
      $where .= ' AND sampling.siteFk  = ' . $request->get('idFk');
    }
    // Search the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Sampling")->createQueryBuilder('sampling')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin('App:Site', 'site', 'WITH', 'sampling.siteFk = site.id')
      ->leftJoin('App:Country', 'country', 'WITH', 'site.countryFk = country.id')
      ->leftJoin('App:Municipality', 'municipality', 'WITH', 'site.municipalityFk = municipality.id')
      ->leftJoin('App:Voc', 'voc', 'WITH', 'sampling.legVocFk = voc.id')
      ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
      ->getQuery()
      ->getResult();
    $nb_entities = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
    ? array_slice($entities_toshow, $minRecord, $rowCount)
    : array_slice($entities_toshow, $minRecord);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $Date = ($entity->getDate() !== null)
      ? $entity->getDate()->format('Y-m-d') : null;
      $DateMaj = ($entity->getDateMaj() !== null)
      ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $DateCre = ($entity->getDateCre() !== null)
      ? $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      // search for material associated with a sampling
      $query = $em->createQuery(
        'SELECT lot.id FROM App:InternalLot lot WHERE lot.samplingFk = ' . $id
      )->getResult();
      $linkInternalLotFk = (count($query) > 0) ? $id : '';
      // search for external material associated with a sampling
      $query = $em->createQuery(
        'SELECT lotext.id FROM App:ExternalLot lotext WHERE lotext.samplingFk = ' . $id
      )->getResult();
      $linkExternalLotFk = (count($query) > 0) ? $id : '';
      // search for external sequence associated with a sampling
      $query = $em->createQuery(
        'SELECT sqcext.id FROM App:ExternalSequence sqcext WHERE sqcext.samplingFk = ' . $id
      )->getResult();
      $linkExternalSequenceFk = (count($query) > 0) ? $id : '';
      // Search for the concatenated list of targeted taxa
      $query = $em->createQuery(
        'SELECT rt.taxname as taxname FROM App:TaxonSampling ac JOIN ac.taxonFk rt WHERE ac.samplingFk = ' . $id
      )->getResult();
      $arrayTaxonsCibler = array();
      foreach ($query as $taxon) {
        $arrayTaxonsCibler[] = $taxon['taxname'];
      }
      $listeTaxonsCibler = implode(", ", $arrayTaxonsCibler);

      $tab_toshow[] = array(
        "id" => $id,
        "sampling.id" => $id,
        "sampling.code" => $entity->getCode(),
        "site.code" => $entity->getSiteFk()->getCode(),
        "country.name" => $entity->getSiteFk()->getCountryFk()->getName(),
        "municipality.code" => $entity->getSiteFk()->getMunicipalityFk()->getCode(),
        "sampling.legVocFk" => $entity->getLegVocFk()->getCode(),
        "sampling.date" => $Date,
        "sampling.status" => $entity->getStatus(),
        "sampling.dateCre" => $DateCre,
        "sampling.dateMaj" => $DateMaj,
        "userCreId" => $service->GetUserCreId($entity),
        "sampling.userCre" => $service->GetUserCreUserfullname($entity),
        "sampling.userMaj" => $service->GetUserMajUserfullname($entity),
        "linkInternalLot" => $linkInternalLotFk,
        "linkExternalLot" => $linkExternalLotFk,
        "linkExternalSequence" => $linkExternalSequenceFk,
        "listeTaxonsCibler" => $listeTaxonsCibler,
      );
    }

    return new JsonResponse([
      "current" => intval($request->get('current')),
      "rowCount" => $rowCount,
      "rows" => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total" => $nb_entities,
    ]);
  }

  /**
   * Creates a new sampling entity.
   *
   * @Route("/new", name="sampling_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $sampling = new Sampling();
    $em = $this->getDoctrine()->getManager();

    // check if the relational Entity (Site) is given
    if ($site_id = $request->get('idFk')) {
      // set the RelationalEntityFk for the new Entity
      $site = $em->getRepository('App:Site')->find($site_id);
      $sampling->setSiteFk($site);
    }

    $form = $this->createForm('App\Form\SamplingType', $sampling, [
      'action_type' => Action::create(),
    ]);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($sampling);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render('Core/sampling/index.html.twig', [
          'exception_message' => explode("\n", $exception_message)[0],
        ]);
      }
      return $this->redirectToRoute('sampling_edit', [
        'id' => $sampling->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    // Initial form render or form invalid
    return $this->render('Core/sampling/edit.html.twig', array(
      'sampling' => $sampling,
      'edit_form' => $form->createView(),
    ));
  }

  /**
   * Finds and displays a sampling entity.
   *
   * @Route("/{id}", name="sampling_show", methods={"GET"})
   */
  public function showAction(Sampling $sampling) {
    $deleteForm = $this->createDeleteForm($sampling);
    $showForm = $this->createForm('App\Form\SamplingType', $sampling, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/sampling/edit.html.twig', [
      'sampling' => $sampling,
      'edit_form' => $showForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing sampling entity.
   *
   * @Route("/{id}/edit", name="sampling_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Sampling $sampling, GenericFunctionE3s $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ($user->getRole() == 'ROLE_COLLABORATION' && $sampling->getUserCre() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $originalSamplingMethods = $service->setArrayCollection('SamplingMethods', $sampling);
    $originalSamplingFixatives = $service->setArrayCollection('SamplingFixatives', $sampling);
    $originalSamplingFundings = $service->setArrayCollection('SamplingFundings', $sampling);
    $originalSamplingParticipants = $service->setArrayCollection('SamplingParticipants', $sampling);
    $originalTaxonSamplings = $service->setArrayCollection('TaxonSamplings', $sampling);

    // editAction
    $deleteForm = $this->createDeleteForm($sampling);
    $editForm = $this->createForm('App\Form\SamplingType', $sampling, [
      'action_type' => Action::edit(),
    ]);
    $editForm->handleRequest($request);
    // dump($sampling->getSamplingParticipants());

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection('SamplingMethods', $sampling, $originalSamplingMethods);
      $service->DelArrayCollection('SamplingFixatives', $sampling, $originalSamplingFixatives);
      $service->DelArrayCollection('SamplingFundings', $sampling, $originalSamplingFundings);
      $service->DelArrayCollection('SamplingParticipants', $sampling, $originalSamplingParticipants);
      $service->DelArrayCollection('TaxonSamplings', $sampling, $originalTaxonSamplings);

      // flush
      $this->getDoctrine()->getManager()->persist($sampling);
      try {
        $this->getDoctrine()->getManager()->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/sampling/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
      return $this->render('Core/sampling/edit.html.twig', array(
        'sampling' => $sampling,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ));
    }

    return $this->render('Core/sampling/edit.html.twig', array(
      'sampling' => $sampling,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Deletes a sampling entity.
   *
   * @Route("/{id}", name="sampling_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Sampling $sampling) {
    $form = $this->createDeleteForm($sampling);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($sampling);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        return $this->render(
          'Core/sampling/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('sampling_index');
  }

  /**
   * Creates a form to delete a sampling entity.
   *
   * @param Sampling $sampling The sampling entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Sampling $sampling) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('sampling_delete', array('id' => $sampling->getId())))
      ->setMethod('DELETE')
      ->getForm();
  }
}
