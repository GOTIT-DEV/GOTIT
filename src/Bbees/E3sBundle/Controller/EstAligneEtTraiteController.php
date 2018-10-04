<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\EstAligneEtTraite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Estaligneettraite controller.
 *
 * @Route("estaligneettraite")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EstAligneEtTraiteController extends Controller
{
    /**
     * Lists all estAligneEtTraite entities.
     *
     * @Route("/", name="estaligneettraite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estAligneEtTraites = $em->getRepository('BbeesE3sBundle:EstAligneEtTraite')->findAll();

        return $this->render('estaligneettraite/index.html.twig', array(
            'estAligneEtTraites' => $estAligneEtTraites,
        ));
    }

    /**
     * Creates a new estAligneEtTraite entity.
     *
     * @Route("/new", name="estaligneettraite_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $estAligneEtTraite = new Estaligneettraite();
        $form = $this->createForm('Bbees\E3sBundle\Form\EstAligneEtTraiteType', $estAligneEtTraite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estAligneEtTraite);
            $em->flush();

            return $this->redirectToRoute('estaligneettraite_show', array('id' => $estAligneEtTraite->getId()));
        }

        return $this->render('estaligneettraite/new.html.twig', array(
            'estAligneEtTraite' => $estAligneEtTraite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a estAligneEtTraite entity.
     *
     * @Route("/{id}", name="estaligneettraite_show")
     * @Method("GET")
     */
    public function showAction(EstAligneEtTraite $estAligneEtTraite)
    {
        $deleteForm = $this->createDeleteForm($estAligneEtTraite);

        return $this->render('estaligneettraite/show.html.twig', array(
            'estAligneEtTraite' => $estAligneEtTraite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing estAligneEtTraite entity.
     *
     * @Route("/{id}/edit", name="estaligneettraite_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EstAligneEtTraite $estAligneEtTraite)
    {
        $deleteForm = $this->createDeleteForm($estAligneEtTraite);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\EstAligneEtTraiteType', $estAligneEtTraite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estaligneettraite_edit', array('id' => $estAligneEtTraite->getId()));
        }

        return $this->render('estaligneettraite/edit.html.twig', array(
            'estAligneEtTraite' => $estAligneEtTraite,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a estAligneEtTraite entity.
     *
     * @Route("/{id}", name="estaligneettraite_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstAligneEtTraite $estAligneEtTraite)
    {
        $form = $this->createDeleteForm($estAligneEtTraite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estAligneEtTraite);
            $em->flush();
        }

        return $this->redirectToRoute('estaligneettraite_index');
    }

    /**
     * Creates a form to delete a estAligneEtTraite entity.
     *
     * @param EstAligneEtTraite $estAligneEtTraite The estAligneEtTraite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstAligneEtTraite $estAligneEtTraite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estaligneettraite_delete', array('id' => $estAligneEtTraite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
