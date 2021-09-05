<?php
namespace App\Tests\Helper;

use App\Entity\Dna;
use App\Entity\Pcr;
use App\Entity\Voc;
use League\FactoryMuffin\Faker\Facade as Faker;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Factories extends \Codeception\Module {
  public function _beforeSuite($settings = []) {
    $factory = $this->getModule('DataFactory');
    // let us get EntityManager from Doctrine
    $em = $this->getModule('Doctrine2')->_getEntityManager();

    $factory->_define(Voc::class, [
      'code' => Faker::word(),
      'libelle' => Faker::word(),
    ]);

    $factory->_define(Dna::class, [
      'code' => Faker::word(),
      'date' => Faker::dateTime(),
      'concentrationNgMicrolitre' => Faker::randomFloat(),
      'comment' => Faker::text(),
      'datePrecisionVocFk' => 'entity|App\Entity\Voc',
      'extractionMethodVocFk' => 'entity|App\Entity\Voc',
      'qualiteAdnVocFk' => 'entity|App\Entity\Voc',
    ]);

    $factory->_define(Pcr::class, [
      'code' => Faker::word(),
      'number' => Faker::word(),
      'date' => Faker::dateTime(),
      'details' => Faker::text(),
      'comment' => Faker::text(),
      'geneVocFk' => 'entity|App\Entity\Voc',
      'qualityVocFk' => 'entity|App\Entity\Voc',
      'specificityVocFk' => 'entity|App\Entity\Voc',
      'primerStartVocFk' => 'entity|App\Entity\Voc',
      'primerEndVocFk' => 'entity|App\Entity\Voc',
      'datePrecisionVocFk' => 'entity|App\Entity\Voc',
      'dnaFk' => 'entity|App\Entity\Dna',
    ]);
  }
}
