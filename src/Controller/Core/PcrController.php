<?php

namespace App\Controller\Core;

use App\Entity\Pcr;
use App\Form\Enums\Action;
use App\Services\Core\EntityEditionService;
use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Pcr controller.
 *
 * @Route("pcr")
 * @Security("is_granted('ROLE_INVITED')")
 *
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class PcrController extends AbstractController {
  public const MAX_RESULTS_TYPEAHEAD = 20;

  /**
   * Lists all pcr entities.
   *
   * @Route("/", name="pcr_index", methods={"GET"})
   * @Route("/", name="pcrchromato_index", methods={"GET"})
   */
  public function indexAction(Request $request) {
    $em = $this->getDoctrine()->getManager();

    $dna = $request->query->get('dna');
    if ($dna) {
      $pcrs = $em->getRepository('App:Pcr')->findBy(['dna' => $dna]);
      if (1 === count($pcrs)) {
        return $this->redirectToRoute('pcr_show', ['id' => array_shift($pcrs)->getId()]);
      }
    }

    $pcrs = $em->getRepository('App:Pcr')->findAll();

    return $this->render('Core/pcr/index.html.twig', [
      'pcrs' => $pcrs,
    ]);
  }

  /**
   * @Route("/search/{q}", requirements={"q": ".+"}, name="pcr_search")
   *
   * @param mixed $q
   */
  public function searchAction($q) {
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->select('pcr.id, pcr.code as code')
      ->from('App:Pcr', 'pcr');
    $query = explode(' ', strtolower(trim(urldecode($q))));
    for ($i = 0; $i < count($query); ++$i) {
      $qb->andWhere('(LOWER(pcr.code) like :q' . $i . ')');
      $qb->setParameter('q' . $i, $query[$i] . '%');
    }
    $qb->addOrderBy('code', 'ASC');
    $qb->setMaxResults(self::MAX_RESULTS_TYPEAHEAD);
    $results = $qb->getQuery()->getResult();

    return $this->json($results);
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria:
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="pcr_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();

    $rowCount = $request->get('rowCount') ?: 10;
    $orderBy = $request->get('sort') ?: [
      'pcr.metaUpdateDate' => 'desc', 'pcr.id' => 'desc',
    ];
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    $maxRecord = $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(specimen.molecularCode) LIKE :criteriaLower';
    $searchPhrase = $request->get('searchPhrase');
    if ($request->get('searchPattern') && !$searchPhrase) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') && false !== filter_var($request->get('idFk'), FILTER_VALIDATE_INT)) {
      $where .= ' AND pcr.dna = ' . $request->get('idFk');
    }
    // Search for the list to show
    $tab_toshow = [];
    $entities_toshow = $em->getRepository('App:Pcr')
      ->createQueryBuilder('pcr')
      ->where($where)
      ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
      ->leftJoin('App:Dna', 'dna', 'WITH', 'pcr.dna = dna.id')
      ->leftJoin('App:Specimen', 'specimen', 'WITH', 'dna.specimen = specimen.id')
      ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.gene = vocGene.id')
      ->leftJoin('App:Voc', 'vocQualitePcr', 'WITH', 'pcr.quality = vocQualitePcr.id')
      ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificity = vocSpecificite.id')
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
      $Date = (null !== $entity->getDate())
      ? $entity->getDate()->format('Y-m-d') : null;
      $MetaUpdateDate = (null !== $entity->getMetaUpdateDate())
      ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $MetaCreationDate = (null !== $entity->getMetaCreationDate())
      ? $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      // Search chromatograms associated to a PCR
      $query = $em->createQuery(
        'SELECT chromato.id FROM App:Chromatogram chromato
                WHERE chromato.pcr = ' . $id
      )->getResult();
      $linkChromatogram = (count($query) > 0) ? $id : '';
      // concatenated list of people
      // $query = $em->createQuery(
      //   'SELECT p.name as nom FROM App:PcrProducer erp
      //           JOIN erp.personFk p WHERE erp.pcr = ' . $id
      // )->getResult();
      $arrayListePerson = [];
      // foreach ($query as $taxon) {
      //   $arrayListePerson[] = $taxon['nom'];
      // }
      $listePerson = implode(', ', $arrayListePerson);

      $tab_toshow[] = [
        'id' => $id, 'pcr.id' => $id,
        'specimen.molecularCode' => $entity->getDna()->getSpecimen()->getMolecularCode(),
        'dna.code' => $entity->getDna()->getCode(),
        'pcr.code' => $entity->getCode(),
        'pcr.number' => $entity->getNumber(),
        'vocGene.code' => $entity->getGene()->getCode(),
        'listePerson' => $listePerson,
        'pcr.date' => $Date,
        'vocQualitePcr.code' => $entity->getQuality()->getCode(),
        'vocSpecificite.code' => $entity->getSpecificity()->getCode(),
        'pcr.metaCreationDate' => $MetaCreationDate,
        'pcr.metaUpdateDate' => $MetaUpdateDate,
        'metaCreationUserId' => $service->GetMetaCreationUserId($entity),
        'pcr.metaCreationUser' => $service->GetMetaCreationUserUserfullname($entity),
        'pcr.metaUpdateUser' => $service->GetMetaUpdateUserUserfullname($entity),
        'linkChromatogram' => $linkChromatogram,
      ];
    }

    return new JsonResponse([
      'current' => intval($request->get('current')),
      'rowCount' => $rowCount,
      'rows' => $tab_toshow,
      'searchPhrase' => $searchPhrase,
      'total' => $nb, // total data array
    ]);
  }

  /**
   * Creates a new pcr entity.
   *
   * @Route("/new", name="pcr_new", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request) {
    $pcr = new Pcr();
    $em = $this->getDoctrine()->getManager();
    // check if the relational Entity (Dna) is given and set the RelationalEntityFk for the new Entity
    if ($dna_id = $request->get('idFk')) {
      $dna = $em->getRepository('App:Dna')->find($dna_id);
      $pcr->setDna($dna);
    }
    $form = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::create(),
      'validation_groups' => ['Default', 'code'],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($pcr);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );

        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->redirectToRoute('pcr_edit', [
        'id' => $pcr->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk'),
      ]);
    }

    return $this->render('Core/pcr/edit.html.twig', [
      'pcr' => $pcr,
      'edit_form' => $form->createView(),
    ]);
  }

  /**
   * Finds and displays a pcr entity.
   *
   * @Route("/{id}", name="pcr_show", methods={"GET"})
   */
  public function showAction(Pcr $pcr) {
    $deleteForm = $this->createDeleteForm($pcr);
    $editForm = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::show(),
    ]);

    return $this->render('Core/pcr/edit.html.twig', [
      'pcr' => $pcr,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Displays a form to edit an existing pcr entity.
   *
   * @Route("/{id}/edit", name="pcr_edit", methods={"GET", "POST"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function editAction(Request $request, Pcr $pcr, EntityEditionService $service) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if ('ROLE_COLLABORATION' == $user->getRole() && $pcr->getMetaCreationUser() != $user->getId()) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }

    $producers = $service->copyArrayCollection($pcr->getProducers());
    $deleteForm = $this->createDeleteForm($pcr);
    $editForm = $this->createForm('App\Form\PcrType', $pcr, [
      'action_type' => Action::edit(),
      'validation_groups' => ['Default'],
    ]);

    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      $service->removeStaleCollection($producers, $pcr->getProducers());

      $em = $this->getDoctrine()->getManager();
      $em->persist($pcr);

      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );

        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }

      return $this->render('Core/pcr/edit.html.twig', [
        'pcr' => $pcr,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
      ]);
    }

    return $this->render('Core/pcr/edit.html.twig', [
      'pcr' => $pcr,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ]);
  }

  /**
   * Deletes a pcr entity.
   *
   * @Route("/{id}", name="pcr_delete", methods={"DELETE"})
   * @Security("is_granted('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, Pcr $pcr) {
    $form = $this->createDeleteForm($pcr);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($pcr);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );

        return $this->render(
          'Core/pcr/index.html.twig',
          ['exception_message' => explode("\n", $exception_message)[0]]
        );
      }
    }

    return $this->redirectToRoute('pcr_index');
  }

  /**
   * Creates a form to delete a pcr entity.
   *
   * @param Pcr $pcr The pcr entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createDeleteForm(Pcr $pcr) {
    return $this->createFormBuilder()
      ->setAction($this->generateUrl('pcr_delete', ['id' => $pcr->getId()]))
      ->setMethod('DELETE')
      ->getForm();
  }
}
