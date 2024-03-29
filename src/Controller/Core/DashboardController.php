<?php

namespace App\Controller\Core;

use App\Services\Core\GenericFunctionE3s;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\EntityController;
use App\Entity\Adn;
use App\Entity\Chromatogramme;
use App\Entity\Collecte;
use App\Entity\Individu;
use App\Entity\IndividuLame;
use App\Entity\LotMateriel;
use App\Entity\LotMaterielExt;
use App\Entity\Motu;
use App\Entity\Pcr;
use App\Entity\SequenceAssemblee;
use App\Entity\SequenceAssembleeExt;
use App\Entity\Station;

class DashboardController extends EntityController {

  #[Route("/", name: "dashboard")]
  public function indexAction(ManagerRegistry $doctrine, GenericFunctionE3s $service) {
    $nbcollectes = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Collecte u')->getSingleScalarResult();
    $nbstations = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Station u')->getSingleScalarResult();
    $nbLotMateriel = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:LotMateriel u')->getSingleScalarResult();
    $nbLotMaterielExt = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:LotMaterielExt u')->getSingleScalarResult();
    $nbIndividu = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Individu u')->getSingleScalarResult();
    $nbIndividuLame = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:IndividuLame u')->getSingleScalarResult();
    $nbAdn = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Adn u')->getSingleScalarResult();
    $nbPcr = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Pcr u')->getSingleScalarResult();
    $nbChromatogramme = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Chromatogramme u')->getSingleScalarResult();
    $nbSequenceAssemblee = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:SequenceAssemblee u')->getSingleScalarResult();
    $nbSequenceAssembleeExt = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:SequenceAssembleeExt u')->getSingleScalarResult();
    $nbMotu = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Assigne u')->getSingleScalarResult();
    $nbMotuSqcAss = count($this->entityManager->createQuery('SELECT COUNT(sa.id) FROM App:Assigne u JOIN u.sequenceAssembleeFk sa GROUP BY sa.id')->getResult());
    $nbMotuSqcAssExt = count($this->entityManager->createQuery('SELECT COUNT(sae.id) FROM App:Assigne u JOIN u.sequenceAssembleeExtFk sae GROUP BY sae.id')->getResult());
    $nbBoite = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Boite u')->getSingleScalarResult();
    $nbSource = $this->entityManager->createQuery('SELECT COUNT(u.id) FROM App:Source u')->getSingleScalarResult();
    $nbTaxon = count($this->entityManager->createQuery('SELECT COUNT(rt.id) FROM App:EspeceIdentifiee u JOIN u.referentielTaxonFk rt GROUP BY rt.id')->getResult());
    //
    $tab_toshow = [];
    // returns the last records of the dna
    $entities_toshow = $this->getRepository(Adn::class)
      ->createQueryBuilder('adn')
      ->addOrderBy('adn.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s')
        : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'adn',
        "code" => $entity->getCodeAdn(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the chromatogram
    $entities_toshow = $this->getRepository(Chromatogramme::class)
      ->createQueryBuilder('chromatogramme')
      ->addOrderBy('chromatogramme.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s')
        : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'chromatogramme',
        "code" => $entity->getCodeChromato(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the sampling
    $entities_toshow = $this->getRepository(Collecte::class)
      ->createQueryBuilder('collecte')
      ->addOrderBy('collecte.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null)
        ? $entity->getDateMaj()->format('Y-m-d H:i:s')
        : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'collecte',
        "code" => $entity->getCodeCollecte(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the specimen
    $entities_toshow = $this->getRepository(Individu::class)
      ->createQueryBuilder('individu')
      ->addOrderBy('individu.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'individu',
        "code" => $entity->getCodeIndTriMorpho(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the slide
    $entities_toshow = $this->getRepository(IndividuLame::class)
      ->createQueryBuilder('individulame')
      ->addOrderBy('individulame.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'individulame',
        "code" => $entity->getCodeLameColl(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the lot material
    $entities_toshow = $this->getRepository(LotMateriel::class)->createQueryBuilder('lotMateriel')
      ->addOrderBy('lotMateriel.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'lotmateriel',
        "code" => $entity->getCodeLotMateriel(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the external lot material
    $entities_toshow = $this->getRepository(LotMaterielExt::class)
      ->createQueryBuilder('lotmaterielext')
      ->addOrderBy('lotmaterielext.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'lotmaterielext',
        "code" => $entity->getCodeLotMaterielExt(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the motu
    $entities_toshow = $this->getRepository(Motu::class)
      ->createQueryBuilder('motu')
      ->addOrderBy('motu.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'motu',
        "code" => $entity->getLibelleMotu(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the pcr
    $entities_toshow = $this->getRepository(Pcr::class)
      ->createQueryBuilder('pcr')
      ->addOrderBy('pcr.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'pcr',
        "code" => $entity->getCodePcr(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the sequence
    $entities_toshow = $this->getRepository(SequenceAssemblee::class)
      ->createQueryBuilder('sequenceassemblee')
      ->addOrderBy('sequenceassemblee.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'sequenceassemblee',
        "code" => $entity->getCodeSqcAss(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the external sequence
    $entities_toshow = $this->getRepository(SequenceAssembleeExt::class)
      ->createQueryBuilder('sequenceassembleeext')
      ->addOrderBy('sequenceassembleeext.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'sequenceassembleeext',
        "code" => $entity->getCodeSqcAssExt(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }
    // returns the last records of the site
    $entities_toshow = $this->getRepository(Station::class)
      ->createQueryBuilder('station')
      ->addOrderBy('station.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ? $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'station',
        "code" => $entity->getCodeStation(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUserfullname($entity),
      );
    }

    return $this->render('Core/dashboard/index.html.twig', array(
      'nbCollecte' => $nbcollectes,
      'nbStation' => $nbstations,
      'nbLotMateriel' => $nbLotMateriel,
      'nbLotMaterielExt' => $nbLotMaterielExt,
      'nbIndividu' => $nbIndividu,
      'nbIndividuLame' => $nbIndividuLame,
      'nbAdn' => $nbAdn,
      'nbPcr' => $nbPcr,
      'nbChromatogramme' => $nbChromatogramme,
      'nbSequenceAssemblee' => $nbSequenceAssemblee,
      'nbSequenceAssembleeExt' => $nbSequenceAssembleeExt,
      'nbMotu' => $nbMotu,
      'nbMotuSqcAss' => $nbMotuSqcAss,
      'nbMotuSqcAssExt' => $nbMotuSqcAssExt,
      'nbBoite' => $nbBoite,
      'nbSource' => $nbSource,
      'nbTaxon' => $nbTaxon,
      'entities' => $tab_toshow,
    ));
  }

  #[Route("/legal/", name: "legal_notices", methods: ["GET"])]
  public function legalNotices(Request $request) {
    return $this->render('misc/legal-notices.html.twig');
  }
}
