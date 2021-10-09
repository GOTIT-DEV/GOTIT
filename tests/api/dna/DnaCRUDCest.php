<?php

namespace App\Tests;

use App\Entity\Dna;
use App\Entity\Person;
use App\Entity\Specimen;
use App\Entity\Voc;
use Codeception\Util\HttpCode;
use Faker\Factory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DnaCRUDCest {
  protected $router;

  public function _before(ApiTester $I) {
    $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    $I->haveHttpHeader('accept', 'application/json');
    $I->haveHttpHeader('content-type', 'application/json');
    $this->router = $I->grabService('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
  }

  public function testDnaCreate(ApiTester $I) {
    $I->wantTo('Create a DNA');
    $faker = Factory::create();
    $dna_info = [
      'code' => $faker->word(),
      'date' => $faker->date(),
      'datePrecision' => '/api/vocs/' . $I->grabEntitiesFromRepository(
        Voc::class,
        ['parent' => 'datePrecision']
      )[0]->getId(),
      'extractionMethod' => '/api/vocs/' . $I->grabEntitiesFromRepository(
        Voc::class,
        ['parent' => 'methodeExtractionAdn']
      )[0]->getId(),
      'quality' => '/api/vocs/' . $I->grabEntitiesFromRepository(
        Voc::class,
        ['parent' => 'qualiteAdn']
      )[0]->getId(),
      'specimen' => '/api/specimens/' . $I->have(Specimen::class)->getId(),
      'producers' => ['/api/people/' . $I->have(Person::class)->getId()],
    ];
    $I->sendPost($this->generateRoute('api_dnas_post_collection'), $dna_info);
    $I->seeResponseCodeIs(201);
    $dna = $I->grabEntityFromRepository(Dna::class, ['code' => $dna_info['code']]);
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
