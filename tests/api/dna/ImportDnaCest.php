<?php

namespace App\Tests;

use Codeception\Util\HttpCode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

function useCSV($csv) {
  return json_encode(['csv' => file_get_contents('tests/_data/imports/dna/' . $csv)]);
}

class ImportDnaCest {
  private function generateRoute(string $route, array $params = []) {
    return $this->router->generate(
      $route,
      $params,
      UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function _before(ApiTester $I) {
    $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    $I->haveHttpHeader('accept', 'application/json');
    $I->haveHttpHeader('Content-Type', 'application/json');
    $this->router = $I->grabService('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
  }

  // tests
  public function tryToImportTwoDnas(ApiTester $I) {
    $I->wantTo('Import 2 DNA CSV records');
    $files = useCSV('dna_import_success.csv');
    $I->sendPost($this->generateRoute('api_dnas_import_collection'), $files);
    $I->seeResponseCodeIs(HttpCode::CREATED);
  }

  // public function failToImportDnasBadCodes(ApiTester $I) {
  //   $I->wantTo('Fail to import records of DNA with incorrect code');
  //   $files = useCSV('dna_import_failure.csv');
  //   $I->sendPost('/dnas/import', $files);
  //   $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
  // }

  public function failToImportDnasExistingCode(ApiTester $I) {
    $I->wantTo('Fail to import records of DNA when code already exists');
    $files = useCSV('dna_import_success.csv');
    $I->sendPost('/dnas/import', $files);
    $I->seeResponseCodeIs(HttpCode::CREATED);
    $I->sendPost('/dnas/import', $files);
    $I->seeResponseCodeIs(HttpCode::UNPROCESSABLE_ENTITY);
  }

  // public function failToImportDnasWithMisformedHeader(ApiTester $I) {
  //   $I->wantTo('Fail to import records of DNA when CSV header is misformed');
  //   $files = useCSV('dna_import_bad_header.csv');
  //   $I->sendPost('/dna/import', $files);
  //   $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
  //   // TODO : also check for related entities
  // }
}
