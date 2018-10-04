<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\APourSamplingMethod;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Apoursamplingmethod controller.
 *
 * @Route("apoursamplingmethod")
 * @Security("has_role('ROLE_ADMIN')")
 */
class APourSamplingMethodController extends Controller
{
    /**
     * Lists all aPourSamplingMethod entities.
     *
     * @Route("/", name="apoursamplingmethod_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aPourSamplingMethods = $em->getRepository('BbeesE3sBundle:APourSamplingMethod')->findAll();

        return $this->render('apoursamplingmethod/index.html.twig', array(
            'aPourSamplingMethods' => $aPourSamplingMethods,
        ));
    }

    /**
     * Creates a new aPourSamplingMethod entity.
     *
     * @Route("/new", name="apoursamplingmethod_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $aPourSamplingMethod = new Apoursamplingmethod();
        $form = $this->createForm('Bbees\E3sBundle\Form\APourSamplingMethodType', $aPourSamplingMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($aPourSamplingMethod);
            $em->flush();

            return $this->redirectToRoute('apoursamplingmethod_show', array('id' => $aPourSamplingMethod->getId()));
        }

        return $this->render('apoursamplingmethod/new.html.twig', array(
            'aPourSamplingMethod' => $aPourSamplingMethod,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a aPourSamplingMethod entity.
     *
     * @Route("/{id}", name="apoursamplingmethod_show")
     * @Method("GET")
     */
    public function showAction(APourSamplingMethod $aPourSamplingMethod)
    {
        $deleteForm = $this->createDeleteForm($aPourSamplingMethod);

        return $this->render('apoursamplingmethod/show.html.twig', array(
            'aPourSamplingMethod' => $aPourSamplingMethod,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing aPourSamplingMethod entity.
     *
     * @Route("/{id}/edit", name="apoursamplingmethod_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, APourSamplingMethod $aPourSamplingMethod)
    {
        $deleteForm = $this->createDeleteForm($aPourSamplingMethod);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\APourSamplingMethodType', $aPourSamplingMethod);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('apoursamplingmethod_edit', array('id' => $aPourSamplingMethod->getId()));
        }

        return $this->render('apoursamplingmethod/edit.html.twig', array(
            'aPourSamplingMethod' => $aPourSamplingMethod,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a aPourSamplingMethod entity.
     *
     * @Route("/{id}", name="apoursamplingmethod_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, APourSamplingMethod $aPourSamplingMethod)
    {
        $form = $this->createDeleteForm($aPourSamplingMethod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($aPourSamplingMethod);
            $em->flush();
        }

        return $this->redirectToRoute('apoursamplingmethod_index');
    }

    /**
     * Creates a form to delete a aPourSamplingMethod entity.
     *
     * @param APourSamplingMethod $aPourSamplingMethod The aPourSamplingMethod entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(APourSamplingMethod $aPourSamplingMethod)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apoursamplingmethod_delete', array('id' => $aPourSamplingMethod->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
