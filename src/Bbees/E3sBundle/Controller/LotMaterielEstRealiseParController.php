<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\LotMaterielEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Lotmaterielestrealisepar controller.
 *
 * @Route("lotmaterielestrealisepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class LotMaterielEstRealiseParController extends Controller
{
    /**
     * Lists all lotMaterielEstRealisePar entities.
     *
     * @Route("/", name="lotmaterielestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotMaterielEstRealisePars = $em->getRepository('BbeesE3sBundle:LotMaterielEstRealisePar')->findAll();

        return $this->render('lotmaterielestrealisepar/index.html.twig', array(
            'lotMaterielEstRealisePars' => $lotMaterielEstRealisePars,
        ));
    }

    /**
     * Creates a new lotMaterielEstRealisePar entity.
     *
     * @Route("/new", name="lotmaterielestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lotMaterielEstRealisePar = new Lotmaterielestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotMaterielEstRealiseParType', $lotMaterielEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotMaterielEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('lotmaterielestrealisepar_show', array('id' => $lotMaterielEstRealisePar->getId()));
        }

        return $this->render('lotmaterielestrealisepar/new.html.twig', array(
            'lotMaterielEstRealisePar' => $lotMaterielEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotMaterielEstRealisePar entity.
     *
     * @Route("/{id}", name="lotmaterielestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielEstRealisePar);

        return $this->render('lotmaterielestrealisepar/show.html.twig', array(
            'lotMaterielEstRealisePar' => $lotMaterielEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lotMaterielEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="lotmaterielestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielEstRealiseParType', $lotMaterielEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lotmaterielestrealisepar_edit', array('id' => $lotMaterielEstRealisePar->getId()));
        }

        return $this->render('lotmaterielestrealisepar/edit.html.twig', array(
            'lotMaterielEstRealisePar' => $lotMaterielEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotMaterielEstRealisePar entity.
     *
     * @Route("/{id}", name="lotmaterielestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        $form = $this->createDeleteForm($lotMaterielEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lotMaterielEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('lotmaterielestrealisepar_index');
    }

    /**
     * Creates a form to delete a lotMaterielEstRealisePar entity.
     *
     * @param LotMaterielEstRealisePar $lotMaterielEstRealisePar The lotMaterielEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotMaterielEstRealisePar $lotMaterielEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotmaterielestrealisepar_delete', array('id' => $lotMaterielEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
