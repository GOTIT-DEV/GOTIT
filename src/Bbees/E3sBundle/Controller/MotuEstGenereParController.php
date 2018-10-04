<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\MotuEstGenerePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Motuestgenerepar controller.
 *
 * @Route("motuestgenerepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class MotuEstGenereParController extends Controller
{
    /**
     * Lists all motuEstGenerePar entities.
     *
     * @Route("/", name="motuestgenerepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $motuEstGenerePars = $em->getRepository('BbeesE3sBundle:MotuEstGenerePar')->findAll();

        return $this->render('motuestgenerepar/index.html.twig', array(
            'motuEstGenerePars' => $motuEstGenerePars,
        ));
    }

    /**
     * Creates a new motuEstGenerePar entity.
     *
     * @Route("/new", name="motuestgenerepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $motuEstGenerePar = new Motuestgenerepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\MotuEstGenereParType', $motuEstGenerePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($motuEstGenerePar);
            $em->flush();

            return $this->redirectToRoute('motuestgenerepar_show', array('id' => $motuEstGenerePar->getId()));
        }

        return $this->render('motuestgenerepar/new.html.twig', array(
            'motuEstGenerePar' => $motuEstGenerePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a motuEstGenerePar entity.
     *
     * @Route("/{id}", name="motuestgenerepar_show")
     * @Method("GET")
     */
    public function showAction(MotuEstGenerePar $motuEstGenerePar)
    {
        $deleteForm = $this->createDeleteForm($motuEstGenerePar);

        return $this->render('motuestgenerepar/show.html.twig', array(
            'motuEstGenerePar' => $motuEstGenerePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing motuEstGenerePar entity.
     *
     * @Route("/{id}/edit", name="motuestgenerepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, MotuEstGenerePar $motuEstGenerePar)
    {
        $deleteForm = $this->createDeleteForm($motuEstGenerePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\MotuEstGenereParType', $motuEstGenerePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('motuestgenerepar_edit', array('id' => $motuEstGenerePar->getId()));
        }

        return $this->render('motuestgenerepar/edit.html.twig', array(
            'motuEstGenerePar' => $motuEstGenerePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a motuEstGenerePar entity.
     *
     * @Route("/{id}", name="motuestgenerepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, MotuEstGenerePar $motuEstGenerePar)
    {
        $form = $this->createDeleteForm($motuEstGenerePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($motuEstGenerePar);
            $em->flush();
        }

        return $this->redirectToRoute('motuestgenerepar_index');
    }

    /**
     * Creates a form to delete a motuEstGenerePar entity.
     *
     * @param MotuEstGenerePar $motuEstGenerePar The motuEstGenerePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MotuEstGenerePar $motuEstGenerePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('motuestgenerepar_delete', array('id' => $motuEstGenerePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
