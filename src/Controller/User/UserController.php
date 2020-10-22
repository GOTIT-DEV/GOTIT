<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * UserBundle & E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * UserBundle & E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle (see LICENSE file).  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Controller\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\Enums\Action;
use App\Entity\User;

/**
 * User controller.
 *
 * @Route("user")
 * @author Philippe Grison  <philippe.grison@mnhn.fr>, Louis Duchemin <ls.duchemin@gmail.com>
 */
class UserController extends AbstractController
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('App:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Get currently logged in user public informations
     * 
     * @Route("/current", name="user_current", methods={"GET"})
     */
    public function currentUserAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
        return new JSONResponse([
            "username" => $user->getUsername(),
            "role" => $user->getRole(),
            "name" => $user->getName(),
            "institution" => $user->getInstitution(),
            "email" => $user->getEmail(),
        ]);
    }


    /**
     * Return to json format a list of fields to show  tab_station_toshow with the following criterion :  
     * a) 1 search criterion ($ request-> get ('searchPhrase')) insensitive to the case and  applied to a field
     * b) the number of lines to display ($ request-> get ('rowCount'))
     * c) 1 sort criterion on a collone ($ request-> get ('sort'))
     *
     * @Route("/indexjson", name="user_indexjson", methods={"POST"})
     */
    public function indexjsonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $rowCount = ($request->get('rowCount')  !== NULL)
            ? $request->get('rowCount') : 10;
        $orderBy = ($request->get('sort')  !== NULL)
            ? $request->get('sort')
            : array('user.dateMaj' => 'desc', 'user.id' => 'desc');
        $minRecord = intval($request->get('current') - 1) * $rowCount;
        $maxRecord = $rowCount;
        $tab_toshow = [];
        $entities_toshow = $em->getRepository("App:User")
            ->createQueryBuilder('user')
            ->where('LOWER(user.username) LIKE :criteriaLower')
            ->setParameter('criteriaLower', strtolower($request->get('searchPhrase')) . '%')
            ->addOrderBy(array_keys($orderBy)[0], array_values($orderBy)[0])
            ->getQuery()
            ->getResult();
        $nb_entities = count($entities_toshow);
        $entities_toshow = array_slice($entities_toshow, $minRecord, $rowCount);

        foreach ($entities_toshow as $entity) {
            $id = $entity->getId();
            $DateCre = ($entity->getDateCre() !== null)
                ?  $entity->getDateCre()->format('Y-m-d H:i:s') : null;
            $DateMaj = ($entity->getDateMaj() !== null)
                ?  $entity->getDateMaj()->format('Y-m-d H:i:s') : null;
            $tab_toshow[] = array(
                "id" => $id,
                "user.id" => $id,
                "user.username" => $entity->getUsername(),
                "user.password" => $entity->getPassword(),
                "user.email" => $entity->getEmail(),
                "user.role" => $entity->getRole(),
                "user.name" => $entity->getName(),
                "user.institution" => $entity->getInstitution(),
                "user.commentaireUser" => $entity->getCommentaireUser(),
                "user.dateCre" => $DateCre,
                "user.dateMaj" => $DateMaj,
            );
        }
        return new JsonResponse([
            "current"    => intval($request->get('current')),
            "rowCount"  => $rowCount,
            "rows"     => $tab_toshow,
            "total"    => $nb_entities // total data array				
        ]);
    }


    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm('App\Form\UserType', $user, [
            'action_type' => Action::create()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encodage du password
            $plainPassword = $user->getPlainPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);
            
            $user->setPassword($encoded);
            //
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            try {
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                return $this->render(
                    'user/index.html.twig',
                    array('exception_message' =>  explode("\n", $exception_message)[0])
                );
            }
            return $this->redirectToRoute('user_edit', array(
                'id' => $user->getId(), 'valid' => 1
            ));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        $editForm = $this->createForm('App\Form\UserType', $user, [
            'action_type' => Action::show()
        ]);
        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_COLLABORATION')")
     */
    public function editAction(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        //  access control for user type  : ROLE_COLLABORATION
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (
            $user->getRole() ==  'ROLE_COLLABORATION' &&
            $user->getUserCre() != $user->getId()
        ) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'ACCESS DENIED');
        }
        //
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('App\Form\UserType', $user, [
            'action_type' => Action::edit()
        ]);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // password encoding
            $plainPassword = $user->getPlainPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);
            
            $user->setPassword($encoded);
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                return $this->render('user/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
            }
            return $this->render('user/edit.html.twig', array(
                'user' => $user,
                'edit_form' => $editForm->createView(),
                'valid' => 1,
            ));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');
        

        if (($form->isSubmitted() && $form->isValid()) || $this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $em = $this->getDoctrine()->getManager();
            if ($user->getRole() != 'ROLE_ADMIN') {
                try {
                    $em->remove($user);
                    $em->flush();
                } catch (\Doctrine\DBAL\DBALException $e) {
                    $exception_message =  str_replace('"', '\"', str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')));
                    return $this->render('user/index.html.twig', array('exception_message' =>  explode("\n", $exception_message)[0]));
                }
            } else {
                return $this->render('user/index.html.twig', array('exception_message' =>  'You can\'t delete this Admin user compt'));
            }
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
