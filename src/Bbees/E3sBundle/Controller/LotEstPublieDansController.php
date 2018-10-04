<?php

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\LotEstPublieDans;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Lotestpubliedan controller.
 *
 * @Route("lotestpubliedans")
 * @Security("has_role('ROLE_ADMIN')")
 */
class LotEstPublieDansController extends Controller
{
    /**
     * Lists all lotEstPublieDan entities.
     *
     * @Route("/", name="lotestpubliedans_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $lotEstPublieDans = $em->getRepository('BbeesE3sBundle:LotEstPublieDans')->findAll();

        return $this->render('lotestpubliedans/index.html.twig', array(
            'lotEstPublieDans' => $lotEstPublieDans,
        ));
    }

    /**
     * Creates a new lotEstPublieDan entity.
     *
     * @Route("/new", name="lotestpubliedans_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $lotEstPublieDan = new Lotestpubliedan();
        $form = $this->createForm('Bbees\E3sBundle\Form\LotEstPublieDansType', $lotEstPublieDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lotEstPublieDan);
            $em->flush();

            return $this->redirectToRoute('lotestpubliedans_show', array('id' => $lotEstPublieDan->getId()));
        }

        return $this->render('lotestpubliedans/new.html.twig', array(
            'lotEstPublieDan' => $lotEstPublieDan,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a lotEstPublieDan entity.
     *
     * @Route("/{id}", name="lotestpubliedans_show")
     * @Method("GET")
     */
    public function showAction(LotEstPublieDans $lotEstPublieDan)
    {
        $deleteForm = $this->createDeleteForm($lotEstPublieDan);

        return $this->render('lotestpubliedans/show.html.twig', array(
            'lotEstPublieDan' => $lotEstPublieDan,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing lotEstPublieDan entity.
     *
     * @Route("/{id}/edit", name="lotestpubliedans_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LotEstPublieDans $lotEstPublieDan)
    {
        $deleteForm = $this->createDeleteForm($lotEstPublieDan);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\LotEstPublieDansType', $lotEstPublieDan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lotestpubliedans_edit', array('id' => $lotEstPublieDan->getId()));
        }

        return $this->render('lotestpubliedans/edit.html.twig', array(
            'lotEstPublieDan' => $lotEstPublieDan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a lotEstPublieDan entity.
     *
     * @Route("/{id}", name="lotestpubliedans_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, LotEstPublieDans $lotEstPublieDan)
    {
        $form = $this->createDeleteForm($lotEstPublieDan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lotEstPublieDan);
            $em->flush();
        }

        return $this->redirectToRoute('lotestpubliedans_index');
    }

    /**
     * Creates a form to delete a lotEstPublieDan entity.
     *
     * @param LotEstPublieDans $lotEstPublieDan The lotEstPublieDan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LotEstPublieDans $lotEstPublieDan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lotestpubliedans_delete', array('id' => $lotEstPublieDan->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
