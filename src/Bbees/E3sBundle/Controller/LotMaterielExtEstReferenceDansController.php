<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\LotMaterielExtEstReferenceDans;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lotmaterielextestreferencedan controller.
 *
 * @Route("lotmaterielextestreferencedans")
 */
class LotMaterielExtEstReferenceDansController extends Controller
{
    /**
     * Lists all lotMaterielExtEstReferenceDan entities.
     *
     * @Route("/", name="lotmaterielextestreferencedans_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotMaterielExtEstReferenceDans = $em->getRepository('BbeesE3sBundle:LotMaterielExtEstReferenceDans')->findAll();

        return $this->render('lotmaterielextestreferencedans/index.html.twig', array(
            'lotMaterielExtEstReferenceDans' => $lotMaterielExtEstReferenceDans,
        ));
    }

    /**
     * Creates a new lotMaterielExtEstReferenceDan entity.
     *
     * @Route("/new", name="lotmaterielextestreferencedans_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lotMaterielExtEstReferenceDan = new Lotmaterielextestreferencedan();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtEstReferenceDansType', $lotMaterielExtEstReferenceDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotMaterielExtEstReferenceDan);
            $em->flush();

            return $this->redirectToRoute('lotmaterielextestreferencedans_show', array('id' => $lotMaterielExtEstReferenceDan->getId()));
        }

        return $this->render('lotmaterielextestreferencedans/new.html.twig', array(
            'lotMaterielExtEstReferenceDan' => $lotMaterielExtEstReferenceDan,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotMaterielExtEstReferenceDan entity.
     *
     * @Route("/{id}", name="lotmaterielextestreferencedans_show")
     * @Method("GET")
     */
    public function showAction(LotMaterielExtEstReferenceDans $lotMaterielExtEstReferenceDan)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielExtEstReferenceDan);

        return $this->render('lotmaterielextestreferencedans/show.html.twig', array(
            'lotMaterielExtEstReferenceDan' => $lotMaterielExtEstReferenceDan,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lotMaterielExtEstReferenceDan entity.
     *
     * @Route("/{id}/edit", name="lotmaterielextestreferencedans_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LotMaterielExtEstReferenceDans $lotMaterielExtEstReferenceDan)
    {
        $deleteForm = $this->createDeleteForm($lotMaterielExtEstReferenceDan);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotMaterielExtEstReferenceDansType', $lotMaterielExtEstReferenceDan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lotmaterielextestreferencedans_edit', array('id' => $lotMaterielExtEstReferenceDan->getId()));
        }

        return $this->render('lotmaterielextestreferencedans/edit.html.twig', array(
            'lotMaterielExtEstReferenceDan' => $lotMaterielExtEstReferenceDan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotMaterielExtEstReferenceDan entity.
     *
     * @Route("/{id}", name="lotmaterielextestreferencedans_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LotMaterielExtEstReferenceDans $lotMaterielExtEstReferenceDan)
    {
        $form = $this->createDeleteForm($lotMaterielExtEstReferenceDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lotMaterielExtEstReferenceDan);
            $em->flush();
        }

        return $this->redirectToRoute('lotmaterielextestreferencedans_index');
    }

    /**
     * Creates a form to delete a lotMaterielExtEstReferenceDan entity.
     *
     * @param LotMaterielExtEstReferenceDans $lotMaterielExtEstReferenceDan The lotMaterielExtEstReferenceDan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotMaterielExtEstReferenceDans $lotMaterielExtEstReferenceDan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotmaterielextestreferencedans_delete', array('id' => $lotMaterielExtEstReferenceDan->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
