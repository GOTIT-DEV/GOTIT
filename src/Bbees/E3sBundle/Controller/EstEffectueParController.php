<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\EstEffectuePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Esteffectuepar controller.
 *
 * @Route("esteffectuepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EstEffectueParController extends Controller
{
    /**
     * Lists all estEffectuePar entities.
     *
     * @Route("/", name="esteffectuepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estEffectuePars = $em->getRepository('BbeesE3sBundle:EstEffectuePar')->findAll();

        return $this->render('esteffectuepar/index.html.twig', array(
            'estEffectuePars' => $estEffectuePars,
        ));
    }

    /**
     * Creates a new estEffectuePar entity.
     *
     * @Route("/new", name="esteffectuepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $estEffectuePar = new Esteffectuepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\EstEffectueParType', $estEffectuePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estEffectuePar);
            $em->flush();

            return $this->redirectToRoute('esteffectuepar_show', array('id' => $estEffectuePar->getId()));
        }

        return $this->render('esteffectuepar/new.html.twig', array(
            'estEffectuePar' => $estEffectuePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a estEffectuePar entity.
     *
     * @Route("/{id}", name="esteffectuepar_show")
     * @Method("GET")
     */
    public function showAction(EstEffectuePar $estEffectuePar)
    {
        $deleteForm = $this->createDeleteForm($estEffectuePar);

        return $this->render('esteffectuepar/show.html.twig', array(
            'estEffectuePar' => $estEffectuePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing estEffectuePar entity.
     *
     * @Route("/{id}/edit", name="esteffectuepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EstEffectuePar $estEffectuePar)
    {
        $deleteForm = $this->createDeleteForm($estEffectuePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\EstEffectueParType', $estEffectuePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('esteffectuepar_edit', array('id' => $estEffectuePar->getId()));
        }

        return $this->render('esteffectuepar/edit.html.twig', array(
            'estEffectuePar' => $estEffectuePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a estEffectuePar entity.
     *
     * @Route("/{id}", name="esteffectuepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstEffectuePar $estEffectuePar)
    {
        $form = $this->createDeleteForm($estEffectuePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estEffectuePar);
            $em->flush();
        }

        return $this->redirectToRoute('esteffectuepar_index');
    }

    /**
     * Creates a form to delete a estEffectuePar entity.
     *
     * @param EstEffectuePar $estEffectuePar The estEffectuePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstEffectuePar $estEffectuePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('esteffectuepar_delete', array('id' => $estEffectuePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
