<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\IndividuLameEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Individulameestrealisepar controller.
 *
 * @Route("individulameestrealisepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class IndividuLameEstRealiseParController extends Controller
{
    /**
     * Lists all individuLameEstRealisePar entities.
     *
     * @Route("/", name="individulameestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $individuLameEstRealisePars = $em->getRepository('BbeesE3sBundle:IndividuLameEstRealisePar')->findAll();

        return $this->render('individulameestrealisepar/index.html.twig', array(
            'individuLameEstRealisePars' => $individuLameEstRealisePars,
        ));
    }

    /**
     * Creates a new individuLameEstRealisePar entity.
     *
     * @Route("/new", name="individulameestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $individuLameEstRealisePar = new Individulameestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\IndividuLameEstRealiseParType', $individuLameEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($individuLameEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('individulameestrealisepar_show', array('id' => $individuLameEstRealisePar->getId()));
        }

        return $this->render('individulameestrealisepar/new.html.twig', array(
            'individuLameEstRealisePar' => $individuLameEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a individuLameEstRealisePar entity.
     *
     * @Route("/{id}", name="individulameestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($individuLameEstRealisePar);

        return $this->render('individulameestrealisepar/show.html.twig', array(
            'individuLameEstRealisePar' => $individuLameEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing individuLameEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="individulameestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($individuLameEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\IndividuLameEstRealiseParType', $individuLameEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('individulameestrealisepar_edit', array('id' => $individuLameEstRealisePar->getId()));
        }

        return $this->render('individulameestrealisepar/edit.html.twig', array(
            'individuLameEstRealisePar' => $individuLameEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a individuLameEstRealisePar entity.
     *
     * @Route("/{id}", name="individulameestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        $form = $this->createDeleteForm($individuLameEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($individuLameEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('individulameestrealisepar_index');
    }

    /**
     * Creates a form to delete a individuLameEstRealisePar entity.
     *
     * @param IndividuLameEstRealisePar $individuLameEstRealisePar The individuLameEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(IndividuLameEstRealisePar $individuLameEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('individulameestrealisepar_delete', array('id' => $individuLameEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
