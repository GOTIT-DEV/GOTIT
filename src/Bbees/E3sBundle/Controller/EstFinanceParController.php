<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\EstFinancePar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Estfinancepar controller.
 *
 * @Route("estfinancepar")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EstFinanceParController extends Controller
{
    /**
     * Lists all estFinancePar entities.
     *
     * @Route("/", name="estfinancepar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $estFinancePars = $em->getRepository('BbeesE3sBundle:EstFinancePar')->findAll();

        return $this->render('estfinancepar/index.html.twig', array(
            'estFinancePars' => $estFinancePars,
        ));
    }

    /**
     * Creates a new estFinancePar entity.
     *
     * @Route("/new", name="estfinancepar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $estFinancePar = new Estfinancepar();
        $form = $this->createForm('Bbees\E3sBundle\Form\EstFinanceParType', $estFinancePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estFinancePar);
            $em->flush();

            return $this->redirectToRoute('estfinancepar_show', array('id' => $estFinancePar->getId()));
        }

        return $this->render('estfinancepar/new.html.twig', array(
            'estFinancePar' => $estFinancePar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a estFinancePar entity.
     *
     * @Route("/{id}", name="estfinancepar_show")
     * @Method("GET")
     */
    public function showAction(EstFinancePar $estFinancePar)
    {
        $deleteForm = $this->createDeleteForm($estFinancePar);

        return $this->render('estfinancepar/show.html.twig', array(
            'estFinancePar' => $estFinancePar,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing estFinancePar entity.
     *
     * @Route("/{id}/edit", name="estfinancepar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, EstFinancePar $estFinancePar)
    {
        $deleteForm = $this->createDeleteForm($estFinancePar);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\EstFinanceParType', $estFinancePar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('estfinancepar_edit', array('id' => $estFinancePar->getId()));
        }

        return $this->render('estfinancepar/edit.html.twig', array(
            'estFinancePar' => $estFinancePar,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a estFinancePar entity.
     *
     * @Route("/{id}", name="estfinancepar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, EstFinancePar $estFinancePar)
    {
        $form = $this->createDeleteForm($estFinancePar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($estFinancePar);
            $em->flush();
        }

        return $this->redirectToRoute('estfinancepar_index');
    }

    /**
     * Creates a form to delete a estFinancePar entity.
     *
     * @param EstFinancePar $estFinancePar The estFinancePar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EstFinancePar $estFinancePar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('estfinancepar_delete', array('id' => $estFinancePar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
