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
use App\Services\Core\GenericFunctionE3s;
use App\Entity\Station;
use App\Entity\Source;
use App\Entity\SequenceAssembleeExt;
use App\Entity\SequenceAssemblee;
use App\Entity\Pcr;
use App\Entity\Motu;
use App\Entity\LotMaterielExt;
use App\Entity\LotMateriel;
use App\Entity\IndividuLame;
use App\Entity\Individu;
use App\Entity\Collecte;
use App\Entity\Chromatogramme;
use App\Entity\Boite;
use App\Entity\Adn;

class DefaultController extends AbstractController
{
  /**
   * @Route("/", name="default_index")
   * @Security("has_role('ROLE_INVITED')")
   * @author Philippe Grison  <philippe.grison@mnhn.fr>
   */
  public function indexAction(GenericFunctionE3s $service)
  {
    // load Doctrine Manager
    $em = $this->getDoctrine()->getManager();
    //
    $nbcollectes = $em->createQuery('SELECT COUNT(u.id) FROM App:Collecte u')->getSingleScalarResult();
    $nbstations = $em->createQuery('SELECT COUNT(u.id) FROM App:Station u')->getSingleScalarResult();
    $nbLotMateriel = $em->createQuery('SELECT COUNT(u.id) FROM App:LotMateriel u')->getSingleScalarResult();
    $nbLotMaterielExt = $em->createQuery('SELECT COUNT(u.id) FROM App:LotMaterielExt u')->getSingleScalarResult();
    $nbIndividu = $em->createQuery('SELECT COUNT(u.id) FROM App:Individu u')->getSingleScalarResult();
    $nbIndividuLame = $em->createQuery('SELECT COUNT(u.id) FROM App:IndividuLame u')->getSingleScalarResult();
    $nbAdn = $em->createQuery('SELECT COUNT(u.id) FROM App:Adn u')->getSingleScalarResult();
    $nbPcr = $em->createQuery('SELECT COUNT(u.id) FROM App:Pcr u')->getSingleScalarResult();
    $nbChromatogramme = $em->createQuery('SELECT COUNT(u.id) FROM App:Chromatogramme u')->getSingleScalarResult();
    $nbSequenceAssemblee = $em->createQuery('SELECT COUNT(u.id) FROM App:SequenceAssemblee u')->getSingleScalarResult();
    $nbSequenceAssembleeExt = $em->createQuery('SELECT COUNT(u.id) FROM App:SequenceAssembleeExt u')->getSingleScalarResult();
    $nbMotu = $em->createQuery('SELECT COUNT(u.id) FROM App:Assigne u')->getSingleScalarResult();
    $nbMotuSqcAss = count($em->createQuery('SELECT COUNT(sa.id) FROM App:Assigne u JOIN u.sequenceAssembleeFk sa GROUP BY sa.id')->getResult());
    $nbMotuSqcAssExt = count($em->createQuery('SELECT COUNT(sae.id) FROM App:Assigne u JOIN u.sequenceAssembleeExtFk sae GROUP BY sae.id')->getResult());
    $nbBoite = $em->createQuery('SELECT COUNT(u.id) FROM App:Boite u')->getSingleScalarResult();
    $nbSource = $em->createQuery('SELECT COUNT(u.id) FROM App:Source u')->getSingleScalarResult();
    $nbTaxon = count($em->createQuery('SELECT COUNT(rt.id) FROM App:EspeceIdentifiee u JOIN u.referentielTaxonFk rt GROUP BY rt.id')->getResult());
    //
    $tab_toshow = [];
    // returns the last records of the dna
    $entities_toshow = $em->getRepository("App:Adn")->createQueryBuilder('adn')
      ->addOrderBy('adn.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'adn',
        "code" => $entity->getCodeAdn(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the chromatogram
    $entities_toshow = $em->getRepository("App:Chromatogramme")->createQueryBuilder('chromatogramme')
      ->addOrderBy('chromatogramme.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'chromatogramme',
        "code" => $entity->getCodeChromato(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the sampling
    $entities_toshow = $em->getRepository("App:Collecte")->createQueryBuilder('collecte')
      ->addOrderBy('collecte.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'collecte',
        "code" => $entity->getCodeCollecte(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the specimen
    $entities_toshow = $em->getRepository("App:Individu")->createQueryBuilder('individu')
      ->addOrderBy('individu.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'individu',
        "code" => $entity->getCodeIndTriMorpho(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the slide
    $entities_toshow = $em->getRepository("App:IndividuLame")->createQueryBuilder('individulame')
      ->addOrderBy('individulame.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'individulame',
        "code" => $entity->getCodeLameColl(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the lot material
    $entities_toshow = $em->getRepository("App:LotMateriel")->createQueryBuilder('lotMateriel')
      ->addOrderBy('lotMateriel.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'lotmateriel',
        "code" => $entity->getCodeLotMateriel(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the external lot material
    $entities_toshow = $em->getRepository("App:LotMaterielExt")->createQueryBuilder('lotmaterielext')
      ->addOrderBy('lotmaterielext.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'lotmaterielext',
        "code" => $entity->getCodeLotMaterielExt(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the motu
    $entities_toshow = $em->getRepository("App:Motu")->createQueryBuilder('motu')
      ->addOrderBy('motu.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'motu',
        "code" => $entity->getLibelleMotu(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the pcr
    $entities_toshow = $em->getRepository("App:Pcr")->createQueryBuilder('pcr')
      ->addOrderBy('pcr.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'pcr',
        "code" => $entity->getCodePcr(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the sequence
    $entities_toshow = $em->getRepository("App:SequenceAssemblee")->createQueryBuilder('sequenceassemblee')
      ->addOrderBy('sequenceassemblee.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'sequenceassemblee',
        "code" => $entity->getCodeSqcAss(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the external sequence
    $entities_toshow = $em->getRepository("App:SequenceAssembleeExt")->createQueryBuilder('sequenceassembleeext')
      ->addOrderBy('sequenceassembleeext.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'sequenceassembleeext',
        "code" => $entity->getCodeSqcAssExt(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
      );
    }
    // returns the last records of the site
    $entities_toshow  = $em->getRepository("App:Station")->createQueryBuilder('station')
      ->addOrderBy('station.dateMaj', 'DESC')
      ->setMaxResults(25)
      ->getQuery()
      ->getResult();
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id,
        "name" => 'station',
        "code" => $entity->getCodeStation(),
        "dateMaj" => $DateMaj,
        "userMaj" => $service->GetUserMajUsername($entity),
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

  /**
   * @Route("/mapstations/", name="mapstations", methods={"POST"})
   */
  public function geoCoords(Request $request)
  {
    $data = $request->request;
    $latitude = $data->get('latitude');
    $longitude = $data->get('longitude');
    $diffLatitudeLongitude = 1;
    //
    $em = $this->getDoctrine()->getManager();

    $tab_toshow = [];
    $entities_toshow = $em->getRepository("App:Station")->createQueryBuilder('station')
      ->getQuery()
      ->getResult();
    $nb_entities = count($entities_toshow);
    foreach ($entities_toshow as $entity) {
      $id = $entity->getId();
      //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
      //$DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
      $tab_toshow[] = array(
        "id" => $id, "station.id" => $id,
        "station.codeStation" => $entity->getCodeStation(),
        "station.nomStation" => $entity->getNomStation(),
        "commune.codeCommune" => $entity->getCommuneFk()->getCodeCommune(),
        "pays.codePays" => $entity->getPaysFk()->getCodePays(),
        "station.latDegDec" => $entity->getLatDegDec(),
        "station.longDegDec" => $entity->getLongDegDec()
      );
    }

    return new JsonResponse(array(
      'stations' => $tab_toshow,
      'latitude' => $latitude,
      'longitude' => $longitude,
    ));
  }
}
