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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\EntityRepository;
use App\Services\Core\GenericFunctionE3s;
use App\Form\Type\GeneType;
use App\Form\Enums\Action;
use App\Entity\SequenceAssemblee;

/**
 * Sequenceassemblee controller.
 *
 * @Route("sequenceassemblee")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class SequenceAssembleeController extends AbstractController
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
   * @Route("/", name="sequenceassemblee_index", methods={"GET"})
   */
  public function indexAction()
  {
    $em = $this->getDoctrine()->getManager();

    $sequenceAssemblees = $em->getRepository('App:SequenceAssemblee')->findAll();

    return $this->render('Core/sequenceassemblee/index.html.twig', array(
      'sequenceAssemblees' => $sequenceAssemblees,
    ));
  }

  /**
   * Returns in json format a set of fields to display (tab_toshow) with the following criteria: 
   * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
   * b) the number of lines to display ($ request-> get ('rowCount'))
   * c) 1 sort criterion on a collone ($ request-> get ('sort'))
   *
   * @Route("/indexjson", name="sequenceassemblee_indexjson", methods={"POST"})
   */
  public function indexjsonAction(Request $request, GenericFunctionE3s $service)
  {
    // load Doctrine Manager       
    $em = $this->getDoctrine()->getManager();
    //
    $rowCount = ($request->get('rowCount')  !== NULL)
      ? $request->get('rowCount') : 10;
    $orderBy = ($request->get('sort')  !== NULL)
      ? array_keys($request->get('sort'))[0] . " " . array_values($request->get('sort'))[0]
      : "sq.date_of_update DESC, sq.id DESC";
    $minRecord = intval($request->get('current') - 1) * $rowCount;
    // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
    $where = 'LOWER(sq.internal_sequence_code) LIKE :criteriaLower';
    $having = ' ';
    $searchPhrase = $request->get('searchPhrase');
    if (
      $request->get('searchPattern') !== null &&
      $request->get('searchPattern') !== '' && $searchPhrase == ''
    ) {
      $searchPhrase = $request->get('searchPattern');
    }
    if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
      $where .= ' AND chromato.id = ' . $request->get('idFk');
    }

    // Search for the list to show
    $tab_toshow = [];
    $rawSql =   "SELECT 
      sq.id,
      sq.internal_sequence_code,
      sq.internal_sequence_creation_date,
      sq.creation_user_name,
      sq.date_of_creation,
      sq.date_of_update,
      voc_internal_sequence_status.code as code_voc_internal_sequence_status,
      sq.internal_sequence_creation_date,
      sq.internal_sequence_alignment_code,
      sq.internal_sequence_accession_number,
      rt_sq.taxon_name as last_taxname_sq,
      ei_sq.identification_date as last_date_identification_sq,
      voc_sq_identification_criterion.code as code_sq_identification_criterion,
      user_cre.user_name as user_cre_username,
      user_maj.user_name as user_maj_username,
      voc_gene.code as voc_internal_sequence_gene_code,
      string_agg(DISTINCT sp.specimen_molecular_code, ' ;') as list_specimen_molecular_code,
      string_agg(DISTINCT source.source_title, ' ; ') as list_source,
      string_agg(DISTINCT cast( chromato.id as character varying) ,';') as list_chromato ,
      CASE 
          WHEN (count(motu_number.id)=0) THEN 0
          WHEN (count(motu_number.id)>0) THEN 1
      END motu_flag
      FROM  internal_sequence sq
        LEFT JOIN user_db user_cre ON user_cre.id = sq.creation_user_name
        LEFT JOIN user_db user_maj ON user_maj.id = sq.update_user_name 
        LEFT JOIN vocabulary voc_internal_sequence_status 
          ON sq.internal_sequence_status_voc_fk = voc_internal_sequence_status.id
        LEFT JOIN internal_sequence_is_published_in isip 
          ON isip.internal_sequence_fk = sq.id
        LEFT JOIN source ON isip.source_fk = source.id
        LEFT JOIN motu_number ON motu_number.internal_sequence_fk = sq.id
        LEFT JOIN chromatogram_is_processed_to eaet 
          ON eaet.internal_sequence_fk = sq.id  
        LEFT JOIN chromatogram chromato ON eaet.chromatogram_fk = chromato.id
        JOIN pcr ON chromato.pcr_fk = pcr.id
        LEFT JOIN vocabulary voc_gene ON pcr.gene_voc_fk = voc_gene.id 
        JOIN dna ON pcr.dna_fk = dna.id 
        JOIN specimen sp ON dna.specimen_fk = sp.id
        LEFT JOIN identified_species ei_sq ON ei_sq.internal_sequence_fk = sq.id
        INNER JOIN (
          SELECT MAX(ei_sqi.id) AS maxei_sqi 
          FROM identified_species ei_sqi 
          GROUP BY ei_sqi.internal_sequence_fk
        ) ei_sq2 ON (ei_sq.id = ei_sq2.maxei_sqi)
        LEFT JOIN taxon rt_sq ON ei_sq.taxon_fk = rt_sq.id
        LEFT JOIN vocabulary voc_sq_identification_criterion 
          ON ei_sq.identification_criterion_voc_fk = voc_sq_identification_criterion.id"
      . " WHERE " . $where . "
        GROUP BY sq.id,sq.internal_sequence_code, internal_sequence_creation_date,
        sq.creation_user_name, sq.date_of_creation, sq.date_of_update,
        voc_internal_sequence_status.code,
        sq.internal_sequence_creation_date, sq.internal_sequence_alignment_code, sq.internal_sequence_accession_number,
        rt_sq.taxon_name, ei_sq.identification_date, voc_sq_identification_criterion.code,            
        user_cre.user_name, user_maj.user_name,
        voc_gene.code"
      . $having
      . " ORDER BY " . $orderBy;
    // execute query and fill tab to show in the bootgrid list (see index.htm)
    $stmt = $em->getConnection()->prepare($rawSql);
    $stmt->bindValue('criteriaLower', strtolower($searchPhrase) . '%');
    $stmt->execute();
    $entities_toshow = $stmt->fetchAll();
    $nb = count($entities_toshow);
    $entities_toshow = ($request->get('rowCount') > 0)
      ? array_slice($entities_toshow, $minRecord, $rowCount)
      : array_slice($entities_toshow, $minRecord);

    foreach ($entities_toshow as $key => $val) {
      $tab_toshow[] = array(
        "id" => $val['id'], "sq.id" => $val['id'],
        "internal_sequence_code" => $val['internal_sequence_code'],
        "internal_sequence_alignment_code" => $val['internal_sequence_alignment_code'],
        "internal_sequence_accession_number" => $val['internal_sequence_accession_number'],
        "voc_internal_sequence_gene_code" => $val['voc_internal_sequence_gene_code'],
        "voc_internal_sequence_status.code" => $val['code_voc_internal_sequence_status'],
        "sq.internal_sequence_creation_date" => $val['internal_sequence_creation_date'],
        "list_specimen_molecular_code" => $val['list_specimen_molecular_code'],
        "list_source" => $val['list_source'],
        "list_chromato" => $val['list_chromato'],
        "internal_sequence_creation_date" => $val['internal_sequence_creation_date'],
        "sq.date_of_creation" => $val['date_of_creation'],
        "sq.date_of_update" => $val['date_of_update'],
        "last_taxname_sq" => $val['last_taxname_sq'],
        "last_date_identification_sq" => $val['last_date_identification_sq'],
        "code_sq_identification_criterion" => $val['code_sq_identification_criterion'],
        "motu_flag" => $val['motu_flag'],
        "creation_user_name" => $val['creation_user_name'],
        "user_cre.user_name" => $val['user_cre_username'],
        "user_maj.user_name" => $val['user_maj_username']
      );
    }

    return new JsonResponse([
      "current"    => intval($request->get('current')),
      "rowCount"  => $rowCount,
      "rows"     => $tab_toshow,
      "searchPhrase" => $searchPhrase,
      "total"    => $nb // total data array				
    ]);
  }


  /**
   * Creates a new sequenceAssemblee entity.
   *
   * @Route("/new", name="sequenceassemblee_new", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function newAction(Request $request)
  {

    $sequenceAssemblee = new Sequenceassemblee();

    // management of the form GeneIndbiomolForm
    $this->geneVocFk = ($request->get('geneVocFk'))
      ? $request->get('geneVocFk') : null;
    $this->individuFk = ($request->get('individuFk'))
      ? $request->get('individuFk') : null;


    $form_gene_indbiomol = $this->createGeneIndbiomolForm(
      $sequenceAssemblee,
      $this->geneVocFk,
      $this->individuFk
    );
    $form_gene_indbiomol->handleRequest($request);


    if ($form_gene_indbiomol->isSubmitted() && $form_gene_indbiomol->isValid()) {
      $this->geneVocFk = $form_gene_indbiomol->get('geneVocFk')->getData()->getId();
      $this->individuFk = $form_gene_indbiomol->get('individuFk')->getData()->getId();
      $sequenceAssemblee->setGeneVocFk($this->geneVocFk);
      $sequenceAssemblee->setIndividuFk($this->individuFk);
    }
    // case where the idFk url parameter is not null 
    if ($request->get('idFk')) {
      $where = ' chromatogramme.id = ' . $request->get('idFk');
      // Search for the list to show EstAligneEtTraite
      $tab_toshow = [];
      $entities_toshow = $this->getDoctrine()->getManager()
        ->getRepository("App:Chromatogramme")
        ->createQueryBuilder('chromatogramme')
        ->where($where)
        ->leftJoin('App:Pcr', 'pcr', 'WITH', 'chromatogramme.pcrFk = pcr.id')
        ->leftJoin('App:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
        ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
        ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
        ->getQuery()
        ->getResult();
      // set the geneVocFk and individuFk parameteres
      foreach ($entities_toshow as $entity) {
        $this->geneVocFk = $entity->getPcrFk()->getGeneVocFk()->getId();
        $this->individuFk = $entity->getPcrFk()->getAdnFk()->getIndividuFk()->getId();
        $form_gene_indbiomol = $this->createGeneIndbiomolForm(
          $sequenceAssemblee,
          $this->geneVocFk,
          $this->individuFk
        );
        $sequenceAssemblee->setGeneVocFk($this->geneVocFk);
        $sequenceAssemblee->setIndividuFk($this->individuFk);
      }
    }

    // management of the form SequenceAssembleeType
    $form_gene_indbiomol = $this->createGeneIndbiomolForm(
      $sequenceAssemblee,
      $this->geneVocFk,
      $this->individuFk
    );
    $form = $this->createForm(
      'App\Form\SequenceAssembleeType',
      $sequenceAssemblee,
      [
        'geneVocFk' => $this->geneVocFk,
        'individuFk' => $this->individuFk,
        'action_type' => Action::create()
      ]
    );
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
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
      }
      return $this->redirectToRoute('sequenceassemblee_edit', array(
        'id' => $sequenceAssemblee->getId(),
        'valid' => 1,
        'idFk' => $request->get('idFk')
      ));
    }
    return $this->render('Core/sequenceassemblee/edit.html.twig', array(
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
   * @Route("/{id}", name="sequenceassemblee_show", methods={"GET"})
   */
  public function showAction(SequenceAssemblee $sequenceAssemblee)
  {
    // Recherche du gene et de l'individu pour la sequenceAssemblee
    $em = $this->getDoctrine()->getManager();
    $id = $sequenceAssemblee->getId();
    $query = $em->createQuery(
      'SELECT eaet.id, voc.id as geneVocFk, individu.id as individuFk 
            FROM App:EstAligneEtTraite eaet 
            JOIN eaet.chromatogrammeFk chromato 
            JOIN chromato.pcrFk pcr 
            JOIN pcr.geneVocFk voc 
            JOIN pcr.adnFk adn 
            JOIN adn.individuFk individu 
            WHERE eaet.sequenceAssembleeFk = ' . $id .
        ' ORDER BY eaet.id DESC'
    )->getResult();
    $this->geneVocFk  = (count($query) > 0) ? $query[0]['geneVocFk'] : '';
    $this->individuFk  = (count($query) > 0) ? $query[0]['individuFk'] : '';
    //
    $deleteForm = $this->createDeleteForm($sequenceAssemblee);
    $editForm = $this->createForm(
      'App\Form\SequenceAssembleeType',
      $sequenceAssemblee,
      [
        'geneVocFk' => $this->geneVocFk,
        'individuFk' => $this->individuFk,
        'action_type' => Action::show()
      ]
    );
    //
    $form_gene_indbiomol = $this->createGeneIndbiomolForm(
      $sequenceAssemblee,
      $this->geneVocFk,
      $this->individuFk
    );
    return $this->render('Core/sequenceassemblee/edit.html.twig', array(
      'sequenceAssemblee' => $sequenceAssemblee,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
    ));
  }

  /**
   * Displays a form to edit an existing sequenceAssemblee entity.
   *
   * @Route("/{id}/edit", name="sequenceassemblee_edit", methods={"GET", "POST"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function editAction(
    Request $request,
    SequenceAssemblee $sequenceAssemblee,
    GenericFunctionE3s $service
  ) {
    //  access control for user type  : ROLE_COLLABORATION
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $user = $this->getUser();
    if (
      $user->getRole() ==  'ROLE_COLLABORATION' &&
      $sequenceAssemblee->getUserCre() != $user->getId()
    ) {
      $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
    }
    // load service  generic_function_e3s
    // 

    // Recherche du gene et de l'individu pour la sequence
    $em = $this->getDoctrine()->getManager();
    $id = $sequenceAssemblee->getId();
    $query = $em->createQuery(
      'SELECT eaet.id, voc.id as geneVocFk, individu.id as individuFk 
            FROM App:EstAligneEtTraite eaet 
            JOIN eaet.chromatogrammeFk chromato 
            JOIN chromato.pcrFk pcr 
            JOIN pcr.geneVocFk voc 
            JOIN pcr.adnFk adn 
            JOIN adn.individuFk individu 
            WHERE eaet.sequenceAssembleeFk = ' . $id .
        ' ORDER BY eaet.id DESC'
    )->getResult();
    $this->geneVocFk  = (count($query) > 0) ? $query[0]['geneVocFk'] : '';
    $this->individuFk  = (count($query) > 0) ? $query[0]['individuFk'] : '';
    $form_gene_indbiomol = $this->createGeneIndbiomolForm(
      $sequenceAssemblee,
      $this->geneVocFk,
      $this->individuFk
    );

    // store ArrayCollection       
    $estAligneEtTraites = $service->setArrayCollection(
      'EstAligneEtTraites',
      $sequenceAssemblee
    );
    $especeIdentifiees = $service->setArrayCollectionEmbed(
      'EspeceIdentifiees',
      'EstIdentifiePars',
      $sequenceAssemblee
    );
    $sqcEstPublieDanss = $service->setArrayCollection(
      'SqcEstPublieDanss',
      $sequenceAssemblee
    );
    $sequenceAssembleeEstRealisePars = $service->setArrayCollection(
      'SequenceAssembleeEstRealisePars',
      $sequenceAssemblee
    );

    $deleteForm = $this->createDeleteForm($sequenceAssemblee);
    $editForm = $this->createForm(
      'App\Form\SequenceAssembleeType',
      $sequenceAssemblee,
      [
        'geneVocFk' => $this->geneVocFk,
        'individuFk' => $this->individuFk,
        'action_type' => Action::edit()
      ]
    );
    $editForm->handleRequest($request);

    if ($editForm->isSubmitted() && $editForm->isValid()) {
      // delete ArrayCollection
      $service->DelArrayCollection(
        'EstAligneEtTraites',
        $sequenceAssemblee,
        $estAligneEtTraites
      );
      $service->DelArrayCollectionEmbed(
        'EspeceIdentifiees',
        'EstIdentifiePars',
        $sequenceAssemblee,
        $especeIdentifiees
      );
      $service->DelArrayCollection(
        'SqcEstPublieDanss',
        $sequenceAssemblee,
        $sqcEstPublieDanss
      );
      $service->DelArrayCollection(
        'SequenceAssembleeEstRealisePars',
        $sequenceAssemblee,
        $sequenceAssembleeEstRealisePars
      );
      $em->persist($sequenceAssemblee);
      try {
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
      }
      $editForm = $this->createForm(
        'App\Form\SequenceAssembleeType',
        $sequenceAssemblee,
        ['geneVocFk' => $this->geneVocFk, 'individuFk' => $this->individuFk]
      );
      return $this->render('Core/sequenceassemblee/edit.html.twig', array(
        'sequenceAssemblee' => $sequenceAssemblee,
        'edit_form' => $editForm->createView(),
        'valid' => 1,
        'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
      ));
    }

    return $this->render('Core/sequenceassemblee/edit.html.twig', array(
      'sequenceAssemblee' => $sequenceAssemblee,
      'edit_form' => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      'form_gene_indbiomol' => $form_gene_indbiomol->createView(),
    ));
  }

  /**
   * Deletes a sequenceAssemblee entity.
   *
   * @Route("/{id}", name="sequenceassemblee_delete", methods={"DELETE"})
   * @Security("has_role('ROLE_COLLABORATION')")
   */
  public function deleteAction(Request $request, SequenceAssemblee $sequenceAssemblee)
  {
    $form = $this->createDeleteForm($sequenceAssemblee);
    $form->handleRequest($request);

    $submittedToken = $request->request->get('token');
    if (($form->isSubmitted() && $form->isValid()) ||
      $this->isCsrfTokenValid('delete-item', $submittedToken)
    ) {
      $em = $this->getDoctrine()->getManager();
      try {
        $em->remove($sequenceAssemblee);
        $em->flush();
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
        return $this->render(
          'Core/sequenceassemblee/index.html.twig',
          array('exception_message' =>  explode("\n", $exception_message)[0])
        );
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
      ->setAction($this->generateUrl(
        'sequenceassemblee_delete',
        array('id' => $sequenceAssemblee->getId())
      ))
      ->setMethod('DELETE')
      ->getForm();
  }

  /**
   * Creates a form  : createGeneIndbiomolForm(SequenceAssemblee $sequenceAssemblee = null, $geneVocFk = null, $individuFk = null)
   *
   * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
   *
   * @return \Symfony\Component\Form\Form The form
   */
  private function createGeneIndbiomolForm(
    SequenceAssemblee $sequenceAssemblee = null,
    $geneVocFk = null,
    $individuFk = null
  ) {
    if ($sequenceAssemblee->getId() == null && $geneVocFk == null) {
      return $this->createFormBuilder()
        ->setMethod('POST')
        ->add('geneVocFk', GeneType::class)
        ->add('individuFk', EntityType::class, array(
          'class' => 'App:Individu',
          'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('ind')
              ->where('ind.codeIndBiomol IS NOT NULL')
              ->orderBy('ind.codeIndBiomol', 'ASC');
          },
          'placeholder' => 'Choose an individu',
          'choice_label' => 'code_ind_biomol',
          'multiple' => false,
          'expanded' => false
        ))
        ->add('button.Valid', SubmitType::class, array(
          'label' => 'button.Valid',
          'attr' => array('class' => 'btn btn-round btn-success')
        ))
        ->getForm();
    }
    if ($geneVocFk != null && $individuFk != null) {
      $options = ['geneVocFk' => $geneVocFk, 'individuFk' => $individuFk];

      return $this->createFormBuilder()
        ->setMethod('POST')
        ->add('geneVocFk', GeneType::class, array(
          'query_builder' => function (EntityRepository $er) use ($options) {
            return $er->createQueryBuilder('voc')
              ->where('voc.id = :geneVocFk')
              ->setParameter('geneVocFk', $options['geneVocFk'])
              ->orderBy('voc.libelle', 'ASC');
          },
          'placeholder' => false
        ))
        ->add('individuFk', EntityType::class, array(
          'class' => 'App:Individu',
          'query_builder' => function (EntityRepository $er) use ($options) {
            return $er->createQueryBuilder('ind')
              ->where('ind.id = :individuFk')
              ->setParameter('individuFk', $options['individuFk'])
              ->orderBy('ind.codeIndBiomol', 'ASC');
          },
          'placeholder' => false,
          'choice_label' => 'code_ind_biomol',
          'multiple' => false,
          'expanded' => false
        ))
        ->add('button.Valid', SubmitType::class, array(
          'label' => 'button.Valid',
          'attr' => array('class' => 'btn btn-round btn-success')
        ))
        ->getForm();
    }
  }

  /**
   * Creates a createCodeSqcAlignement
   *
   * @param SequenceAssemblee $sequenceAssemblee The sequenceAssemblee entity
   *
   */
  private function createCodeSqcAlignement(
    SequenceAssemblee $sequenceAssemblee = null,
    $geneVocFk = null,
    $individuFk = null
  ) {
    $codeSqcAlignement = '';
    $em = $this->getDoctrine()->getManager();
    $EspeceIdentifiees =  $sequenceAssemblee->getEspeceIdentifiees();
    $nbEspeceIdentifiees = count($EspeceIdentifiees);
    $eaetId = $sequenceAssemblee->getEstAligneEtTraites()[0]->getId();
    $nbChromato = count($sequenceAssemblee->getEstAligneEtTraites());

    if ($nbChromato > 0 && $nbEspeceIdentifiees > 0) {
      // The status of the sequence DNA the referential Taxon = to the last taxname attributed
      $codeStatutSqcAss = $sequenceAssemblee->getStatutSqcAssVocFk()->getCode();
      $lastCodeTaxon = $EspeceIdentifiees[$nbEspeceIdentifiees - 1]
        ->getReferentielTaxonFk()->getCodeTaxon();
      $codeSqcAlignement = (substr($codeStatutSqcAss, 0, 5) == 'VALID')
        ? $lastCodeTaxon : $codeStatutSqcAss . '_' . $lastCodeTaxon;
      $Chromatogramme1 = $sequenceAssemblee
        ->getEstAligneEtTraites()[0]->getChromatogrammeFk();
      $numIndBiomol = $Chromatogramme1->getPcrFk()->getAdnFk()->getIndividuFk()
        ->getNumIndBiomol();
      $codeCollecte = $Chromatogramme1->getPcrFk()->getAdnFk()->getIndividuFk()
        ->getLotMaterielFk()->getCollecteFk()->getCodeCollecte();
      $codeSqcAlignement = $codeSqcAlignement . '_' . $codeCollecte . '_' . $numIndBiomol;
      //  the concaténation [chromatogramme.code_chromato|pcr.specificite_voc_fk(voc.code)]
      $arrayCodeChromato = array();
      foreach ($sequenceAssemblee->getEstAligneEtTraites() as $entityEstAligneEtTraites) {
        $codeChromato = $entityEstAligneEtTraites->getChromatogrammeFk()->getCodeChromato();
        $specificite = $entityEstAligneEtTraites->getChromatogrammeFk()
          ->getPcrFk()->getSpecificiteVocFk()->getCode();
        $arrayCodeChromato[] = $codeChromato . '|' . $specificite;
      }
      sort($arrayCodeChromato);
      $listeCodeChromato = implode("-", $arrayCodeChromato);
      $codeSqcAlignement = $codeSqcAlignement . '_' . $listeCodeChromato;
    } else {
      $codeSqcAlignement = null;
    }
    return $codeSqcAlignement;
  }
}
