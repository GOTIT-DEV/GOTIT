<?php

/*
 * This file is part of the E3sBundle.
 *
 * Copyright (c) 2018 Philippe Grison <philippe.grison@mnhn.fr>
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace Bbees\E3sBundle\Controller;

use Bbees\E3sBundle\Entity\ACibler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Acibler controller.
 *
 * @Route("acibler")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ACiblerController extends Controller
{
    /**
     * Lists all aCibler entities.
     *
     * @Route("/", name="acibler_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $aCiblers = $em->getRepository('BbeesE3sBundle:ACibler')->findAll();

        return $this->render('acibler/index.html.twig', array(
            'aCiblers' => $aCiblers,
        ));
    }

    /**
     * Creates a new aCibler entity.
     *
     * @Route("/new", name="acibler_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $aCibler = new Acibler();
        $form = $this->createForm('Bbees\E3sBundle\Form\ACiblerType', $aCibler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($aCibler);
            $em->flush();

            return $this->redirectToRoute('acibler_show', array('id' => $aCibler->getId()));
        }

        return $this->render('acibler/new.html.twig', array(
            'aCibler' => $aCibler,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a aCibler entity.
     *
     * @Route("/{id}", name="acibler_show")
     * @Method("GET")
     */
    public function showAction(ACibler $aCibler)
    {
        $deleteForm = $this->createDeleteForm($aCibler);

        return $this->render('acibler/show.html.twig', array(
            'aCibler' => $aCibler,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing aCibler entity.
     *
     * @Route("/{id}/edit", name="acibler_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ACibler $aCibler)
    {
        $deleteForm = $this->createDeleteForm($aCibler);
        $editForm = $this->createForm('Bbees\E3sBundle\Form\ACiblerType', $aCibler);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('acibler_edit', array('id' => $aCibler->getId()));
        }

        return $this->render('acibler/edit.html.twig', array(
            'aCibler' => $aCibler,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a aCibler entity.
     *
     * @Route("/{id}", name="acibler_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ACibler $aCibler)
    {
        $form = $this->createDeleteForm($aCibler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($aCibler);
            $em->flush();
        }

        return $this->redirectToRoute('acibler_index');
    }

    /**
     * Creates a form to delete a aCibler entity.
     *
     * @param ACibler $aCibler The aCibler entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ACibler $aCibler)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('acibler_delete', array('id' => $aCibler->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
