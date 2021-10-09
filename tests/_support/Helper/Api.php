<?php

namespace App\Tests\Helper;

use App\Entity\User;
use Codeception\Exception\ModuleException;
use Codeception\Module\Symfony;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module {
  public function _after($settings = []) {
    /** @var Symfony $symfony */
    $symfony = $this->getModule('Symfony');
    /** @var ManagerRegistry $doctrine */
    $doctrine = $symfony->grabService('doctrine');
    $doctrine->resetManager();
  }

  /**
   * Create user or administrator and set auth cookie to client
   */
  public function setAuth(string $user, string $password, string $role) {
    try {
      /** @var \Codeception\Module\Symfony $symfony */
      $symfony = $this->getModule('Symfony');
    } catch (ModuleException $e) {
      $this->fail('Unable to get module \'Symfony\'');
    }

    try {
      /** @var \Codeception\Module\Doctrine2 $doctrine */
      $doctrine = $this->getModule('Doctrine2');
    } catch (ModuleException $e) {
      $this->fail('Unable to get module \'Doctrine2\'');
    }

    // Create the user

    /** @var UserPasswordHasherInterface $encoder */
    $encoder = $symfony->grabService('security.user_password_hasher');
    /** @var int $user_id */
    $user_id = $doctrine->haveInRepository('App\Entity\User', [
      'username' => $user,
      'isActive' => true,
      'name' => $user,
      'role' => $role,
      'password' => $encoder
        ->hashPassword(new \App\Entity\User(), $password),
    ]);


    // Authenticate

    $user = $doctrine->grabEntityFromRepository('App\Entity\User', [
      'id' => $user_id,
    ]);
    $token = new UsernamePasswordToken($user, $password, 'main', [$user->getRole()]);
    $symfony->grabService('security.token_storage')->setToken($token);
    /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
    $session = $symfony->grabService('session');
    $session->set('_security_main', serialize($token));
    $session->save();
    $cookie = new Cookie($session->getName(), $session->getId());
    $symfony->client->getCookieJar()->set($cookie);
  }
}
