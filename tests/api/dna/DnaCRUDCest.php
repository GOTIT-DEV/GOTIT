<?php

namespace App\Tests;

use App\Entity\Dna;
use Codeception\Util\HttpCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DnaCRUDCest {
  protected $router;

  private function generateRoute(string $route, array $params = []) {
    return $this->router->generate(
      $route,
      $params,
      UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function _before(ApiTester $I) {
    $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    $I->haveHttpHeader('accept', 'application/json');
    $this->router = $I->grabService('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
  }

  public function testDnaDeletion(ApiTester $I) {
    $I->wantTo('Check successful DNA deletion');
    $dna = $I->have('App\\Entity\\Dna');
    $I->sendDelete($this->generateRoute('api_dnas_delete_item', ['id' => $dna->getId()]));
    $I->seeResponseCodeIs(204);
    $I->dontSeeInRepository(\App\Entity\Dna::class, ['id' => $dna->getId()]);
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
}
