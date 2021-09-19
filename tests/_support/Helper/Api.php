<?php
namespace App\Tests\Helper;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module {

  public function _after($settings = []) {
    $this->getModule('Symfony')->grabService('doctrine')->resetManager();
  }

/**
 * Create user or administrator and set auth cookie to client
 *
 * @param string $user
 * @param string $password
 */
  public function setAuth(string $user, string $password, string $role) {
    /** @var \Codeception\Module\Symfony $symfony */
    try {
      $symfony = $this->getModule('Symfony');
    } catch (ModuleException $e) {
      $this->fail('Unable to get module \'Symfony\'');
    }
    /** @var \Codeception\Module\Doctrine2 $doctrine */
    try {
      $doctrine = $this->getModule('Doctrine2');
    } catch (ModuleException $e) {
      $this->fail('Unable to get module \'Doctrine2\'');
    }
    /** @var UserPasswordHasherInterface $encoder */
    $encoder = $symfony->grabService('security.user_password_hasher');
    /** @var Uuid $uuid */
    $uuid = $doctrine->haveInRepository('App\Entity\User', [
      'username' => $user,
      'isActive' => true,
      'name' => $user,
      'role' => $role,
      'password' => $encoder
        ->hashPassword(new \App\Entity\User(), $password),
    ]);
    $user = $doctrine->grabEntityFromRepository('App\Entity\User', [
      'id' => $uuid,
    ]);
    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
    $symfony->grabService('security.token_storage')->setToken($token);
    /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
    $session = $symfony->grabService('session');
    $session->set('_security_main', serialize($token));
    $session->save();
    $cookie = new Cookie($session->getName(), $session->getId());
    $symfony->client->getCookieJar()->set($cookie);
  }
}
