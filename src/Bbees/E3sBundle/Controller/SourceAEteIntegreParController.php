<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\SourceAEteIntegrePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Sourceaeteintegrepar controller.
 *
 * @Route("sourceaeteintegrepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SourceAEteIntegreParController extends Controller
{
    /**
     * Lists all sourceAEteIntegrePar entities.
     *
     * @Route("/", name="sourceaeteintegrepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sourceAEteIntegrePars = $em->getRepository('BbeesE3sBundle:SourceAEteIntegrePar')->findAll();

        return $this->render('sourceaeteintegrepar/index.html.twig', array(
            'sourceAEteIntegrePars' => $sourceAEteIntegrePars,
        ));
    }

    /**
     * Creates a new sourceAEteIntegrePar entity.
     *
     * @Route("/new", name="sourceaeteintegrepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sourceAEteIntegrePar = new Sourceaeteintegrepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\SourceAEteIntegreParType', $sourceAEteIntegrePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sourceAEteIntegrePar);
            $em->flush();

            return $this->redirectToRoute('sourceaeteintegrepar_show', array('id' => $sourceAEteIntegrePar->getId()));
        }

        return $this->render('sourceaeteintegrepar/new.html.twig', array(
            'sourceAEteIntegrePar' => $sourceAEteIntegrePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sourceAEteIntegrePar entity.
     *
     * @Route("/{id}", name="sourceaeteintegrepar_show")
     * @Method("GET")
     */
    public function showAction(SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        $deleteForm = $this->createDeleteForm($sourceAEteIntegrePar);

        return $this->render('sourceaeteintegrepar/show.html.twig', array(
            'sourceAEteIntegrePar' => $sourceAEteIntegrePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sourceAEteIntegrePar entity.
     *
     * @Route("/{id}/edit", name="sourceaeteintegrepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        $deleteForm = $this->createDeleteForm($sourceAEteIntegrePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SourceAEteIntegreParType', $sourceAEteIntegrePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sourceaeteintegrepar_edit', array('id' => $sourceAEteIntegrePar->getId()));
        }

        return $this->render('sourceaeteintegrepar/edit.html.twig', array(
            'sourceAEteIntegrePar' => $sourceAEteIntegrePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sourceAEteIntegrePar entity.
     *
     * @Route("/{id}", name="sourceaeteintegrepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        $form = $this->createDeleteForm($sourceAEteIntegrePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sourceAEteIntegrePar);
            $em->flush();
        }

        return $this->redirectToRoute('sourceaeteintegrepar_index');
    }

    /**
     * Creates a form to delete a sourceAEteIntegrePar entity.
     *
     * @param SourceAEteIntegrePar $sourceAEteIntegrePar The sourceAEteIntegrePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SourceAEteIntegrePar $sourceAEteIntegrePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sourceaeteintegrepar_delete', array('id' => $sourceAEteIntegrePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
