<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Source;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Source controller.
 *
 * @Route("source")
 */
class SourceController extends Controller
{
    /**
     * Lists all source entities.
     *
     * @Route("/", name="source_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sources = $em->getRepository('BbeesE3sBundle:Source')->findAll();

        return $this->render('source/index.html.twig', array(
            'sources' => $sources,
        ));
    }

    /**
     * Creates a new source entity.
     *
     * @Route("/new", name="source_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $source = new Source();
        $form = $this->createForm('Bbees\E3sBundle\Form\SourceType', $source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($source);
            $em->flush();

            return $this->redirectToRoute('source_show', array('id' => $source->getId()));
        }

        return $this->render('source/new.html.twig', array(
            'source' => $source,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a source entity.
     *
     * @Route("/{id}", name="source_show")
     * @Method("GET")
     */
    public function showAction(Source $source)
    {
        $deleteForm = $this->createDeleteForm($source);

        return $this->render('source/show.html.twig', array(
            'source' => $source,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing source entity.
     *
     * @Route("/{id}/edit", name="source_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Source $source)
    {
        $deleteForm = $this->createDeleteForm($source);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SourceType', $source);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('source_edit', array('id' => $source->getId()));
        }

        return $this->render('source/edit.html.twig', array(
            'source' => $source,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a source entity.
     *
     * @Route("/{id}", name="source_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Source $source)
    {
        $form = $this->createDeleteForm($source);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($source);
            $em->flush();
        }

        return $this->redirectToRoute('source_index');
    }

    /**
     * Creates a form to delete a source entity.
     *
     * @param Source $source The source entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Source $source)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('source_delete', array('id' => $source->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
