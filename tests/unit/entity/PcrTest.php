<?php
namespace App\Tests;

use App\Tests\UnitTester;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PcrTest extends \Codeception\Test\Unit {
  /**
   * @var \App\Tests\UnitTester
   */
  protected $tester;

  protected function _before() {
  }

  protected function _after() {
  }

  // tests
  public function testValidationFailsForCustomCode() {
    $pcr = $this->tester->make('App\Entity\Pcr')->setCode('SOME_CUSTOM_CODE');
    $validator = $this->tester->grabService(ValidatorInterface::class);
    $errors = $validator->validate($pcr, null, ['Default', 'code']);

    $this->assertCount(1, $errors, "Count validation errors");
    $error = $errors->get(0);
    $this->assertTrue(
      $error->getPropertyPath() === 'code',
      "Assert validation error is on 'code' property."
    );
    $this->assertTrue(
      $error->getConstraint()->expression === 'this.hasValidCode()',
      "Assert violated constraint is hasValidCode()"
    );
  }
  public function testValidationPassesForCustomCodeWithDefaultGroup() {
    $pcr = $this->tester->make('App\Entity\Pcr')->setCode('SOME_CUSTOM_CODE');
    $validator = $this->tester->grabService(ValidatorInterface::class);
    $errors = $validator->validate($pcr, null, ['Default']);

    $this->assertCount(0, $errors, "Count validation errors");
  }
}