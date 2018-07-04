<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\Boite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Boite controller.
 *
 * @Route("boite")
 */
class BoiteController extends Controller
{
    /**
     * Lists all boite entities.
     *
     * @Route("/", name="boite_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $boites = $em->getRepository('BbeesE3sBundle:Boite')->findAll();

        return $this->render('boite/index.html.twig', array(
            'boites' => $boites,
        ));
    }

    /**
     * Creates a new boite entity.
     *
     * @Route("/new", name="boite_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $boite = new Boite();
        $form = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($boite);
            $em->flush();

            return $this->redirectToRoute('boite_show', array('id' => $boite->getId()));
        }

        return $this->render('boite/new.html.twig', array(
            'boite' => $boite,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a boite entity.
     *
     * @Route("/{id}", name="boite_show")
     * @Method("GET")
     */
    public function showAction(Boite $boite)
    {
        $deleteForm = $this->createDeleteForm($boite);

        return $this->render('boite/show.html.twig', array(
            'boite' => $boite,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing boite entity.
     *
     * @Route("/{id}/edit", name="boite_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Boite $boite)
    {
        $deleteForm = $this->createDeleteForm($boite);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\BoiteType', $boite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('boite_edit', array('id' => $boite->getId()));
        }

        return $this->render('boite/edit.html.twig', array(
            'boite' => $boite,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a boite entity.
     *
     * @Route("/{id}", name="boite_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Boite $boite)
    {
        $form = $this->createDeleteForm($boite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($boite);
            $em->flush();
        }

        return $this->redirectToRoute('boite_index');
    }

    /**
     * Creates a form to delete a boite entity.
     *
     * @param Boite $boite The boite entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Boite $boite)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('boite_delete', array('id' => $boite->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
