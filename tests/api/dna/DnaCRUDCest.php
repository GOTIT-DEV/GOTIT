<?php
namespace App\Tests;
use App\Entity\Dna;
use App\Tests\ApiTester;
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
    $I->haveHttpHeader('Content-Type', 'multipart/form-data');
    $this->router = $I->grabService('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
  }

  public function testDnaDeletion(ApiTester $I) {
    $I->wantTo('Check successful DNA deletion');
    $dna = $I->have("App\Entity\Dna");
    $I->sendDelete($this->generateRoute("app_api_dna_delete", ["id" => $dna->getId()]));
    $I->seeResponseCodeIs(204);
    $I->dontSeeInRepository(\App\Entity\Dna::class, ['id' => $dna->getId()]);
  }

  public function testFailDnaDeletionWithPCR(ApiTester $I) {
    $I->wantTo('Fail deletion of DNA having related PCR(s)');
    $pcr = $I->have('App\Entity\Pcr');
    $I->sendDelete($this->generateRoute("app_api_dna_delete", ["id" => $pcr->getDna()->getId()]));
    $I->seeResponseCodeIs(400);
  }

  public function testListAllDna(ApiTester $I) {
    $I->wantTo('Test list of all DNA entities');
    $dna = $I->have('App\Entity\Dna');
    $I->sendGet($this->generateRoute('app_api_dna_list', ["perPage" => 0]));
    $I->seeResponseCodeIs(200);
    $I->seeResponseIsJson();
    $dnas = $I->grabEntitiesFromRepository(Dna::class, []);
    $dna_count = count($dnas);
    $I->seeResponseContainsJson(["pagination" => ["total_items" => $dna_count]]);
    $items = $I->grabDataFromResponseByJsonPath('$.items.*');
    $I->assertCount($dna_count, $items);
  }

}