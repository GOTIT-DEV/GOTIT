<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\APourFixateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Apourfixateur controller.
 *
 * @Route("apourfixateur")
 * @Security("has_role('ROLE_ADMIN')")
 */
class APourFixateurController extends Controller
{
    /**
     * Lists all aPourFixateur entities.
     *
     * @Route("/", name="apourfixateur_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aPourFixateurs = $em->getRepository('BbeesE3sBundle:APourFixateur')->findAll();

        return $this->render('apourfixateur/index.html.twig', array(
            'aPourFixateurs' => $aPourFixateurs,
        ));
    }

    /**
     * Creates a new aPourFixateur entity.
     *
     * @Route("/new", name="apourfixateur_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $aPourFixateur = new Apourfixateur();
        $form = $this->createForm('Bbees\E3sBundle\Form\APourFixateurType', $aPourFixateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($aPourFixateur);
            $em->flush();

            return $this->redirectToRoute('apourfixateur_show', array('id' => $aPourFixateur->getId()));
        }

        return $this->render('apourfixateur/new.html.twig', array(
            'aPourFixateur' => $aPourFixateur,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a aPourFixateur entity.
     *
     * @Route("/{id}", name="apourfixateur_show")
     * @Method("GET")
     */
    public function showAction(APourFixateur $aPourFixateur)
    {
        $deleteForm = $this->createDeleteForm($aPourFixateur);

        return $this->render('apourfixateur/show.html.twig', array(
            'aPourFixateur' => $aPourFixateur,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing aPourFixateur entity.
     *
     * @Route("/{id}/edit", name="apourfixateur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, APourFixateur $aPourFixateur)
    {
        $deleteForm = $this->createDeleteForm($aPourFixateur);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\APourFixateurType', $aPourFixateur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('apourfixateur_edit', array('id' => $aPourFixateur->getId()));
        }

        return $this->render('apourfixateur/edit.html.twig', array(
            'aPourFixateur' => $aPourFixateur,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a aPourFixateur entity.
     *
     * @Route("/{id}", name="apourfixateur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, APourFixateur $aPourFixateur)
    {
        $form = $this->createDeleteForm($aPourFixateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($aPourFixateur);
            $em->flush();
        }

        return $this->redirectToRoute('apourfixateur_index');
    }

    /**
     * Creates a form to delete a aPourFixateur entity.
     *
     * @param APourFixateur $aPourFixateur The aPourFixateur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(APourFixateur $aPourFixateur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('apourfixateur_delete', array('id' => $aPourFixateur->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
