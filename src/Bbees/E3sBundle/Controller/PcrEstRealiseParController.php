<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\PcrEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Pcrestrealisepar controller.
 *
 * @Route("pcrestrealisepar")
 */
class PcrEstRealiseParController extends Controller
{
    /**
     * Lists all pcrEstRealisePar entities.
     *
     * @Route("/", name="pcrestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pcrEstRealisePars = $em->getRepository('BbeesE3sBundle:PcrEstRealisePar')->findAll();

        return $this->render('pcrestrealisepar/index.html.twig', array(
            'pcrEstRealisePars' => $pcrEstRealisePars,
        ));
    }

    /**
     * Creates a new pcrEstRealisePar entity.
     *
     * @Route("/new", name="pcrestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pcrEstRealisePar = new Pcrestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\PcrEstRealiseParType', $pcrEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pcrEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('pcrestrealisepar_show', array('id' => $pcrEstRealisePar->getId()));
        }

        return $this->render('pcrestrealisepar/new.html.twig', array(
            'pcrEstRealisePar' => $pcrEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pcrEstRealisePar entity.
     *
     * @Route("/{id}", name="pcrestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(PcrEstRealisePar $pcrEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($pcrEstRealisePar);

        return $this->render('pcrestrealisepar/show.html.twig', array(
            'pcrEstRealisePar' => $pcrEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pcrEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="pcrestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PcrEstRealisePar $pcrEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($pcrEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\PcrEstRealiseParType', $pcrEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pcrestrealisepar_edit', array('id' => $pcrEstRealisePar->getId()));
        }

        return $this->render('pcrestrealisepar/edit.html.twig', array(
            'pcrEstRealisePar' => $pcrEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pcrEstRealisePar entity.
     *
     * @Route("/{id}", name="pcrestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, PcrEstRealisePar $pcrEstRealisePar)
    {
        $form = $this->createDeleteForm($pcrEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pcrEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('pcrestrealisepar_index');
    }

    /**
     * Creates a form to delete a pcrEstRealisePar entity.
     *
     * @param PcrEstRealisePar $pcrEstRealisePar The pcrEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PcrEstRealisePar $pcrEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pcrestrealisepar_delete', array('id' => $pcrEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
