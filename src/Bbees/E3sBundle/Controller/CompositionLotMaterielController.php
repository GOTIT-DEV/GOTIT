<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\CompositionLotMateriel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Compositionlotmateriel controller.
 *
 * @Route("compositionlotmateriel")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CompositionLotMaterielController extends Controller
{
    /**
     * Lists all compositionLotMateriel entities.
     *
     * @Route("/", name="compositionlotmateriel_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $compositionLotMateriels = $em->getRepository('BbeesE3sBundle:CompositionLotMateriel')->findAll();

        return $this->render('compositionlotmateriel/index.html.twig', array(
            'compositionLotMateriels' => $compositionLotMateriels,
        ));
    }

    /**
     * Creates a new compositionLotMateriel entity.
     *
     * @Route("/new", name="compositionlotmateriel_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $compositionLotMateriel = new Compositionlotmateriel();
        $form = $this->createForm('Bbees\E3sBundle\Form\CompositionLotMaterielType', $compositionLotMateriel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($compositionLotMateriel);
            $em->flush();

            return $this->redirectToRoute('compositionlotmateriel_show', array('id' => $compositionLotMateriel->getId()));
        }

        return $this->render('compositionlotmateriel/new.html.twig', array(
            'compositionLotMateriel' => $compositionLotMateriel,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a compositionLotMateriel entity.
     *
     * @Route("/{id}", name="compositionlotmateriel_show")
     * @Method("GET")
     */
    public function showAction(CompositionLotMateriel $compositionLotMateriel)
    {
        $deleteForm = $this->createDeleteForm($compositionLotMateriel);

        return $this->render('compositionlotmateriel/show.html.twig', array(
            'compositionLotMateriel' => $compositionLotMateriel,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing compositionLotMateriel entity.
     *
     * @Route("/{id}/edit", name="compositionlotmateriel_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CompositionLotMateriel $compositionLotMateriel)
    {
        $deleteForm = $this->createDeleteForm($compositionLotMateriel);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\CompositionLotMaterielType', $compositionLotMateriel);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compositionlotmateriel_edit', array('id' => $compositionLotMateriel->getId()));
        }

        return $this->render('compositionlotmateriel/edit.html.twig', array(
            'compositionLotMateriel' => $compositionLotMateriel,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a compositionLotMateriel entity.
     *
     * @Route("/{id}", name="compositionlotmateriel_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CompositionLotMateriel $compositionLotMateriel)
    {
        $form = $this->createDeleteForm($compositionLotMateriel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($compositionLotMateriel);
            $em->flush();
        }

        return $this->redirectToRoute('compositionlotmateriel_index');
    }

    /**
     * Creates a form to delete a compositionLotMateriel entity.
     *
     * @param CompositionLotMateriel $compositionLotMateriel The compositionLotMateriel entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CompositionLotMateriel $compositionLotMateriel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('compositionlotmateriel_delete', array('id' => $compositionLotMateriel->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
