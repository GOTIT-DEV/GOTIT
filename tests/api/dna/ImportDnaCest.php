<?php
namespace App\Tests;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

function useCSV($csv) {
  return ['csvFile' => 'tests/_data/imports/dna/' . $csv];
}

class ImportDnaCest {
  public function _before(ApiTester $I) {
    $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    $I->haveHttpHeader('accept', 'application/json');
    $I->haveHttpHeader('Content-Type', 'multipart/form-data');
  }

  // tests
  public function tryToImportTwoDnas(ApiTester $I) {
    $I->wantTo('Import 2 DNA CSV records');
    $files = useCSV('dna_import_success.csv');
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::CREATED);
  }

  public function failToImportDnasBadCodes(ApiTester $I) {
    $I->wantTo('Fail importing records of DNA with incorrect code');
    $files = useCSV('dna_import_failure.csv');
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
  }

  public function failToImportDnasExistingCode(ApiTester $I) {
    $I->wantTo('Fail importing records of DNA when code already exists');
    $files = useCSV('dna_import_success.csv');
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::CREATED);
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
  }

  public function failToImportDnasWithMisformedHeader(ApiTester $I) {
    $I->wantTo('Fail to import records of DNA when CSV header is misformed');
    $files = useCSV('dna_import_bad_header.csv');
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    // TODO : also check for related entities
  }
}
