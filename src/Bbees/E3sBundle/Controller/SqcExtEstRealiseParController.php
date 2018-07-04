<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\SqcExtEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Sqcextestrealisepar controller.
 *
 * @Route("sqcextestrealisepar")
 */
class SqcExtEstRealiseParController extends Controller
{
    /**
     * Lists all sqcExtEstRealisePar entities.
     *
     * @Route("/", name="sqcextestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sqcExtEstRealisePars = $em->getRepository('BbeesE3sBundle:SqcExtEstRealisePar')->findAll();

        return $this->render('sqcextestrealisepar/index.html.twig', array(
            'sqcExtEstRealisePars' => $sqcExtEstRealisePars,
        ));
    }

    /**
     * Creates a new sqcExtEstRealisePar entity.
     *
     * @Route("/new", name="sqcextestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sqcExtEstRealisePar = new Sqcextestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\SqcExtEstRealiseParType', $sqcExtEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sqcExtEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('sqcextestrealisepar_show', array('id' => $sqcExtEstRealisePar->getId()));
        }

        return $this->render('sqcextestrealisepar/new.html.twig', array(
            'sqcExtEstRealisePar' => $sqcExtEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sqcExtEstRealisePar entity.
     *
     * @Route("/{id}", name="sqcextestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($sqcExtEstRealisePar);

        return $this->render('sqcextestrealisepar/show.html.twig', array(
            'sqcExtEstRealisePar' => $sqcExtEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sqcExtEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="sqcextestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($sqcExtEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SqcExtEstRealiseParType', $sqcExtEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sqcextestrealisepar_edit', array('id' => $sqcExtEstRealisePar->getId()));
        }

        return $this->render('sqcextestrealisepar/edit.html.twig', array(
            'sqcExtEstRealisePar' => $sqcExtEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sqcExtEstRealisePar entity.
     *
     * @Route("/{id}", name="sqcextestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        $form = $this->createDeleteForm($sqcExtEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sqcExtEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('sqcextestrealisepar_index');
    }

    /**
     * Creates a form to delete a sqcExtEstRealisePar entity.
     *
     * @param SqcExtEstRealisePar $sqcExtEstRealisePar The sqcExtEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SqcExtEstRealisePar $sqcExtEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sqcextestrealisepar_delete', array('id' => $sqcExtEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
