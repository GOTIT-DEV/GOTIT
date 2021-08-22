<?php
namespace App\Tests;
use App\Tests\ApiTester;
use Codeception\Util\HttpCode;

class ImportDnaCest {
  public function _before(ApiTester $I) {
  }

  // tests
  public function tryToImportOneDna(ApiTester $I) {
    $I->wantTo('Import DNA CSV records');
    $I->setAuth('admin_testing', 'admintesting', 'ROLE_ADMIN');
    $I->haveHttpHeader('accept', 'application/json');
    $I->haveHttpHeader('Content-Type', 'multipart/form-data');
    $files = [
      'csvFile' => 'tests/_data/imports/dna/dna_test_import.csv',
    ];
    $I->sendPost('/dna/import', [], $files);
    $I->seeResponseCodeIs(HttpCode::CREATED);
  }
}
