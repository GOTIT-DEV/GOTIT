<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\LotMaterielExtEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lotmaterielextestrealisepar controller.
 *
 * @Route("lotmaterielextestrealisepar")
 */
class LotMaterielExtEstRealiseParController extends Controller
{
    /**
     * Lists all lotMaterielExtEstRealisePar entities.
     *
     * @Route("/", name="lotmaterielextestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotMaterielExtEstRealisePars = $em->getRepository('BbeesE3sBundle:LotMaterielExtEstRealisePar')->findAll();

        return $this->render('lotmaterielextestrealisepar/index.html.twig', array(
            'lotMaterielExtEstRealisePars' => $lotMaterielExtEstRealisePars,
        ));
    }

    /**
     * Creates a new lotMaterielExtEstRealisePar entity.
     *
     * @Route("/new", name="lotmaterielextestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lotMaterielExtEstRealisePar = new Lotmaterielextestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtEstRealiseParType', $lotMaterielExtEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotMaterielExtEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('lotmaterielextestrealisepar_show', array('id' => $lotMaterielExtEstRealisePar->getId()));
        }

        return $this->render('lotmaterielextestrealisepar/new.html.twig', array(
            'lotMaterielExtEstRealisePar' => $lotMaterielExtEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotMaterielExtEstRealisePar entity.
     *
     * @Route("/{id}", name="lotmaterielextestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(LotMaterielExtEstRealisePar $lotMaterielExtEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielExtEstRealisePar);

        return $this->render('lotmaterielextestrealisepar/show.html.twig', array(
            'lotMaterielExtEstRealisePar' => $lotMaterielExtEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lotMaterielExtEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="lotmaterielextestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LotMaterielExtEstRealisePar $lotMaterielExtEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielExtEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtEstRealiseParType', $lotMaterielExtEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lotmaterielextestrealisepar_edit', array('id' => $lotMaterielExtEstRealisePar->getId()));
        }

        return $this->render('lotmaterielextestrealisepar/edit.html.twig', array(
            'lotMaterielExtEstRealisePar' => $lotMaterielExtEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotMaterielExtEstRealisePar entity.
     *
     * @Route("/{id}", name="lotmaterielextestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LotMaterielExtEstRealisePar $lotMaterielExtEstRealisePar)
    {
        $form = $this->createDeleteForm($lotMaterielExtEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lotMaterielExtEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('lotmaterielextestrealisepar_index');
    }

    /**
     * Creates a form to delete a lotMaterielExtEstRealisePar entity.
     *
     * @param LotMaterielExtEstRealisePar $lotMaterielExtEstRealisePar The lotMaterielExtEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotMaterielExtEstRealisePar $lotMaterielExtEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotmaterielextestrealisepar_delete', array('id' => $lotMaterielExtEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
