<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\EstIdentifiePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Estidentifiepar controller.
 *
 * @Route("estidentifiepar")
 */
class EstIdentifieParController extends Controller
{
    /**
     * Lists all estIdentifiePar entities.
     *
     * @Route("/", name="estidentifiepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estIdentifiePars = $em->getRepository('BbeesE3sBundle:EstIdentifiePar')->findAll();

        return $this->render('estidentifiepar/index.html.twig', array(
            'estIdentifiePars' => $estIdentifiePars,
        ));
    }

    /**
     * Creates a new estIdentifiePar entity.
     *
     * @Route("/new", name="estidentifiepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $estIdentifiePar = new Estidentifiepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\EstIdentifieParType', $estIdentifiePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estIdentifiePar);
            $em->flush();

            return $this->redirectToRoute('estidentifiepar_show', array('id' => $estIdentifiePar->getId()));
        }

        return $this->render('estidentifiepar/new.html.twig', array(
            'estIdentifiePar' => $estIdentifiePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a estIdentifiePar entity.
     *
     * @Route("/{id}", name="estidentifiepar_show")
     * @Method("GET")
     */
    public function showAction(EstIdentifiePar $estIdentifiePar)
    {
        $deleteForm = $this->createDeleteForm($estIdentifiePar);

        return $this->render('estidentifiepar/show.html.twig', array(
            'estIdentifiePar' => $estIdentifiePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing estIdentifiePar entity.
     *
     * @Route("/{id}/edit", name="estidentifiepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EstIdentifiePar $estIdentifiePar)
    {
        $deleteForm = $this->createDeleteForm($estIdentifiePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\EstIdentifieParType', $estIdentifiePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estidentifiepar_edit', array('id' => $estIdentifiePar->getId()));
        }

        return $this->render('estidentifiepar/edit.html.twig', array(
            'estIdentifiePar' => $estIdentifiePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a estIdentifiePar entity.
     *
     * @Route("/{id}", name="estidentifiepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstIdentifiePar $estIdentifiePar)
    {
        $form = $this->createDeleteForm($estIdentifiePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estIdentifiePar);
            $em->flush();
        }

        return $this->redirectToRoute('estidentifiepar_index');
    }

    /**
     * Creates a form to delete a estIdentifiePar entity.
     *
     * @param EstIdentifiePar $estIdentifiePar The estIdentifiePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstIdentifiePar $estIdentifiePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estidentifiepar_delete', array('id' => $estIdentifiePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
