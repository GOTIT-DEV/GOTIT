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

use App\Entity\Pcr;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\Core\GenericFunctionE3s;

/**
 * Pcr controller.
 *
 * @Route("pcr")
 * @Security("has_role('ROLE_INVITED')")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 * 
 */
class PcrController extends Controller
{
    const MAX_RESULTS_TYPEAHEAD   = 20;
    
    /**
     * Lists all pcr entities.
     *
     * @Route("/", name="pcr_index", methods={"GET"})
     * @Route("/", name="pcrchromato_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pcrs = $em->getRepository('App:Pcr')->findAll();

        return $this->render('pcr/index.html.twig', array(
            'pcrs' => $pcrs,
        ));
    }

    
    /**
     * @Route("/search/{q}", requirements={"q"=".+"}, name="pcr_search")
     */
    public function searchAction($q)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('pcr.id, pcr.codePcr as code')
            ->from('App:Pcr', 'pcr');
        $query = explode(' ', strtolower(trim(urldecode($q))));
        $and = [];
        for($i=0; $i<count($query); $i++) {
            $and[] = '(LOWER(pcr.codePcr) like :q'.$i.')';
        }
        $qb->where(implode(' and ', $and));
        for($i=0; $i<count($query); $i++) {
            $qb->setParameter('q'.$i, $query[$i].'%');
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
     * @Route("/indexjson", name="pcr_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request, GenericFunctionE3s $service)
    {
        // load Doctrine Manager
        $em = $this->getDoctrine()->getManager();
        //
        $rowCount = ($request->get('rowCount')  !== NULL) ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL) ? $request->get('sort') : array('pcr.dateMaj' => 'desc', 'pcr.id' => 'desc');
        $minRecord = intval($request->get('current') - 1) * $rowCount;
        $maxRecord = $rowCount;
        // initializes the searchPhrase variable as appropriate and sets the condition according to the url idFk parameter
        $where = 'LOWER(individu.codeIndBiomol) LIKE :criteriaLower';
        $searchPhrase = $request->get('searchPhrase');
        if ($request->get('searchPatern') !== null && $request->get('searchPatern') !== '' && $searchPhrase == '') {
            $searchPhrase = $request->get('searchPatern');
        }
        if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
            $where .= ' AND pcr.adnFk = ' . $request->get('idFk');
        }
        // Search for the list to show
        $tab_toshow = [];
        $entities_toshow = $em->getRepository("App:Pcr")->createQueryBuilder('pcr')
            ->where($where)
            ->setParameter('criteriaLower', strtolower($searchPhrase) . '%')
            ->leftJoin('App:Adn', 'adn', 'WITH', 'pcr.adnFk = adn.id')
            ->leftJoin('App:Individu', 'individu', 'WITH', 'adn.individuFk = individu.id')
            ->leftJoin('App:Voc', 'vocGene', 'WITH', 'pcr.geneVocFk = vocGene.id')
            ->leftJoin('App:Voc', 'vocQualitePcr', 'WITH', 'pcr.qualitePcrVocFk = vocQualitePcr.id')
            ->leftJoin('App:Voc', 'vocSpecificite', 'WITH', 'pcr.specificiteVocFk = vocSpecificite.id')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb = count($entities_toshow);
        $entities_toshow = ($request->get('rowCount') > 0 ) ? array_slice($entities_toshow, $minRecord, $rowCount) : array_slice($entities_toshow, $minRecord);
        $lastTaxname = '';
        foreach ($entities_toshow as $entity) {
            $id = $entity->getId();
            $DatePcr = ($entity->getDatePcr() !== null) ?  $entity->getDatePcr()->format('Y-m-d') : null;
            $DateMaj = ($entity->getDateMaj() !== null) ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $DateCre = ($entity->getDateCre() !== null) ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            // Search chromatograms associated to a PCR
            $query = $em->createQuery('SELECT chromato.id FROM App:Chromatogramme chromato WHERE chromato.pcrFk = ' . $id . '')->getResult();
            $linkChromatogramme = (count($query) > 0) ? $id : '';
            // concatenated list of people 
            $query = $em->createQuery('SELECT p.nomPersonne as nom FROM App:PcrEstRealisePar erp JOIN erp.personneFk p WHERE erp.pcrFk = ' . $id . '')->getResult();
            $arrayListePersonne = array();
            foreach ($query as $taxon) {
                $arrayListePersonne[] = $taxon['nom'];
            }
            $listePersonne = implode(", ", $arrayListePersonne);
            //
            $tab_toshow[] = array(
                "id" => $id, "pcr.id" => $id,
                "individu.codeIndBiomol" => $entity->getAdnFk()->getIndividuFk()->getCodeIndBiomol(),
                "adn.codeAdn" => $entity->getAdnFk()->getCodeAdn(),
                "pcr.codePcr" => $entity->getCodePcr(),
                "pcr.numPcr" => $entity->getNumPcr(),
                "vocGene.code" => $entity->getGeneVocFk()->getCode(),
                "listePersonne" => $listePersonne,
                "pcr.datePcr" => $DatePcr,
                "vocQualitePcr.code" => $entity->getQualitePcrVocFk()->getCode(),
                "vocSpecificite.code" => $entity->getSpecificiteVocFk()->getCode(),
                "pcr.dateCre" => $DateCre, "pcr.dateMaj" => $DateMaj,
                "userCreId" => $service->GetUserCreId($entity), "pcr.userCre" => $service->GetUserCreUsername($entity), "pcr.userMaj" => $service->GetUserMajUsername($entity),
                "linkChromatogramme" => $linkChromatogramme,
            );
        }
        // Ajax answer
        $response = new JsonResponse([
            "current"    => intval($request->get('current')),
            "rowCount"  => $rowCount,
            "rows"     => $tab_toshow,
            "searchPhrase" => $searchPhrase,
            "total"    => $nb // total data array
        ]);

        return $response;
    }


    /**
     * Creates a new pcr entity.
     *
     * @Route("/new", name="pcr_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function newAction(Request $request)
    {
        $pcr = new Pcr();
        $em = $this->getDoctrine()->getManager();
        // check if the relational Entity (Adn) is given and set the RelationalEntityFk for the new Entity
        if ($request->get('idFk') !== null && $request->get('idFk') !== '') {
            $RelEntityId = $request->get('idFk');
            $RelEntity = $em->getRepository('App:Adn')->find($RelEntityId);
            $pcr->setAdnFk($RelEntity);
        }
        $form = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $form->get('adnId')->getData() !== null) {
            // (i) load the id of relational Entity (Adn) from typeahead input field and (ii) set the foreign key
            $RelEntityId = $form->get('adnId');
            $RelEntity = $em->getRepository('App:Adn')->find($RelEntityId->getData());
            $pcr->setAdnFk($RelEntity);
            // persist Entity
            $em->persist($pcr);
            try {
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            return $this->redirectToRoute('pcr_edit', array('id' => $pcr->getId(), 'valid' => 1, 'idFk' => $request->get('idFk') ));
        }

        return $this->render('pcr/edit.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pcr entity.
     *
     * @Route("/{id}", name="pcr_show", methods={"GET"})
     */
    public function showAction(Pcr $pcr)
    {
        $deleteForm = $this->createDeleteForm($pcr);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);

        return $this->render('pcr/edit.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pcr entity.
     *
     * @Route("/{id}/edit", name="pcr_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     * 
     */
    public function editAction(Request $request, Pcr $pcr, GenericFunctionE3s $service)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if ($user->getRole() ==  'ROLE_COLLABORATION' && $pcr->getUserCre() != $user->getId()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        // load service  generic_function_e3s
        // 
        // store ArrayCollection       
        $pcrEstRealisePars = $service->setArrayCollection('PcrEstRealisePars', $pcr);
        //
        $deleteForm = $this->createDeleteForm($pcr);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PcrType', $pcr);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // delete ArrayCollection
            $service->DelArrayCollection('PcrEstRealisePars', $pcr, $pcrEstRealisePars);
            // (i) load the id of relational Entity (Adn) from typeahead input field  (ii) set the foreign key
            $em = $this->getDoctrine()->getManager();
            $RelEntityId = $editForm->get('adnId');
            $RelEntity = $em->getRepository('App:Adn')->find($RelEntityId->getData());
            $pcr->setAdnFk($RelEntity);
            // flush
            $this->getDoctrine()->getManager()->persist($pcr);
            try {
                $this->getDoctrine()->getManager()->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            //return $this->redirectToRoute('lotmateriel_edit', array('id' => $lotMateriel->getId()));
            // return $this->redirectToRoute('lotmateriel_index');
            return $this->render('pcr/edit.html.twig', array(
                'pcr' => $pcr,
                'edit_form' => $editForm->createView(),
                'valid' => 1
            ));
        }

        return $this->render('pcr/edit.html.twig', array(
            'pcr' => $pcr,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pcr entity.
     *
     * @Route("/{id}", name="pcr_delete", methods={"DELETE"})
     * @Security("has_role('ROLE_COLLABORATION')")
     * 
     */
    public function deleteAction(Request $request, Pcr $pcr)
    {
        $form = $this->createDeleteForm($pcr);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($pcr);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                return $this->render('pcr/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
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
    private function createDeleteForm(Pcr $pcr)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pcr_delete', array('id' => $pcr->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
