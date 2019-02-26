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

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Bbees\E3sBundle\Entity\Collecte;
use Bbees\E3sBundle\Entity\Station;
use Bbees\E3sBundle\Entity\LotMateriel;
use Bbees\E3sBundle\Entity\LotMaterielExt;
use Bbees\E3sBundle\Entity\Individu;
use Bbees\E3sBundle\Entity\IndividuLame;
use Bbees\E3sBundle\Entity\Adn;
use Bbees\E3sBundle\Entity\Pcr;
use Bbees\E3sBundle\Entity\Chromatogramme;
use Bbees\E3sBundle\Entity\SequenceAssemblee;
use Bbees\E3sBundle\Entity\SequenceAssembleeExt;
use Bbees\E3sBundle\Entity\Motu;
use Bbees\E3sBundle\Entity\Boite;
use Bbees\E3sBundle\Entity\Source;
use Bbees\E3sBundle\Services\GenericFunctionService;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     * @Security("has_role('ROLE_INVITED')")
     * @author Philippe Grison  <philippe.grison@mnhn.fr>
     */
    public function indexAction()
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s'); 
        //
        $em = $this->getDoctrine()->getManager();
        $nbcollectes = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Collecte u')->getSingleScalarResult();
        $nbstations = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Station u')->getSingleScalarResult();
        $nbLotMateriel = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:LotMateriel u')->getSingleScalarResult();
        $nbLotMaterielExt = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:LotMaterielExt u')->getSingleScalarResult();
        $nbIndividu = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Individu u')->getSingleScalarResult();
        $nbIndividuLame = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:IndividuLame u')->getSingleScalarResult();
        $nbAdn = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Adn u')->getSingleScalarResult();
        $nbPcr = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Pcr u')->getSingleScalarResult();
        $nbChromatogramme = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Chromatogramme u')->getSingleScalarResult();
        $nbSequenceAssemblee = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:SequenceAssemblee u')->getSingleScalarResult();
        $nbSequenceAssembleeExt = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:SequenceAssembleeExt u')->getSingleScalarResult();
        $nbMotu = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Assigne u')->getSingleScalarResult();
        $nbMotuSqcAss = count($em->createQuery('SELECT COUNT(sa.id) FROM BbeesE3sBundle:Assigne u JOIN u.sequenceAssembleeFk sa GROUP BY sa.id')->getResult());
        $nbMotuSqcAssExt = count($em->createQuery('SELECT COUNT(sae.id) FROM BbeesE3sBundle:Assigne u JOIN u.sequenceAssembleeExtFk sae GROUP BY sae.id')->getResult());
        $nbBoite = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Boite u')->getSingleScalarResult();
        $nbSource = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Source u')->getSingleScalarResult();
        $nbTaxon = count($em->createQuery('SELECT COUNT(rt.id) FROM BbeesE3sBundle:EspeceIdentifiee u JOIN u.referentielTaxonFk rt GROUP BY rt.id')->getResult());
        //
        $tab_toshow =[];
        // retourne les derniers enregistrements des adn
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Adn")->createQueryBuilder('adn')
            ->addOrderBy('adn.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'adn',
            "code" => $entity->getCodeAdn(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
        // retourne les derniers enregistrements des chromatogramme
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Chromatogramme")->createQueryBuilder('chromatogramme')
            ->addOrderBy('chromatogramme.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'chromatogramme',
            "code" => $entity->getCodeChromato(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }
        // retourne les derniers enregistrements des collectes
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Collecte")->createQueryBuilder('collecte')
            ->addOrderBy('collecte.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'collecte',
            "code" => $entity->getCodeCollecte(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }  
        // retourne les derniers enregistrements des individu
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Individu")->createQueryBuilder('individu')
            ->addOrderBy('individu.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'individu',
            "code" => $entity->getCodeIndTriMorpho(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
        // retourne les derniers enregistrements des lame
        $entities_toshow = $em->getRepository("BbeesE3sBundle:IndividuLame")->createQueryBuilder('individulame')
            ->addOrderBy('individulame.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'individulame',
            "code" => $entity->getCodeLameColl(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
        // retourne les derniers enregistrements des lots
        $entities_toshow = $em->getRepository("BbeesE3sBundle:LotMateriel")->createQueryBuilder('lotMateriel')
            ->addOrderBy('lotMateriel.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'lotmateriel',
            "code" => $entity->getCodeLotMateriel(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
        // retourne les derniers enregistrements des lots ext
        $entities_toshow = $em->getRepository("BbeesE3sBundle:LotMaterielExt")->createQueryBuilder('lotmaterielext')
            ->addOrderBy('lotmaterielext.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'lotmaterielext',
            "code" => $entity->getCodeLotMaterielExt(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }
        // retourne les derniers enregistrements des motu
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Motu")->createQueryBuilder('motu')
            ->addOrderBy('motu.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'motu',
            "code" => $entity->getLibelleMotu(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }
        // retourne les derniers enregistrements des pcr
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Pcr")->createQueryBuilder('pcr')
            ->addOrderBy('pcr.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'pcr',
            "code" => $entity->getCodePcr(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }
        // retourne les derniers enregistrements des sequence
        $entities_toshow = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->createQueryBuilder('sequenceassemblee')
            ->addOrderBy('sequenceassemblee.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'sequenceassemblee',
            "code" => $entity->getCodeSqcAss(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
                // retourne les derniers enregistrements des sequenceext
        $entities_toshow = $em->getRepository("BbeesE3sBundle:SequenceAssembleeExt")->createQueryBuilder('sequenceassembleeext')
            ->addOrderBy('sequenceassembleeext.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'sequenceassembleeext',
            "code" => $entity->getCodeSqcAssExt(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        } 
        // retourne les derniers enregistrements des stations
        $entities_toshow  = $em->getRepository("BbeesE3sBundle:Station")->createQueryBuilder('station')
            ->addOrderBy('station.dateMaj', 'DESC')
            ->setMaxResults( 25 )
            ->getQuery()
            ->getResult();             
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id,
            "name" => 'station',
            "code" => $entity->getCodeStation(),
            "dateMaj" => $DateMaj,
            "userMaj" => $service->GetUserMajUsername($entity),
             );
        }

        return $this->render('default/index.html.twig', array( 
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
     * @Route("/mapstations/", name="mapstations")
     * @Method("POST")
     */
    public function geoCoords(Request $request){
        $data = $request->request;
        $latitude = $data->get('latitude');
        $longitude = $data->get('longitude');
        $diffLatitudeLongitude = 1;
        //
        $em = $this->getDoctrine()->getManager();
              
        $tab_toshow =[];
        $entities_toshow = $em->getRepository("BbeesE3sBundle:Station")->createQueryBuilder('station')
            ->getQuery()
            ->getResult();
        $nb_entities = count($entities_toshow);
        foreach($entities_toshow as $entity)
        {
            $id = $entity->getId();
            //$DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            //$DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array("id" => $id, "station.id" => $id, 
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
