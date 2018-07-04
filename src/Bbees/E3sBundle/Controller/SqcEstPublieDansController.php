<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\SqcEstPublieDans;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Sqcestpubliedan controller.
 *
 * @Route("sqcestpubliedans")
 */
class SqcEstPublieDansController extends Controller
{
    /**
     * Lists all sqcEstPublieDan entities.
     *
     * @Route("/", name="sqcestpubliedans_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sqcEstPublieDans = $em->getRepository('BbeesE3sBundle:SqcEstPublieDans')->findAll();

        return $this->render('sqcestpubliedans/index.html.twig', array(
            'sqcEstPublieDans' => $sqcEstPublieDans,
        ));
    }

    /**
     * Creates a new sqcEstPublieDan entity.
     *
     * @Route("/new", name="sqcestpubliedans_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sqcEstPublieDan = new Sqcestpubliedan();
        $form = $this->createForm('Bbees\E3sBundle\Form\SqcEstPublieDansType', $sqcEstPublieDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sqcEstPublieDan);
            $em->flush();

            return $this->redirectToRoute('sqcestpubliedans_show', array('id' => $sqcEstPublieDan->getId()));
        }

        return $this->render('sqcestpubliedans/new.html.twig', array(
            'sqcEstPublieDan' => $sqcEstPublieDan,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sqcEstPublieDan entity.
     *
     * @Route("/{id}", name="sqcestpubliedans_show")
     * @Method("GET")
     */
    public function showAction(SqcEstPublieDans $sqcEstPublieDan)
    {
        $deleteForm = $this->createDeleteForm($sqcEstPublieDan);

        return $this->render('sqcestpubliedans/show.html.twig', array(
            'sqcEstPublieDan' => $sqcEstPublieDan,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sqcEstPublieDan entity.
     *
     * @Route("/{id}/edit", name="sqcestpubliedans_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SqcEstPublieDans $sqcEstPublieDan)
    {
        $deleteForm = $this->createDeleteForm($sqcEstPublieDan);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SqcEstPublieDansType', $sqcEstPublieDan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sqcestpubliedans_edit', array('id' => $sqcEstPublieDan->getId()));
        }

        return $this->render('sqcestpubliedans/edit.html.twig', array(
            'sqcEstPublieDan' => $sqcEstPublieDan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sqcEstPublieDan entity.
     *
     * @Route("/{id}", name="sqcestpubliedans_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SqcEstPublieDans $sqcEstPublieDan)
    {
        $form = $this->createDeleteForm($sqcEstPublieDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sqcEstPublieDan);
            $em->flush();
        }

        return $this->redirectToRoute('sqcestpubliedans_index');
    }

    /**
     * Creates a form to delete a sqcEstPublieDan entity.
     *
     * @param SqcEstPublieDans $sqcEstPublieDan The sqcEstPublieDan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SqcEstPublieDans $sqcEstPublieDan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sqcestpubliedans_delete', array('id' => $sqcEstPublieDan->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
