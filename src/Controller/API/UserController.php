<?php

namespace App\Controller\API;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Rest\Route("/user")
 */
class UserController {

  /**
   * @Rest\Get("/{id}", requirements={"id":"\d+"})
   * @Rest\View()
   *
   * @param User $user
   * @return User
   */
  public function show(User $user) {
    return $user;
  }

}