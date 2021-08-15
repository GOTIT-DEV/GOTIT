<?php

namespace App\Controller\API;

use App\Entity\Voc;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Vocabulary API controller
 *
 * @Rest\Route("/voc")
 * @Security("is_granted('ROLE_INVITED')")
 */
class VocController extends AbstractFOSRestController {

  /**
   * List Voc entities by parent property
   *
   * @param string $parent
   *
   * @Rest\Get("/parent/{parent}", requirements = {"parent"="[a-zA-Z]+"})
   * @Rest\View()
   */
  public function listByParent(string $parent) {
    return $this->getDoctrine()
      ->getRepository(Voc::class)
      ->findByParent($parent);
  }
}