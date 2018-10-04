<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\EspeceIdentifiee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Especeidentifiee controller.
 *
 * @Route("especeidentifiee")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EspeceIdentifieeController extends Controller
{
    /**
     * Lists all especeIdentifiee entities.
     *
     * @Route("/", name="especeidentifiee_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $especeIdentifiees = $em->getRepository('BbeesE3sBundle:EspeceIdentifiee')->findAll();

        return $this->render('especeidentifiee/index.html.twig', array(
            'especeIdentifiees' => $especeIdentifiees,
        ));
    }

    /**
     * Creates a new especeIdentifiee entity.
     *
     * @Route("/new", name="especeidentifiee_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $especeIdentifiee = new Especeidentifiee();
        $form = $this->createForm('Bbees\E3sBundle\Form\EspeceIdentifieeType', $especeIdentifiee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($especeIdentifiee);
            $em->flush();

            return $this->redirectToRoute('especeidentifiee_show', array('id' => $especeIdentifiee->getId()));
        }

        return $this->render('especeidentifiee/new.html.twig', array(
            'especeIdentifiee' => $especeIdentifiee,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a especeIdentifiee entity.
     *
     * @Route("/{id}", name="especeidentifiee_show")
     * @Method("GET")
     */
    public function showAction(EspeceIdentifiee $especeIdentifiee)
    {
        $deleteForm = $this->createDeleteForm($especeIdentifiee);

        return $this->render('especeidentifiee/show.html.twig', array(
            'especeIdentifiee' => $especeIdentifiee,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing especeIdentifiee entity.
     *
     * @Route("/{id}/edit", name="especeidentifiee_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EspeceIdentifiee $especeIdentifiee)
    {
        $deleteForm = $this->createDeleteForm($especeIdentifiee);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\EspeceIdentifieeType', $especeIdentifiee);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('especeidentifiee_edit', array('id' => $especeIdentifiee->getId()));
        }

        return $this->render('especeidentifiee/edit.html.twig', array(
            'especeIdentifiee' => $especeIdentifiee,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a especeIdentifiee entity.
     *
     * @Route("/{id}", name="especeidentifiee_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EspeceIdentifiee $especeIdentifiee)
    {
        $form = $this->createDeleteForm($especeIdentifiee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($especeIdentifiee);
            $em->flush();
        }

        return $this->redirectToRoute('especeidentifiee_index');
    }

    /**
     * Creates a form to delete a especeIdentifiee entity.
     *
     * @param EspeceIdentifiee $especeIdentifiee The especeIdentifiee entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EspeceIdentifiee $especeIdentifiee)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('especeidentifiee_delete', array('id' => $especeIdentifiee->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
