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

use Bbees\E3sBundle\Entity\SequenceAssemblee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Bbees\E3sBundle\Services\GenericFunctionService;
use Bbees\E3sBundle\Entity\Pcr;
use Bbees\E3sBundle\Entity\Voc;
use Bbees\E3sBundle\Entity\Individu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Sequenceassemblee controller.
 *
 * @Route("sequenceassemblee")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeController extends Controller
{
    /**
    * @var integer
    */
    private $geneVocFk = null;
    private $individuFk = null;
    /**
     * constante 
     */
    const DATEINF_SQCALIGNEMENT_AUTO = '2018-05-01';
    
    /**
     * Lists all sequenceAssemblee entities.
     *
     * @Route("/", name="sequenceassemblee_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sequenceAssemblees = $em->getRepository('BbeesE3sBundle:SequenceAssemblee')->findAll();

        return $this->render('sequenceassemblee/index.html.twig', array(
            'sequenceAssemblees' => $sequenceAssemblees,
        ));
    }

     /**
     * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="sequenceassemblee_indexjson")
     * @Method("POST")
     */
    public function indexjsonAction(Request $request)
    {
        // load services
        $service = $this->get('bbees_e3s.generic_function_e3s');       
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('sequenceAssemblee.dateMaj' => 'desc', 'sequenceAssemblee.id' => 'desc');  
        $minRecord = intval($request->get('current')-1)*$rowCount;
        $maxRecord = $rowCount; 
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(sequenceAssemblee.codeSqcAss) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ( $request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        // Search for the list to show EstAligneEtTraite
        $tab_toshow =[];
        $toshow = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->createQueryBuilder('sequenceAssemblee')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase).'%')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocStatutSqcAss', 'WITH', 'sequenceAssemblee.statutSqcAssVocFk = vocStatutSqcAss.id')
            ->leftJoin('BbeesE3sBundle:EstAligneEtTraite', 'eaet', 'WITH', 'eaet.sequenceAssembleeFk = sequenceAssemblee.id')
            ->leftJoin('BbeesE3sBundle:Chromatogramme', 'chromatogramme', 'WITH', 'eaet.chromatogrammeFk = chromatogramme.id')
            ->leftJoin('BbeesE3sBundle:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
            ->leftJoin('BbeesE3sBundle:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
            ->leftJoin('BbeesE3sBundle:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
            ->leftJoin('BbeesE3sBundle:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
            ->groupBy('sequenceAssemblee.id')
            ->addGroupBy('vocStatutSqcAss.code')
            ->addGroupBy('sequenceAssemblee.codeSqcAlignement')
            ->addGroupBy('sequenceAssemblee.dateCreationSqcAss')
            ->addGroupBy('sequenceAssemblee.dateCre')
            ->addGroupBy('sequenceAssemblee.dateMaj')
            ->addGroupBy('individu.codeIndBiomol')
            ->addGroupBy('vocGene.code')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($toshow);
        $toshow = array_slice($toshow, $minRecord, $rowCount);  
        $lastTaxname = '';
        foreach($toshow as $entity)
        {
            $id = $entity->getId();
            $DateCreationSqcAss = ($entity->getDateCreationSqcAss() !== null) ?  $entity->getDateCreationSqcAss()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;       
            // Search for the number of sequence associated to a chromatogram
            $query = $em->createQuery('SELECT eaet.id, voc.libelle as gene, individu.codeIndBiomol as code_ind_biomol FROM BbeesE3sBundle:EstAligneEtTraite eaet JOIN eaet.chromatogrammeFk chromato JOIN chromato.pcrFk pcr JOIN pcr.geneVocFk voc JOIN pcr.adnFk adn JOIN adn.individuFk individu WHERE eaet.sequenceAssembleeFk = '.$id.' ORDER BY eaet.id DESC')->getResult();
            $geneSeqAss = (count($query) > 0) ? $query[0]['gene'] : '';
            $codeIndBiomol = (count($query) > 0) ? $query[0]['code_ind_biomol'] : '';
            // search for motu associated to a sequence
            $query = $em->createQuery('SELECT a.id FROM BbeesE3sBundle:Assigne a JOIN a.sequenceAssembleeFk sqc  WHERE a.sequenceAssembleeFk = '.$id.' ')->getResult();
            $motuAssigne = (count($query) > 0) ? 1 : 0;
            // load the first identified taxon            
            $query = $em->createQuery('SELECT ei.id, ei.dateIdentification, rt.taxname as taxname, voc.code as codeIdentification FROM BbeesE3sBundle:EspeceIdentifiee ei JOIN ei.referentielTaxonFk rt JOIN ei.critereIdentificationVocFk voc WHERE ei.sequenceAssembleeFk = '.$id.' ORDER BY ei.id DESC')->getResult(); 
            $lastTaxname = ($query[0]['taxname'] !== NULL) ? $query[0]['taxname'] : NULL;
            $lastdateIdentification = ($query[0]['dateIdentification']  !== NULL) ? $query[0]['dateIdentification']->format('Y-m-d') : NULL; 
            $codeIdentification = ($query[0]['codeIdentification'] !== NULL) ? $query[0]['codeIdentification'] : NULL;
            // Search for sousrces associated to a sequence
            $query = $em->createQuery('SELECT s.codeSource as source FROM BbeesE3sBundle:SqcEstPublieDans sepd JOIN sepd.sourceFk s WHERE sepd.sequenceAssembleeFk = '.$id.'')->getResult();            
            $arrayListeSource = array();
            foreach($query as $taxon) {
                 $arrayListeSource[] = $taxon['source'];
            }
            $listSource = implode(", ", $arrayListeSource);
            //
            $tab_toshow[] = array("id" => $id, "sequenceAssemblee.id" => $id, 
             "individu.codeIndBiomol" => $codeIndBiomol,
             "sequenceAssemblee.codeSqcAlignement" => $entity->getCodeSqcAlignement(),
             "sequenceAssemblee.codeSqcAss" => $entity->getCodeSqcAss(),
             "sequenceAssemblee.accessionNumber" => $entity->getAccessionNumber(),
             "vocGene.code" => $geneSeqAss, 
             "vocStatutSqcAss.code" => $entity->getStatutSqcAssVocFk()->getCode(),                 
             "sequenceAssemblee.dateCreationSqcAss" => $DateCreationSqcAss,   
             "lastTaxname" => $lastTaxname,  
             "listSource" => $listSource, 
             "lastdateIdentification" => $lastdateIdentification ,
             "codeIdentification" => $codeIdentification ,
             "motuAssigne" => $motuAssigne ,   
             "sequenceAssemblee.dateCre" => $DateCre, "sequenceAssemblee.dateMaj" => $DateMaj, 
             "userCreId" => $service->GetUserCreId($entity), "sequenceAssemblee.userCre" => $service->GetUserCreUsername($entity) ,"sequenceAssemblee.userMaj" => $service->GetUserMajUsername($entity),
             );
        }     
        // Ajax answer
        $response = new Response ();
        $response->setContent ( json_encode ( array (
            "current"    => intval( $request->get('current') ), 
            "rowCount"  => $rowCount,            
            "rows"     => $tab_toshow, 
            "searchPhrase" => $searchPhrase,
            "total"    => $nb // total data array				
            ) ) );
        // If it is an Ajax request: returns the content in json format
        $response->headers->set('Content-Type', 'application/json');

        return $response;          
    } 

    
    /**
     * Creates a new sequenceAssemblee entity.
     *
     * @Route("/new", name="sequenceassemblee_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {

        $sequenceAssemblee = new Sequenceassemblee();  

        // management of the form GeneIndbiomolForm
        $this->geneVocFk = ($request->get('geneVocFk')!== null && $request->get('geneVocFk') != '') ? $request->get('geneVocFk') : null ;
        $this->individuFk = ($request->get('individuFk')!== null && $request->get('individuFk') != '') ? $request->get('individuFk') : null ;
        $form_gene_indbiomol = $this->createGeneIndbiomolForm($sequenceAssemblee, $this->geneVocFk, $this->individuFk );
        $form_gene_indbiomol->handleRequest($request); 
        if ($form_gene_indbiomol->isSubmitted() && $form_gene_indbiomol->isValid()) { 
            $this->geneVocFk = $form_gene_indbiomol->get('geneVocFk')->getData()->getId();
            $this->individuFk = $form_gene_indbiomol->get('individuFk')->getData()->getId();
            $sequenceAssemblee->setGeneVocFk($form_gene_indbiomol->get('geneVocFk')->getData()->getId());
            $sequenceAssemblee->setIndividuFk($form_gene_indbiomol->get('individuFk')->getData()->getId());
        } 

        // management of the form SequenceAssembleeType
        $form_gene_indbiomol = $this->createGeneIndbiomolForm($sequenceAssemblee, $this->geneVocFk, $this->individuFk );
        $form = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeType', $sequenceAssemblee, ['geneVocFk' => $this->geneVocFk, 'individuFk' => $this->individuFk ]);
        $form->handleRequest($request);        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sequenceAssemblee);
            // on initialise le code sqcAlignement : setCodeSqcAlignement($codeSqcAlignement)
            $CodeSqcAlignement = $this->createCodeSqcAlignement($sequenceAssemblee);
            $sequenceAssemblee->setCodeSqcAlignement($CodeSqcAlignement);
            $em->persist($sequenceAssemblee);
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassemblee/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            return $this->redirectToRoute('sequenceassemblee_edit', array('id' => $sequenceAssemblee->getId(), 'valid' => 1));
        }       
        return $this->render('sequenceassemblee/edit.html.twig', array(
                                'sequenceAssemblee' => $sequenceAssemblee,
                                'edit_form' => $form->createView(),
                                'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
                                'geneVocFk' => $this->geneVocFk,
                                'individuFk' => $this->individuFk,
        ));
    }

    /**
     * Finds and displays a sequenceAssemblee entity.
     *
     * @Route("/{id}", name="sequenceassemblee_show")
     * @Method("GET")
     */
    public function showAction(SequenceAssemblee $sequenceAssemblee)
    {
        // Recherche du gene et de l'individu pour la sequenceAssemblee
        $em = $this->getDoctrine()->getManager();
        $id = $sequenceAssemblee->getId();
        $query = $em->createQuery('SELECT eaet.id, voc.id as geneVocFk, individu.id as individuFk FROM BbeesE3sBundle:EstAligneEtTraite eaet JOIN eaet.chromatogrammeFk chromato JOIN chromato.pcrFk pcr JOIN pcr.geneVocFk voc JOIN pcr.adnFk adn JOIN adn.individuFk individu WHERE eaet.sequenceAssembleeFk = '.$id.' ORDER BY eaet.id DESC')->getResult();
        $this->geneVocFk  = (count($query) > 0) ? $query[0]['geneVocFk'] : '';
        $this->individuFk  = (count($query) > 0) ? $query[0]['individuFk'] : ''; 
        //
        $deleteForm = $this->createDeleteForm($sequenceAssemblee);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeType', $sequenceAssemblee, ['geneVocFk' => $this->geneVocFk , 'individuFk' => $this->individuFk]);
        //
        $form_gene_indbiomol = $this->createGeneIndbiomolForm($sequenceAssemblee, $this->geneVocFk, $this->individuFk ); 

        return $this->render('show.html.twig', array(
            'sequenceAssemblee' => $sequenceAssemblee,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sequenceAssemblee entity.
     *
     * @Route("/{id}/edit", name="sequenceassemblee_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, SequenceAssemblee $sequenceAssemblee)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $sequenceAssemblee->getUserCre() != $user->getId() ) {
                $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        $service = $this->get('bbees_e3s.generic_function_e3s');
        
        // Recherche du gene et de l'individu pour la sequence
        $em = $this->getDoctrine()->getManager();
        $id = $sequenceAssemblee->getId();
        $query = $em->createQuery('SELECT eaet.id, voc.id as geneVocFk, individu.id as individuFk FROM BbeesE3sBundle:EstAligneEtTraite eaet JOIN eaet.chromatogrammeFk chromato JOIN chromato.pcrFk pcr JOIN pcr.geneVocFk voc JOIN pcr.adnFk adn JOIN adn.individuFk individu WHERE eaet.sequenceAssembleeFk = '.$id.' ORDER BY eaet.id DESC')->getResult();
        $this->geneVocFk  = (count($query) > 0) ? $query[0]['geneVocFk'] : '';
        $this->individuFk  = (count($query) > 0) ? $query[0]['individuFk'] : '';  
        $form_gene_indbiomol = $this->createGeneIndbiomolForm($sequenceAssemblee, $this->geneVocFk, $this->individuFk );        
                
        // store ArrayCollection       
        $estAligneEtTraites = $service->setArrayCollection('EstAligneEtTraites',$sequenceAssemblee);
        $especeIdentifiees = $service->setArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$sequenceAssemblee);
        $sqcEstPublieDanss = $service->setArrayCollection('SqcEstPublieDanss',$sequenceAssemblee);
        $sequenceAssembleeEstRealisePars = $service->setArrayCollection('SequenceAssembleeEstRealisePars',$sequenceAssemblee);
       
        $deleteForm = $this->createDeleteForm($sequenceAssemblee);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeType', $sequenceAssemblee, ['geneVocFk' => $this->geneVocFk , 'individuFk' => $this->individuFk]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollection('EstAligneEtTraites',$sequenceAssemblee, $estAligneEtTraites);
            $service->DelArrayCollectionEmbed('EspeceIdentifiees','EstIdentifiePars',$sequenceAssemblee, $especeIdentifiees);
            $service->DelArrayCollection('SqcEstPublieDanss',$sequenceAssemblee, $sqcEstPublieDanss);
            $service->DelArrayCollection('SequenceAssembleeEstRealisePars',$sequenceAssemblee, $sequenceAssembleeEstRealisePars);
            $em->persist($sequenceAssemblee); 
            try {
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassemblee/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            } 
            $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeType', $sequenceAssemblee, ['geneVocFk' => $this->geneVocFk , 'individuFk' => $this->individuFk]);
            return $this->render('sequenceassemblee/edit.html.twig', array(
                'sequenceAssemblee' => $sequenceAssemblee,
                'edit_form' => $editForm->createView(),
                'valid' => 1,
                'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
                ));
        }

        return $this->render('sequenceassemblee/edit.html.twig', array(
            'sequenceAssemblee' => $sequenceAssemblee,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
        ));
    }

    /**
     * Deletes a sequenceAssemblee entity.
     *
     * @Route("/{id}", name="sequenceassemblee_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function deleteAction(Request $request, SequenceAssemblee $sequenceAssemblee)
    {
        $form = $this->createDeleteForm($sequenceAssemblee);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken) ) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($sequenceAssemblee);
                $em->flush();
            } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                return $this->render('sequenceassemblee/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }   
        }

        return $this->redirectToRoute('sequenceassemblee_index');
    }

    /**
     * Creates a form to delete a sequenceAssemblee entity.
     *
     * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SequenceAssemblee $sequenceAssemblee)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sequenceassemblee_delete', array('id' => $sequenceAssemblee->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
    * Creates a form  : createGeneIndbiomolForm(SequenceAssemblee $sequenceAssemblee = null, $geneVocFk = null, $individuFk = null)
    *
    * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createGeneIndbiomolForm(SequenceAssemblee $sequenceAssemblee = null, $geneVocFk = null, $individuFk = null)
    {       
        if ($sequenceAssemblee->getId() == null && $geneVocFk == null) {
            return $this->createFormBuilder()
                    ->setMethod('POST')
                    ->add('geneVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                           'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('voc')
                                        ->where('voc.parent LIKE :parent')
                                        ->setParameter('parent', 'gene')
                                        ->orderBy('voc.libelle', 'ASC');
                            }, 
                        'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => 'Choose a gene')) 
                     ->add('individuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Individu',
                             'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ind')
                                   ->where('ind.codeIndBiomol IS NOT NULL')
                                   ->orderBy('ind.codeIndBiomol', 'ASC');
                            }, 
                             'placeholder' => 'Choose an individu', 'choice_label' => 'code_ind_biomol', 'multiple' => false, 'expanded' => false))
                    ->add('buton.Valid', SubmitType::class, array('label' => 'buton.Valid', 'attr' => array('class' => 'btn btn-round btn-success')))
                    ->getForm()
            ;
        } 
        if ( $geneVocFk != null && $individuFk != null ) {
            $options = ['geneVocFk'=>$geneVocFk ,'individuFk'=>$individuFk ];
            return $this->createFormBuilder()
                    ->setMethod('POST')
                    ->add('geneVocFk', EntityType::class, array('class' => 'BbeesE3sBundle:Voc', 
                           'query_builder' => function (EntityRepository $er) use($options) {
                                return $er->createQueryBuilder('voc')
                                        ->where('voc.id = :geneVocFk')
                                        ->setParameter('geneVocFk', $options['geneVocFk'])
                                        ->orderBy('voc.libelle', 'ASC');
                            }, 
                        'choice_label' => 'libelle', 'multiple' => false, 'expanded' => false,'placeholder' => false)) 
                     ->add('individuFk',EntityType::class, array('class' => 'BbeesE3sBundle:Individu',
                             'query_builder' => function (EntityRepository $er) use($options) {
                                return $er->createQueryBuilder('ind')
                                   ->where('ind.id = :individuFk')
                                   ->setParameter('individuFk', $options['individuFk'])
                                   ->orderBy('ind.codeIndBiomol', 'ASC');
                            }, 
                             'placeholder' => false, 'choice_label' => 'code_ind_biomol', 'multiple' => false, 'expanded' => false))                                   
                    ->add('buton.Valid', SubmitType::class, array('label' => 'buton.Valid', 'attr' => array('class' => 'btn btn-round btn-success')))
                    ->getForm()
            ;
        } 
        
    }
    
    /**
     * Creates a createCodeSqcAlignement
     *
     * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
     *
     */
    private function createCodeSqcAlignement(SequenceAssemblee $sequenceAssemblee = null, $geneVocFk = null, $individuFk = null)
    {  
        $codeSqcAlignement = '';
        $em = $this->getDoctrine()->getManager();
        $EspeceIdentifiees =  $sequenceAssemblee->getEspeceIdentifiees();
        $nbEspeceIdentifiees = count($EspeceIdentifiees);
        $eaetId = $sequenceAssemblee->getEstAligneEtTraites()[0]->getId();
        $nbChromato = count($sequenceAssemblee->getEstAligneEtTraites());
        
        if( $nbChromato > 0 && $nbEspeceIdentifiees>0 ) {
            // The status of the sequence DNA the referential Taxon = to the last taxname attributed
            $codeStatutSqcAss = $sequenceAssemblee->getStatutSqcAssVocFk()->getCode();
            $lastCodeTaxon = $EspeceIdentifiees[$nbEspeceIdentifiees-1]->getReferentielTaxonFk()->getCodeTaxon();
            $codeSqcAlignement = (substr($codeStatutSqcAss, 0, 5)=='VALID') ? $lastCodeTaxon : $codeStatutSqcAss.'_'.$lastCodeTaxon;           
            $Chromatogramme1 = $sequenceAssemblee->getEstAligneEtTraites()[0]->getChromatogrammeFk();
            $numIndBiomol = $Chromatogramme1->getPcrFk()->getAdnFk()->getIndividuFk()->getNumIndBiomol();
            $codeCollecte = $Chromatogramme1->getPcrFk()->getAdnFk()->getIndividuFk()->getLotMaterielFk()->getCollecteFk()->getCodeCollecte();
            $codeSqcAlignement = $codeSqcAlignement.'_'.$codeCollecte.'_'.$numIndBiomol;
            //  the concaténation [chromatogramme.code_chromato|pcr.specificite_voc_fk(voc.code)]
            $arrayCodeChromato = array();
            foreach ($sequenceAssemblee->getEstAligneEtTraites() as $entityEstAligneEtTraites) {
                 $codeChromato = $entityEstAligneEtTraites->getChromatogrammeFk()->getCodeChromato();
                 $specificite = $entityEstAligneEtTraites->getChromatogrammeFk()->getPcrFk()->getSpecificiteVocFk()->getCode();
                 $arrayCodeChromato[] = $codeChromato.'|'.$specificite;
            }
            sort($arrayCodeChromato);
            $listeCodeChromato = implode("-", $arrayCodeChromato);
            $codeSqcAlignement = $codeSqcAlignement.'_'.$listeCodeChromato;
        } else {
            $codeSqcAlignement = null;
        }
        return $codeSqcAlignement;        
        
    }
}
