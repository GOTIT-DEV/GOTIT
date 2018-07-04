<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\SqcExtEstReferenceDans;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Sqcextestreferencedan controller.
 *
 * @Route("sqcextestreferencedans")
 */
class SqcExtEstReferenceDansController extends Controller
{
    /**
     * Lists all sqcExtEstReferenceDan entities.
     *
     * @Route("/", name="sqcextestreferencedans_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sqcExtEstReferenceDans = $em->getRepository('BbeesE3sBundle:SqcExtEstReferenceDans')->findAll();

        return $this->render('sqcextestreferencedans/index.html.twig', array(
            'sqcExtEstReferenceDans' => $sqcExtEstReferenceDans,
        ));
    }

    /**
     * Creates a new sqcExtEstReferenceDan entity.
     *
     * @Route("/new", name="sqcextestreferencedans_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sqcExtEstReferenceDan = new Sqcextestreferencedan();
        $form = $this->createForm('Bbees\E3sBundle\Form\SqcExtEstReferenceDansType', $sqcExtEstReferenceDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sqcExtEstReferenceDan);
            $em->flush();

            return $this->redirectToRoute('sqcextestreferencedans_show', array('id' => $sqcExtEstReferenceDan->getId()));
        }

        return $this->render('sqcextestreferencedans/new.html.twig', array(
            'sqcExtEstReferenceDan' => $sqcExtEstReferenceDan,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sqcExtEstReferenceDan entity.
     *
     * @Route("/{id}", name="sqcextestreferencedans_show")
     * @Method("GET")
     */
    public function showAction(SqcExtEstReferenceDans $sqcExtEstReferenceDan)
    {
        $deleteForm = $this->createDeleteForm($sqcExtEstReferenceDan);

        return $this->render('sqcextestreferencedans/show.html.twig', array(
            'sqcExtEstReferenceDan' => $sqcExtEstReferenceDan,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sqcExtEstReferenceDan entity.
     *
     * @Route("/{id}/edit", name="sqcextestreferencedans_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SqcExtEstReferenceDans $sqcExtEstReferenceDan)
    {
        $deleteForm = $this->createDeleteForm($sqcExtEstReferenceDan);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SqcExtEstReferenceDansType', $sqcExtEstReferenceDan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sqcextestreferencedans_edit', array('id' => $sqcExtEstReferenceDan->getId()));
        }

        return $this->render('sqcextestreferencedans/edit.html.twig', array(
            'sqcExtEstReferenceDan' => $sqcExtEstReferenceDan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sqcExtEstReferenceDan entity.
     *
     * @Route("/{id}", name="sqcextestreferencedans_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SqcExtEstReferenceDans $sqcExtEstReferenceDan)
    {
        $form = $this->createDeleteForm($sqcExtEstReferenceDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sqcExtEstReferenceDan);
            $em->flush();
        }

        return $this->redirectToRoute('sqcextestreferencedans_index');
    }

    /**
     * Creates a form to delete a sqcExtEstReferenceDan entity.
     *
     * @param SqcExtEstReferenceDans $sqcExtEstReferenceDan The sqcExtEstReferenceDan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SqcExtEstReferenceDans $sqcExtEstReferenceDan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sqcextestreferencedans_delete', array('id' => $sqcExtEstReferenceDan->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
