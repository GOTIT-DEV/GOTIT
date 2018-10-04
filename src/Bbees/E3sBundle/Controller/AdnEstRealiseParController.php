<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\AdnEstRealisePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Adnestrealisepar controller.
 *
 * @Route("adnestrealisepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdnEstRealiseParController extends Controller
{
    /**
     * Lists all adnEstRealisePar entities.
     *
     * @Route("/", name="adnestrealisepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adnEstRealisePars = $em->getRepository('BbeesE3sBundle:AdnEstRealisePar')->findAll();

        return $this->render('adnestrealisepar/index.html.twig', array(
            'adnEstRealisePars' => $adnEstRealisePars,
        ));
    }

    /**
     * Creates a new adnEstRealisePar entity.
     *
     * @Route("/new", name="adnestrealisepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $adnEstRealisePar = new Adnestrealisepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\AdnEstRealiseParType', $adnEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($adnEstRealisePar);
            $em->flush();

            return $this->redirectToRoute('adnestrealisepar_show', array('id' => $adnEstRealisePar->getId()));
        }

        return $this->render('adnestrealisepar/new.html.twig', array(
            'adnEstRealisePar' => $adnEstRealisePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a adnEstRealisePar entity.
     *
     * @Route("/{id}", name="adnestrealisepar_show")
     * @Method("GET")
     */
    public function showAction(AdnEstRealisePar $adnEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($adnEstRealisePar);

        return $this->render('adnestrealisepar/show.html.twig', array(
            'adnEstRealisePar' => $adnEstRealisePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing adnEstRealisePar entity.
     *
     * @Route("/{id}/edit", name="adnestrealisepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, AdnEstRealisePar $adnEstRealisePar)
    {
        $deleteForm = $this->createDeleteForm($adnEstRealisePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\AdnEstRealiseParType', $adnEstRealisePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('adnestrealisepar_edit', array('id' => $adnEstRealisePar->getId()));
        }

        return $this->render('adnestrealisepar/edit.html.twig', array(
            'adnEstRealisePar' => $adnEstRealisePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a adnEstRealisePar entity.
     *
     * @Route("/{id}", name="adnestrealisepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, AdnEstRealisePar $adnEstRealisePar)
    {
        $form = $this->createDeleteForm($adnEstRealisePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($adnEstRealisePar);
            $em->flush();
        }

        return $this->redirectToRoute('adnestrealisepar_index');
    }

    /**
     * Creates a form to delete a adnEstRealisePar entity.
     *
     * @param AdnEstRealisePar $adnEstRealisePar The adnEstRealisePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(AdnEstRealisePar $adnEstRealisePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('adnestrealisepar_delete', array('id' => $adnEstRealisePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
