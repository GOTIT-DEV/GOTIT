<?php

namespace App\Tests;

use App\Entity\Dna;
use Codeception\Util\HttpCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DnaCRUDCest {
  protected $router;

  public function _before(ApiTester $I) {
    $user = $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    // $encoder = $I->grabService('security.user_password_hasher');
    // $I->haveInRepository('App\Entity\User', [
    //   'username' => 'admin_testing',
    //   'isActive' => true,
    //   'name' => 'admin_testing',
    //   'role' => 'ROLE_ADMIN',
    //   'password' => $encoder->hashPassword(new \App\Entity\User(), 'admintesting'),
    // ]);
    // $I->amHttpAuthenticated('admin_testing', 'admintesting');
    $I->haveHttpHeader('accept', 'application/json');
    $this->router = $I->grabService('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
  }

  public function testDnaDeletion(ApiTester $I) {
    $I->wantTo('Check successful DNA deletion');
    $dna = $I->have('App\\Entity\\Dna');
    $dnaId = $dna->getId();
    $I->sendDelete($this->generateRoute('api_dnas_delete_item', ['id' => $dnaId]));
    $I->seeResponseCodeIs(204);
    $I->dontSeeInRepository(\App\Entity\Dna::class, ['id' => $dnaId]);
  }

  public function testFailDnaDeletionWithPCR(ApiTester $I) {
    $I->wantTo('Fail deletion of DNA having related PCR(s)');
    $dna = $I->have('App\Entity\Dna');
    $pcr = $I->have('App\Entity\Pcr', ['dna' => $dna]);
    $dna->addPcr($pcr);
    $I->persistEntity($dna);
    $I->sendDelete($this->generateRoute('api_dnas_delete_item', ['id' => $dna->getId()]));
    $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
  }

  public function testListAllDna(ApiTester $I) {
    $I->wantTo('Test list of all DNA entities');
    $I->haveHttpHeader('Content-Type', 'application/json');
    $I->haveHttpHeader('accept', 'application/ld+json');
    $I->sendGet($this->generateRoute('api_dnas_get_collection', ['pagination' => false]));
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $dnas = $I->grabEntitiesFromRepository(Dna::class, []);
    $dna_count = count($dnas);
    $totalItems = $I->grabDataFromResponseByJsonPath('$."hydra:totalItems"');
    $I->assertEquals($dna_count, $totalItems[0]);
    $items = $I->grabDataFromResponseByJsonPath('$."hydra:member".*');
    $I->assertCount($dna_count, $items);
  }

  private function generateRoute(string $route, array $params = []) {
    return $this->router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
  }
}
