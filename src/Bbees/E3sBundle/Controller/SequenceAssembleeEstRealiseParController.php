<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Sequenceassembleeestrealisepar controller.
 *
 * @Route("sequenceassembleeestrealisepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SequenceAssembleeEstRealiseParController extends Controller
{
    /**
     * Lists all sequenceAssembleeEstRealisePar entities.
     *
     * @Route("/", name="sequenceassembleeestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sequenceAssembleeEstRealisePars = $em->getRepository('BbeesE3sBundle:SequenceAssembleeEstRealisePar')->findAll();

        return $this->render('sequenceassembleeestrealisepar/index.html.twig', array(
            'sequenceAssembleeEstRealisePars' => $sequenceAssembleeEstRealisePars,
        ));
    }

    /**
     * Creates a new sequenceAssembleeEstRealisePar entity.
     *
     * @Route("/new", name="sequenceassembleeestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $sequenceAssembleeEstRealisePar = new Sequenceassembleeestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeEstRealiseParType', $sequenceAssembleeEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sequenceAssembleeEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('sequenceassembleeestrealisepar_show', array('id' => $sequenceAssembleeEstRealisePar->getId()));
        }

        return $this->render('sequenceassembleeestrealisepar/new.html.twig', array(
            'sequenceAssembleeEstRealisePar' => $sequenceAssembleeEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sequenceAssembleeEstRealisePar entity.
     *
     * @Route("/{id}", name="sequenceassembleeestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($sequenceAssembleeEstRealisePar);

        return $this->render('sequenceassembleeestrealisepar/show.html.twig', array(
            'sequenceAssembleeEstRealisePar' => $sequenceAssembleeEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sequenceAssembleeEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="sequenceassembleeestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($sequenceAssembleeEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\SequenceAssembleeEstRealiseParType', $sequenceAssembleeEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sequenceassembleeestrealisepar_edit', array('id' => $sequenceAssembleeEstRealisePar->getId()));
        }

        return $this->render('sequenceassembleeestrealisepar/edit.html.twig', array(
            'sequenceAssembleeEstRealisePar' => $sequenceAssembleeEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sequenceAssembleeEstRealisePar entity.
     *
     * @Route("/{id}", name="sequenceassembleeestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        $form = $this->createDeleteForm($sequenceAssembleeEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sequenceAssembleeEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('sequenceassembleeestrealisepar_index');
    }

    /**
     * Creates a form to delete a sequenceAssembleeEstRealisePar entity.
     *
     * @param SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar The sequenceAssembleeEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SequenceAssembleeEstRealisePar $sequenceAssembleeEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sequenceassembleeestrealisepar_delete', array('id' => $sequenceAssembleeEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
