<?php

namespace Bbees\E3sBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     */
    public function indexAction()
    {
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
        $nbBoite = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Boite u')->getSingleScalarResult();
        $nbSource = $em->createQuery('SELECT COUNT(u.id) FROM BbeesE3sBundle:Source u')->getSingleScalarResult();
        return $this->render('default/index.html.twig', array( 
            'nbcollectes' => $nbcollectes,
            'nbstations' => $nbstations,
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
            'nbBoite' => $nbBoite,
            'nbSource' => $nbSource,
            ));
    }
}
