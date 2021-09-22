<?php

namespace App\Controller\Core;

use App\Services\Core\GenericFunctionE3s;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController {
  /**
   * @Route("/", name="dashboard")
   * @Security("is_granted('ROLE_INVITED')")
   * @author Philippe Grison  <philippe.grison@mnhn.fr>
   */
  public function indexAction(GenericFunctionE3s $service) {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $samplingCounts = $em->createQuery('SELECT COUNT(u.id) FROM App:Sampling u')->getSingleScalarResult();
    $siteCount = $em->createQuery('SELECT COUNT(u.id) FROM App:Site u')->getSingleScalarResult();
    $internalLotCount = $em->createQuery('SELECT COUNT(u.id) FROM App:InternalLot u')->getSingleScalarResult();
    $nbExternalLot = $em->createQuery('SELECT COUNT(u.id) FROM App:ExternalLot u')->getSingleScalarResult();
    $nbSpecimen = $em->createQuery('SELECT COUNT(u.id) FROM App:Specimen u')->getSingleScalarResult();
    $nbSlide = $em->createQuery('SELECT COUNT(u.id) FROM App:Slide u')->getSingleScalarResult();
    $nbDna = $em->createQuery('SELECT COUNT(u.id) FROM App:Dna u')->getSingleScalarResult();
    $nbPcr = $em->createQuery('SELECT COUNT(u.id) FROM App:Pcr u')->getSingleScalarResult();
    $nbChromatogram = $em->createQuery('SELECT COUNT(u.id) FROM App:Chromatogram u')->getSingleScalarResult();
    $internalSequenceCount = $em->createQuery('SELECT COUNT(u.id) FROM App:InternalSequence u')->getSingleScalarResult();
    $externalSeqCount = $em->createQuery('SELECT COUNT(u.id) FROM App:ExternalSequence u')->getSingleScalarResult();
    $nbMotu = $em->createQuery('SELECT COUNT(u.id) FROM App:MotuDelimitation u')->getSingleScalarResult();
    $nbMotuSqcAss = count($em->createQuery('SELECT COUNT(sa.id) FROM App:MotuDelimitation u JOIN u.internalSequence sa GROUP BY sa.id')->getResult());
    $nbMotuSqcAssExt = count($em->createQuery('SELECT COUNT(sae.id) FROM App:MotuDelimitation u JOIN u.externalSequence sae GROUP BY sae.id')->getResult());
    $nbBoite = $em->createQuery('SELECT COUNT(u.id) FROM App:Store u')->getSingleScalarResult();
    $nbSource = $em->createQuery('SELECT COUNT(u.id) FROM App:Source u')->getSingleScalarResult();
    $nbTaxon = count($em->createQuery('SELECT COUNT(rt.id) FROM App:TaxonIdentification u JOIN u.taxon rt GROUP BY rt.id')->getResult());
    //
    $tab_toshow = [];
    // returns the last records of the dna
    $entities_toshow = $em->getRepository("App:Dna")->createQueryBuilder('dna')
      ->addOrderBy('dna.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'dna',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the chromatogram
    $entities_toshow = $em->getRepository("App:Chromatogram")->createQueryBuilder('chromatogram')
      ->addOrderBy('chromatogram.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'chromatogram',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the sampling
    $entities_toshow = $em->getRepository("App:Sampling")->createQueryBuilder('sampling')
      ->addOrderBy('sampling.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'sampling',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the specimen
    $entities_toshow = $em->getRepository("App:Specimen")->createQueryBuilder('specimen')
      ->addOrderBy('specimen.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'specimen',
        "code" => $entity->getMorphologicalCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the slide
    $entities_toshow = $em->getRepository("App:Slide")->createQueryBuilder('slide')
      ->addOrderBy('slide.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'slide',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the lot material
    $entities_toshow = $em->getRepository("App:InternalLot")->createQueryBuilder('InternalLot')
      ->addOrderBy('InternalLot.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'internal_lot',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the external lot material
    $entities_toshow = $em->getRepository("App:ExternalLot")->createQueryBuilder('external_lot')
      ->addOrderBy('external_lot.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'external_lot',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the motu
    $entities_toshow = $em->getRepository("App:MotuDataset")->createQueryBuilder('motu_dataset')
      ->addOrderBy('motu_dataset.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'motu',
        "code" => $entity->getTitle(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the pcr
    $entities_toshow = $em->getRepository("App:Pcr")->createQueryBuilder('pcr')
      ->addOrderBy('pcr.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'pcr',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the sequence
    $entities_toshow = $em->getRepository("App:InternalSequence")->createQueryBuilder('internal_sequence')
      ->addOrderBy('internal_sequence.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'internal_sequence',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the external sequence
    $entities_toshow = $em->getRepository("App:ExternalSequence")->createQueryBuilder('external_sequence')
      ->addOrderBy('external_sequence.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'external_sequence',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }
    // returns the last records of the site
    $entities_toshow = $em->getRepository("App:Site")->createQueryBuilder('site')
      ->addOrderBy('site.metaUpdateDate', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$MetaCreationDate = ($entity->getMetaCreationDate() !== null) ?  $entity->getMetaCreationDate()->format('Y-m-d H:i:s') : null;
      $MetaUpdateDate = ($entity->getMetaUpdateDate() !== null) ? $entity->getMetaUpdateDate()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'site',
        "code" => $entity->getCode(),
        "metaUpdateDate" => $MetaUpdateDate,
        "metaUpdateUser" => $service->GetMetaUpdateUserUserfullname($entity),
      );
    }

    return $this->render('Core/dashboard/index.html.twig', array(
      'samplingCount' => $samplingCounts,
      'nbStation' => $siteCount,
      'internalLotCount' => $internalLotCount,
      'nbExternalLot' => $nbExternalLot,
      'nbSpecimen' => $nbSpecimen,
      'nbSlide' => $nbSlide,
      'nbDna' => $nbDna,
      'nbPcr' => $nbPcr,
      'nbChromatogram' => $nbChromatogram,
      'internalSequenceCount' => $internalSequenceCount,
      'externalSeqCount' => $externalSeqCount,
      'nbMotu' => $nbMotu,
      'nbMotuSqcAss' => $nbMotuSqcAss,
      'nbMotuSqcAssExt' => $nbMotuSqcAssExt,
      'nbBoite' => $nbBoite,
      'nbSource' => $nbSource,
      'nbTaxon' => $nbTaxon,
      'entities' => $tab_toshow,
    ));
  }

  /**
   * @Route("/legal/", name="legal_notices", methods={"GET"})
   */
  public function legalNotices(Request $request) {
    $locale = $request->getLocale();
    return $this->render('misc/legal-notices.html.twig');
  }
}
