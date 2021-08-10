<?php

namespace App\Services\Core;

use App\Services\Core\ImportFileCsv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Service ImportFileE3s
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ImportFileE3s {
  private $entityManager;
  private $importFileCsv;
  private $translator;

  /**
   *  __construct(EntityManager $manager,ImportFileCsv $importFileCsv )
   * $manager : service manager service of Doctrine ( @doctrine.orm.entity_manager )
   * $importFileCsv : CSV file import service
   */
  public function __construct(EntityManagerInterface $manager, ImportFileCsv $importFileCsv, TranslatorInterface $translator) {
    $this->entityManager = $manager;
    $this->importFileCsv = $importFileCsv;
    $this->translator = $translator;
  }

  /**
   *  importCSVDataDnaDeplace($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA_move
   */
  public function importCSVDataDnaDeplace($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataDnaRange = $importFileCsvService->readCSV($fichier);
    //$columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataDnaRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataDnaRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_dna = $em->getRepository("App:Dna")->createQueryBuilder('dna')
        ->where('dna.code  LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagDna = count($query_dna);
      if ($flagDna == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagDna && $flagStore) {
        if ($flagStoreAffecte) {
          $query_dna[0]->setStoreFk($query_store[0]);
          $query_dna[0]->setDateMaj($DateImport);
          $query_dna[0]->setUserMaj($userId);
          $em->persist($query_dna[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        } else {
          $query_dna[0]->setStoreFk(null);
          $query_dna[0]->setDateMaj($DateImport);
          $query_dna[0]->setUserMaj($userId);
          $em->persist($query_dna[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataDnaRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataDnaRange($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA_store
   */
  public function importCSVDataDnaRange($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataDnaRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataDnaRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataDnaRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_dna = $em->getRepository("Dna")->createQueryBuilder('dna')
        ->where('dna.code  LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagDna = count($query_dna);
      if ($flagDna == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no store code") . '<b> : ' . $data["code"] . " </b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagDna && $flagStore && $flagStoreAffecte) {
        if ($query_dna[0]->getStoreFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR dna already store') . '<b> : ' . $data["code"] . '</b> / ' . $query_dna[0]->getStoreFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_dna[0]->setStoreFk($query_store[0]);
          $query_dna[0]->setDateMaj($DateImport);
          $query_dna[0]->setUserMaj($userId);
          $em->persist($query_dna[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataDnaRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSlideDeplace($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is slide_move
   */
  public function importCSVDataSlideDeplace($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataSlidelRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataSlidelRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataSlidelRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lame = $em->getRepository("App:Slide")->createQueryBuilder('lame')
        ->where('lame.code  LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagLame = count($query_lame);
      if ($flagLame == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLame && $flagStore) {
        if ($flagStoreAffecte) {
          $query_lame[0]->setStoreFk($query_store[0]);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        } else {
          $query_lame[0]->setStoreFk(null);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataSlidelRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSlideRange($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is slide_store
   */
  public function importCSVDataSlideRange($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataSlidelRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataSlidelRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataSlidelRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lame = $em->getRepository("App:Slide")->createQueryBuilder('lame')
        ->where('lame.code  LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagLame = count($query_lame);
      if ($flagLame == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no store code") . '<b> : ' . $data["code"] . " </b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLame && $flagStore && $flagStoreAffecte) {
        if ($query_lame[0]->getStoreFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR slide already store') . '<b> : ' . $data["code"] . '</b> / ' . $query_lame[0]->getStoreFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_lame[0]->setStoreFk($query_store[0]);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataSlidelRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataLotMaterielDeplace($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is biological_material_move
   */
  public function importCSVDataLotMaterielDeplace($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataLotMaterielRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lot = $em->getRepository("App:InternalLot")->createQueryBuilder('lot')
        ->where('lot.code LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagLot = count($query_lot);
      if ($flagLot == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLot && $flagStore) {
        if ($flagStoreAffecte) {
          $query_lot[0]->setStoreFk($query_store[0]);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        } else {
          $query_lot[0]->setStoreFk(null);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataLotMaterielRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataLotMaterielRange($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is biological_material_store
   */
  public function importCSVDataLotMaterielRange($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataLotMaterielRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lot = $em->getRepository("App:InternalLot")->createQueryBuilder('lot')
        ->where('lot.code LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $flagLot = count($query_lot);
      if ($flagLot == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagStore = 1;
      $flagStoreAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagStoreAffecte = 1;
        $query_store = $em->getRepository("App:Store")->createQueryBuilder('store')
          ->where('store.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagStore = count($query_store);
      }
      if ($flagStoreAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no store code for material") . '<b> : ' . $data["code"] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagStoreAffecte && $flagStore == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLot && $flagStore && $flagStoreAffecte) {
        if ($query_lot[0]->getStoreFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR lot already store') . '<b> : ' . $data["code"] . '</b> / ' . $query_lot[0]->getStoreFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_lot[0]->setStoreFk($query_store[0]);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
          $query_store[0]->setDateMaj($DateImport);
          $query_store[0]->setUserMaj($userId);
          $em->persist($query_store[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataLotMaterielRange) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataLotMaterielPublie($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is source_attribute_to_lot
   */
  public function importCSVDataLotMaterielPublie($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataLotMaterielPublie = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataLotMaterielPublie); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataLotMaterielPublie as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lot = $em->getRepository("App:InternalLot")->createQueryBuilder('lot')
        ->where('lot.code LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $query_source = $em->getRepository("App:Source")->createQueryBuilder('source')
        ->where('source.codeSource LIKE :code_source')
        ->setParameter('code_source', $data["source.code_source"])
        ->getQuery()
        ->getResult();
      if (count($query_lot) == 0 || count($query_source) == 0) {
        if (count($query_lot) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

        if (count($query_source) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["source.code_source"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

      } else {
        $query_lepd = $em->getRepository("App:InternalLotPublication")->createQueryBuilder('lepd')
          ->where('lepd.internalLotFk = :id_lot')
          ->setParameter('id_lot', $query_lot[0]->getId())
          ->andwhere('source.codeSource = :code_source')
          ->setParameter('code_source', $data["source.code_source"])
          ->leftJoin('App:Source', 'source', 'WITH', 'lepd.sourceFk = source.id')
          ->getQuery()
          ->getResult();
        if (count($query_lepd) != 0) {
          $message .= $this->translator->trans('importfileService.ERROR lot already publish') . '<b> : ' . $data["source.code_source"] . ' / ' . $data["code"] . ' </b><br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $entityRel = new \App\Entity\InternalLotPublication();
          $method = "setSourceFk";
          $entityRel->$method($query_source[0]);
          $method = "setInternalLotFk";
          $entityRel->$method($query_lot[0]);
          $entityRel->setDateCre($DateImport);
          $entityRel->setDateMaj($DateImport);
          $entityRel->setUserCre($userId);
          $entityRel->setUserMaj($userId);
          $em->persist($entityRel);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataLotMaterielPublie) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSqcAssembleePublie($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is source_attribute_to_sequence
   */
  public function importCSVDataSqcAssembleePublie($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataSqcAssembleePublie = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataSqcAssembleePublie); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataSqcAssembleePublie as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_sa = $em->getRepository("App:InternalSequence")->createQueryBuilder('sa')
        ->where('sa.code LIKE :code')
        ->setParameter('code', $data["code"])
        ->getQuery()
        ->getResult();
      $query_source = $em->getRepository("App:Source")->createQueryBuilder('source')
        ->where('source.codeSource LIKE :code_source')
        ->setParameter('code_source', $data["source.code_source"])
        ->getQuery()
        ->getResult();
      if (count($query_sa) == 0 || count($query_source) == 0) {
        if (count($query_sa) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

        if (count($query_source) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["source.code_source"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

      } else {
        $query_lepd = $em->getRepository("App:InternalSequencePublication")->createQueryBuilder('sepd')
          ->where('sepd.internalSequenceFk = :id_sa')
          ->setParameter('id_sa', $query_sa[0]->getId())
          ->getQuery()
          ->getResult();
        if (count($query_lepd) != 0 || $query_sa[0]->getAccessionNumber() != '') {
          if (count($query_lepd) != 0) {
            $message .= $this->translator->trans('importfileService.ERROR sqc already publish') . '<b> : ' . $data["source.code_source"] . ' / ' . $data["code"] . ' </b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }

          if ($query_sa[0]->getAccessionNumber() != '') {
            $message .= $this->translator->trans('importfileService.ERROR assession number already assign') . '<b> : ' . $query_sa[0]->getAccessionNumber() . ' / ' . $data["code"] . ' </b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }

        } else {
          $method = "setAccessionNumber";
          $query_sa[0]->$method($data["accession_number"]);
          $query_sa[0]->setDateMaj($DateImport);
          $query_sa[0]->setUserMaj($userId);
          $em->persist($query_sa[0]);
          $entityRel = new \App\Entity\InternalSequencePublication();
          $method = "setSourceFk";
          $entityRel->$method($query_source[0]);
          $method = "setInternalSequenceFk";
          $entityRel->$method($query_sa[0]);
          $entityRel->setDateCre($DateImport);
          $entityRel->setDateMaj($DateImport);
          $entityRel->setUserCre($userId);
          $entityRel->setUserMaj($userId);
          $em->persist($entityRel);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataSqcAssembleePublie) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSource($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is source
   */
  public function importCSVDataSource($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Source();
      foreach ($columnByTable["source"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($dataColCsv === '') {
          $dataColCsv = NULL;
        }

        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          if ($ColCsv == 'source.code_source') {
            $record_entity = $em->getRepository("App:Source")->findOneBy(array("codeSource" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }

          // control and standardization of field formats
          if ($ColCsv == 'source.annee_source' && !is_null($dataColCsv)) {
            $dataColCsv = intval(str_replace(",", ".", $dataColCsv));
          }
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          if (!is_null($dataColCsv)) {
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " [" . $data[$ColCsv] . ']</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . "]</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);

      $em->persist($entity);

      # Record of  SourceProvider
      foreach ($columnByTable["source_provider"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SourceProvider();
            $method = "setSourceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataPcrChromato($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : ! the template of csv file to import is NOT YET SUPPORTED in V1.1
   */
  public function importCSVDataPcrChromato($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_pcr = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $flag_new_pcr = 1;
      # Records of pcr data
      $entity = new \App\Entity\Pcr();
      //
      foreach ($columnByTable["pcr"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'pcr.code') {
            $record_entity = $em->getRepository("App:Pcr")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'pcr.date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      // management of the pcr which gave rise to several chromato (n lines)
      if (array_key_exists($data['pcr.code'], $list_new_pcr)) {
        $flag_new_pcr = 0;
        $entity = $list_new_pcr[$data['pcr.code']];
      } else {
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
        $list_new_pcr[$data['pcr.code']] = $entity;
      }

      # Record of PcrProducer
      if ($flag_new_pcr) {
        foreach ($columnByTable["pcr_producer"] as $ColCsv) {
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
          if ($flag_foreign && trim($dataColCsv) != '') {
            foreach ($tab_foreign_field as $val_foreign_field) {
              $val_foreign_field = trim($val_foreign_field);
              $entityRel = new \App\Entity\PcrProducer();
              $method = "setPcrFk";
              $entityRel->$method($entity);
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
              } else {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                case "Voc":
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  break;
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
              } else {
                $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                $entityRel->$method($foreign_record);
              }
              $entityRel->setDateCre($DateImport);
              $entityRel->setDateMaj($DateImport);
              $entityRel->setUserCre($userId);
              $entityRel->setUserMaj($userId);
              $em->persist($entityRel);
            }
          }
        }
      }

      # Record of chromatogram
      $entityRel = new \App\Entity\Chromatogram();
      $method = "setPcrFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["chromatogram"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          if ($ColCsv == 'chromatogram.code_chromato') { // On teste pour savoir si le chromatogram.code_chromato a déja été créé.
            $record_entity = $em->getRepository("App:Chromatogram")->findOneBy(array("codeChromato" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign && $ColCsv != 'chromatogram.pcr_fk(pcr.code)') { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($foreign_record);
          }
          $entityRel->setDateCre($DateImport);
          $entityRel->setDateMaj($DateImport);
          $entityRel->setUserCre($userId);
          $entityRel->setUserMaj($userId);
          $em->persist($entityRel);
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataPcr($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import PCR
   */
  public function importCSVDataPcr($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Record PCR data
      $entity = new \App\Entity\Pcr();
      foreach ($columnByTable["pcr"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          if ($ColCsv == 'pcr.code') {
            $record_entity = $em->getRepository("App:Pcr")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'pcr.date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      // persist the PCR (1 pcr /line)
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of PcrProducer
      foreach ($columnByTable["pcr_producer"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\PcrProducer();
            $method = "setPcrFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataChromato(array $csvData)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is chromatogram
   */
  public function importCSVDataChromato($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Record of the chromatogram
      $entity = new \App\Entity\Chromatogram();
      foreach ($columnByTable["chromatogram"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'chromatogram.code_chromato') {
            $record_entity = $em->getRepository("App:Chromatogram")->findOneBy(array("codeChromato" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
          $entity->setDateCre($DateImport);
          $entity->setDateMaj($DateImport);
          $entity->setUserCre($userId);
          $entity->setUserMaj($userId);
          $em->persist($entity);
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataDna($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA
   */
  public function importCSVDataDna($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\Dna();
      //
      foreach ($columnByTable["dna"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($dataColCsv === '') {
          $dataColCsv = NULL;
        }

        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'dna.code') {
            $record_entity = $em->getRepository("App:Dna")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }

          // control and standardization of field formats
          if ($ColCsv == 'dna.concentration_ng_microlitre' && !is_null($dataColCsv)) {
            $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
          }
          // test of the date format
          if ($ColCsv == 'dna.date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if (!is_null($dataColCsv)) {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          if (!is_null($dataColCsv)) {
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . "]</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of DnaExtraction
      foreach ($columnByTable["dna_est_realise_par"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\DnaExtraction();
            $method = "setDnaFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataProgram($fichier, $userId = null)
   * $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is program
   */
  public function importCSVDataProgram($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_commune = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Program();
      if (array_key_exists("program", $columnByTable)) {
        foreach ($columnByTable["program"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          $varfield = explode(".", $field)[1];
          if ($field == 'program.code') {
            $record_entity = $em->getRepository("App:Program")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b><br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          if ($dataColCsv === '') {
            $dataColCsv = NULL;
          }
          // if there is no value, initialize the value to NULL
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv); // save the values ​​of the field
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSampling($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is sampling
   */
  public function importCSVDataSampling($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // appel du manager de Doctrine
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_person = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Sampling();
      foreach ($columnByTable["sampling"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($field == 'sampling.code') {
            $record_entity = $em->getRepository("App:Sampling")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $dataColCsv . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'sampling.conductance_micro_sie_cm' || $ColCsv == 'sampling.temperature_c') {
            if ($dataColCsv != '') {
              $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
              if ($dataColCsv == '') {
                $message .= $this->translator->trans('importfileService.ERROR bad float format') . '<b> : ' . $data[$ColCsv] . "</b>  <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }

            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'sampling.duration_mn') {
            if ($dataColCsv != '') {
              $dataColCsv = intval(str_replace(",", ".", $dataColCsv));
            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'sampling.status') {
            if ($dataColCsv != '') {
              if ($dataColCsv == 'OUI' || $dataColCsv == 'YES' || $dataColCsv == '1') {
                $dataColCsv = 1;
              }
              if ($dataColCsv == 'NON' || $dataColCsv == 'NO' || $dataColCsv == '0') {
                $dataColCsv = 0;
              }
              if ($dataColCsv !== 1 && $dataColCsv !== 0) {
                $message .= $this->translator->trans('importfileService.ERROR bad data OUI-NON') . '<b> : ' . $ColCsv . "/ " . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'sampling.date') {
            if ($dataColCsv != '') {
              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name) ex. : site.municipality(municipality.name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          // var_dump($varfield); var_dump($varfield_parent); var_dump($field);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . $field . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of TaxonSampling
      foreach ($columnByTable["a_cibler"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\TaxonSampling();
            $method = "setSamplingFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . "-" . $varfield . "-" . $varfield_parent . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of SamplingFixative
      foreach ($columnByTable["a_pour_fixateur"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SamplingFixative();
            $method = "setSamplingFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . "-" . $varfield . "-" . $varfield_parent . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of SamplingMethod
      foreach ($columnByTable["a_pour_sampling_method"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SamplingMethod();
            $method = "setSamplingFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . "-" . $varfield . "-" . $varfield_parent . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of SamplingParticipant
      foreach ($columnByTable["est_effectue_par"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SamplingParticipant();
            $method = "setSamplingFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . "-" . $varfield . "-" . $varfield_parent . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of SamplingFunding
      foreach ($columnByTable["est_finance_par"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SamplingFunding();
            $method = "setSamplingFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . "-" . $varfield . "-" . $varfield_parent . "-" . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataLame($fichier, $userId = null)
   * $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is specimen_slide
   */
  public function importCSVDataLame($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Enregistrement des données de lame
      $entity = new \App\Entity\Slide();
      //
      foreach ($columnByTable["slide"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($dataColCsv == '' || trim($dataColCsv) == '') {
          $dataColCsv = NULL;
        }

        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($field); var_dump($ColCsv);
          if ($ColCsv == 'slide.code') {
            $record_entity = $em->getRepository("App:Slide")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'slide.date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          if (!is_null($dataColCsv)) {
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of SlideProducer
      foreach ($columnByTable["slide_producer"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\SlideProducer();
            $method = "setSlideFk";
            $entityRel->$method($entity);
            if (!is_null($val_foreign_field) && $val_foreign_field != '') {
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
              } else {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                case "Voc":
                  if ($data[$ColCsv] == '') {
                    $foreign_record = NULL;
                  } else {
                    $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                  break;
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
              } else {
                $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                $entityRel->$method($foreign_record);
              }
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    # FLUSH
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSpecimen($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is specimen
   */
  public function importCSVDataSpecimen($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Enregistrement des données de Specimen
      $entity = new \App\Entity\Specimen();
      //
      foreach ($columnByTable["specimen"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($dataColCsv === '') {
          $dataColCsv = NULL;
        }

        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'specimen.code_ind_biomol' && !is_null($dataColCsv)) {
            $record_entity = $em->getRepository("App:Specimen")->findOneBy(array("codeIndBiomol" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          if ($ColCsv == 'code_ind_tri_morpho' && !is_null($dataColCsv)) {
            $record_entity = $em->getRepository("App:Specimen")->findOneBy(array("codeIndTriMorpho" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          if (!is_null($dataColCsv)) {
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of TaxonIdentification
      $key_taxname = array_keys($columnByTable["taxon_identification"], "taxon_identification.taxon_fk(taxon.taxname)")[0];
      // var_dump($data[$columnByTable["taxon_identification"][$key_taxname]]);
      $entityEspeceIdentifie = NULL;
      if ($data[$columnByTable["taxon_identification"][$key_taxname]] != '') {
        $entityRel = new \App\Entity\TaxonIdentification();
        $entityEspeceIdentifie = $entityRel;
        $method = "setSpecimenFk";
        $entityRel->$method($entity);
        foreach ($columnByTable["taxon_identification"] as $ColCsv) {
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($dataColCsv === '') {
            $dataColCsv = NULL;
          }

          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            // control and standardization of field formats
            if ($ColCsv == 'taxon_identification.date_identification') {
              // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
              if (!is_null($dataColCsv)) {
                if (count(explode("/", $dataColCsv)) == 2) {
                  $dataColCsv = "01/" . $dataColCsv;
                }

                if (count(explode("/", $dataColCsv)) == 1) {
                  $dataColCsv = "01/01/" . $dataColCsv;
                }

                $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                if (!$eventDate) {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                } else {
                  $tabdate = explode("/", $dataColCsv);
                  if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                    $dataColCsv = new \DateTime($dataColCsv);
                  } else {
                    $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                    $dataColCsv = NULL;
                  }
                }
              }
            }
            // save the values ​​of the field
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($dataColCsv);
          }
          if ($flag_foreign) {
            $varfield = explode(".", strstr($field, '(', true))[1];
            $linker = explode('.', trim($foreign_content[0], "()"));
            $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
            $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
            if (!is_null($dataColCsv)) {
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                case "Voc":
                  if ($data[$ColCsv] == '') {
                    $foreign_record = NULL;
                  } else {
                    $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                  break;
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
              } else {
                $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                $entityRel->$method($foreign_record);
              }
            }
          }
        }
        $entityRel->setDateCre($DateImport);
        $entityRel->setDateMaj($DateImport);
        $entityRel->setUserCre($userId);
        $entityRel->setUserMaj($userId);
        $em->persist($entityRel);
      }

      # Record of TaxonCurator
      if (!is_null($entityEspeceIdentifie)) {
        foreach ($columnByTable["taxon_curator"] as $ColCsv) {
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($dataColCsv == '' || trim($dataColCsv) == '') {
            $dataColCsv = NULL;
          }

          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
          if ($flag_foreign && !is_null($dataColCsv)) {
            foreach ($tab_foreign_field as $val_foreign_field) {
              $val_foreign_field = trim($val_foreign_field);
              $entityRel = new \App\Entity\TaxonCurator();
              $method = "setTaxonIdentificationFk";
              $entityRel->$method($entityEspeceIdentifie);
              if (!is_null($val_foreign_field) && $val_foreign_field != '') {
                //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                $varfield_parent = strstr($varfield, 'Voc', true);
                if (!$varfield_parent) {
                  $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
                } else {
                  $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
                }
                if ($foreign_record === NULL) {
                  switch ($foreign_table) {
                  case "Voc":
                    if ($data[$ColCsv] == '') {
                      $foreign_record = NULL;
                    } else {
                      $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                    }
                    break;
                  default:
                    $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                } else {
                  $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                  $entityRel->$method($foreign_record);
                }
              }
              $entityRel->setDateCre($DateImport);
              $entityRel->setDateMaj($DateImport);
              $entityRel->setUserCre($userId);
              $entityRel->setUserMaj($userId);
              $em->persist($entityRel);
            }
          }
        }
      }
    }
    # FLUSH
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataStore($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is store
   */
  public function importCSVDataStore($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Store();
      if (array_key_exists("store", $columnByTable)) {
        foreach ($columnByTable["store"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            if ($field == 'store.codeBoite') {
              $record_entity = $em->getRepository("App:Store")->findOneBy(array("codeBoite" => $dataColCsv));
              if ($record_entity !== NULL) {
                $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            }
            if ($dataColCsv === '') {
              $dataColCsv = NULL;
            }
            // if there is no value, initialize the value to NULL
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($dataColCsv); // save the values ​​of the field
          }
          if ($flag_foreign) {
            $varfield = explode(".", strstr($field, '(', true))[1];
            $linker = explode('.', trim($foreign_content[0], "()"));
            $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
            $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " parent=" . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataLotMateriel($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is internal_biological_material
   */
  public function importCSVDataLotMateriel($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // traitement ligne par ligne du fichier csv
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_person = array();
    $comment = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;

      #
      $entity = new \App\Entity\InternalLot();
      //
      foreach ($columnByTable["lot_materiel"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'lot_materiel.code') {
            $record_entity = $em->getRepository("App:InternalLot")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'lot_materiel.date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'lot_materiel.status') {
            if ($dataColCsv != '') {
              if ($dataColCsv == 'OUI' || $dataColCsv == 'YES' || $dataColCsv == '1') {
                $dataColCsv = 1;
              }
              if ($dataColCsv == 'NON' || $dataColCsv == 'NO' || $dataColCsv == '0') {
                $dataColCsv = 0;
              }
              if ($dataColCsv !== 1 && $dataColCsv !== 0) {
                $message .= $this->translator->trans('importfileService.ERROR bad data OUI-NON') . '<b> : ' . $ColCsv . "/ " . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Store":
              // la valeur NULL est permise
              break;
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of InternalLotProducer
      foreach ($columnByTable["producer"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalLotProducer();
            $method = "setInternalLotFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of InternalLotPublication
      foreach ($columnByTable["publication"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalLotPublication();
            $method = "setInternalLotFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of InternalLotContent
      foreach ($columnByTable["content"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($ColCsv == 'content.comment') {
          $comment = $dataColCsv;
        }

        if ($ColCsv == 'content.specimen_count+specimen_type_voc_fk(voc.code)') {
          $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalLotContent();
            $method = "setInternalLotFk";
            $entityRel->$method($entity);
            $entityRel->setComment($comment);
            // We split the information into two variable $specimen_count & $specimen_type
            $specimen_count = (int) preg_replace('/[^0-9]/', '', $val_foreign_field);
            $specimen_type = preg_replace('/[0-9]/', '', $val_foreign_field);
            $specimen_type = trim($specimen_type);
            if ($specimen_count == 0) {
              $specimen_count = NULL;
            }

            $entityRel->setSpecimentCount($specimen_count);
            $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $specimen_type, "parent" => 'typeIndividu'));
            if ($foreign_record === NULL) {
              switch ("Voc") {
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $specimen_type . '</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $entityRel->setSpecimenTypeVocFk($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of TaxonIdentification
      $entityRel = new \App\Entity\TaxonIdentification();
      $entityEspeceIdentifie = $entityRel;
      $method = "setInternalLotFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["taxon_identification"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'taxon_identification.date_identification') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign) {
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $val_foreign_field = trim($dataColCsv);
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($foreign_record);
          }
        }
      }
      $entityRel->setDateCre($DateImport);
      $entityRel->setDateMaj($DateImport);
      $entityRel->setUserCre($userId);
      $entityRel->setUserMaj($userId);
      $em->persist($entityRel);

      # Record of TaxonCurator
      foreach ($columnByTable["taxon_curator"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\TaxonCurator();
            $method = "setTaxonIdentificationFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataMotuDatasetFile($fichier, ,\App\Entity\MotuDataset $motu_dataset, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is MOTU
   */
  public function importCSVDataMotuDatasetFile($fichier, \App\Entity\MotuDataset $motu_dataset, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataMotuDataset = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataMotuDataset); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');

    $entity = $motu_dataset;
    foreach ($csvDataMotuDataset as $l2 => $data2) { // 1- Line-to-line data processing ($ l)
      $flagSeq = 0;
      $flagSeqExt = 0;
      $record_entity_sqc_ass = $em->getRepository("App:InternalSequence")->findOneBy(array("code" => $data2["code_seq_ass"]));
      if ($record_entity_sqc_ass !== NULL) {
        $flagSeq = 1;
        $entityRel = new \App\Entity\MotuDelimitation();
        $method = "setMotuDatasetFk";
        $entityRel->$method($entity);
        $method = "setInternalSequenceFk";
        $entityRel->$method($record_entity_sqc_ass);
        $method = "setMotuNumber";
        $entityRel->$method($data2["motu_number"]);
        $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
        if ($foreign_record === NULL) {
          $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
        }

        $method = "setMethodVocFk";
        $entityRel->$method($foreign_record);
      }
      $record_entity_sqc_ass_ext = $em->getRepository("App:ExternalSequence")->findOneBy(array("code" => $data2["code_seq_ass"]));
      if ($record_entity_sqc_ass_ext !== NULL) {
        $flagSeqExt = 1;
        $entityRel = new \App\Entity\MotuDelimitation();
        $method = "setMotuDatasetFk";
        $entityRel->$method($entity);
        $method = "setExternalSequenceFk";
        $entityRel->$method($record_entity_sqc_ass_ext);
        $method = "setMotuNumber";
        $entityRel->$method($data2["motu_number"]);
        $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
        if ($foreign_record === NULL) {
          $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
        }

        $method = "setMethodVocFk";
        $entityRel->$method($foreign_record);
      }

      $entityRel->setDateCre($DateImport);
      $entityRel->setDateMaj($DateImport);
      $entityRel->setUserCre($userId);
      $entityRel->setUserMaj($userId);
      $em->persist($entityRel);
      if (!$flagSeq && !$flagSeqExt) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data2["code_seq_ass"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data2) . "<br>";
      }

      if ($flagSeq && $flagSeqExt) {
        $message .= $this->translator->trans('importfileService.ERROR duplicate code sqc sqcext') . '<b> : ' . $data2["code_seq_ass"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data2) . "<br>";
      }

    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataMotuDataset) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataMotuDataset($fichier, $fichier_motu)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import IS NOT YET SUPPORTED in V1.1
   */
  public function importCSVDataMotuDataset($fichier, $fichier_motu) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $csvDataMotuDataset = $importFileCsvService->readCSV($fichier_motu);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');

    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Enregistrement des données de motu
      $entity = new \App\Entity\MotuDataset();
      //
      foreach ($columnByTable["motu_dataset"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($dataColCsv === '') {
          $dataColCsv = NULL;
        }

        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // we memorize the name of the file to treat it later
          if ($ColCsv == 'motu_dataset.filename') {
            $filename = $dataColCsv;
          }
          // we adapt the date format of the column motu_dataset.date
          if ($ColCsv == 'motu_dataset.date') {
            if ($dataColCsv != '') {
              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          if (!is_null($dataColCsv)) {
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . "]</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entity->$method($foreign_record);
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of MotuDelimiter
      foreach ($columnByTable["motu_delimiter"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\MotuDelimiter();
            $method = "setMotuDatasetFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Process of file motus
      if (array_key_exists("code_seq_ass", $csvDataMotuDataset[0]) && array_key_exists("motu_number", $csvDataMotuDataset[0]) && array_key_exists("code_methode_motu", $csvDataMotuDataset[0])) {
        foreach ($csvDataMotuDataset as $l2 => $data2) { // 1- Line-to-line data processing ($ l)
          $flagSeq = 0;
          $flagSeqExt = 0;
          $record_entity_sqc_ass = $em->getRepository("App:InternalSequence")->findOneBy(array("code" => $data2["code_seq_ass"]));
          if ($record_entity_sqc_ass !== NULL) {
            $flagSeq = 1;
            $entityRel = new \App\Entity\MotuDelimitation();
            $method = "setMotuDatasetFk";
            $entityRel->$method($entity);
            $method = "setInternalSequenceFk";
            $entityRel->$method($record_entity_sqc_ass);
            $method = "setMotuNumber";
            $entityRel->$method($data2["motu_number"]);
            $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
            if ($foreign_record === NULL) {
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
            }

            $method = "setMethodVocFk";
            $entityRel->$method($foreign_record);
          }
          $record_entity_sqc_ass_ext = $em->getRepository("App:ExternalSequence")->findOneBy(array("code" => $data2["code_seq_ass"]));
          if ($record_entity_sqc_ass_ext !== NULL) {
            $flagSeqExt = 1;
            $entityRel = new \App\Entity\MotuDelimitation();
            $method = "setMotuDatasetFk";
            $entityRel->$method($entity);
            $method = "setExternalSequenceFk";
            $entityRel->$method($record_entity_sqc_ass_ext);
            $method = "setMotuNumber";
            $entityRel->$method($data2["motu_number"]);
            $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
            if ($foreign_record === NULL) {
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b> INCONNU <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
            }

            $method = "setMethodVocFk";
            $entityRel->$method($foreign_record);
          }

          $entityRel->setDateCre($DateImport);
          $entityRel->setDateMaj($DateImport);
          $entityRel->setUserCre($userId);
          $entityRel->setUserMaj($userId);
          $em->persist($entityRel);
          if (!$flagSeq && !$flagSeqExt) {
            $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data2["code_seq_ass"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data2) . "<br>";
          }

          if ($flagSeq && $flagSeqExt) {
            $message .= $this->translator->trans('importfileService.ERROR duplicate code sqc sqcext') . '<b> : ' . $data2["code_seq_ass"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data2) . "<br>";
          }

        }
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataMotuDataset) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataInstitution($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is institution
   */
  public function importCSVDataInstitution($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Institution();
      //
      if (array_key_exists("institution", $columnByTable)) {
        foreach ($columnByTable["institution"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($dataColCsv == '' || trim($dataColCsv) == '') {
            $dataColCsv = NULL;
          }

          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            if ($ColCsv == 'institution.name') {
              $record_entity = $em->getRepository("App:Institution")->findOneBy(array("name" => $dataColCsv));
              if ($record_entity !== NULL) {
                $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            }
            // control and standardization of field formats
            // save the values ​​of the field
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($dataColCsv);
          }
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    # FLUSH
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataPays(array $csvData)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is country
   */
  public function importCSVDataPays($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_commune = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Country();
      if (array_key_exists("country", $columnByTable)) {
        foreach ($columnByTable["country"] as $ColCsv) {
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $varfield = explode(".", $field)[1];
          if ($field == 'country.codePays') {
            $record_country = $em->getRepository("App:Country")->findOneBy(array("codePays" => $dataColCsv));
            if ($record_country !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 1) . ": " . join(';', $data) . "<br>";
            }
          }
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv); // save the values ​​of the field
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataExternalSequence($fichier, $userId = null)
   * $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is external_sequence
   */
  public function importCSVDataExternalSequence($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_person = array();
    $comment = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\ExternalSequence();
      //
      foreach ($columnByTable["external_sequence"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'external_sequence.code') {
            $record_entity = $em->getRepository("App:ExternalSequence")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'external_sequence.date_creation') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of seq_ass_ext_est_realise_par
      foreach ($columnByTable["assembler"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\ExternalSequenceAssembler;
            $method = "setExternalSequenceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Enregistrement de ExternalSequencePublication
      foreach ($columnByTable["external_sequence_publication"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\ExternalSequencePublication();
            $method = "setExternalSequenceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of TaxonIdentification
      $entityRel = new \App\Entity\TaxonIdentification();
      $entityEspeceIdentifie = $entityRel;
      $method = "setExternalSequenceFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["taxon_identification"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'taxon_identification.date_identification') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign) {
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $val_foreign_field = trim($dataColCsv);
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($foreign_record);
          }
        }
      }
      $entityRel->setDateCre($DateImport);
      $entityRel->setDateMaj($DateImport);
      $entityRel->setUserCre($userId);
      $entityRel->setUserMaj($userId);
      $em->persist($entityRel);

      # Record of TaxonCurator
      foreach ($columnByTable["taxon_curator"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\TaxonCurator();
            $method = "setTaxonIdentificationFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataExternalLot($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is external_biological_external
   */
  public function importCSVDataExternalLot($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_person = array();
    $comment = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\ExternalLot();
      //
      foreach ($columnByTable["external_lot"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'external_lot.code_external_lot') {
            $record_entity = $em->getRepository("App:ExternalLot")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'external_lot.date_creation_external_lot') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of producer
      foreach ($columnByTable["producer"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\ExternalLotProducer;
            $method = "setExternalLotFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Enregistrement de ExternalLotPublication
      foreach ($columnByTable["publication"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\ExternalLotPublication();
            $method = "setExternalLotFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of TaxonIdentification
      $entityRel = new \App\Entity\TaxonIdentification();
      $entityEspeceIdentifie = $entityRel;
      $method = "setExternalLotFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["taxon_identification"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'taxon_identification.date_identification') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign) {
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $val_foreign_field = trim($dataColCsv);
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($foreign_record);
          }
        }
      }
      $entityRel->setDateCre($DateImport);
      $entityRel->setDateMaj($DateImport);
      $entityRel->setUserCre($userId);
      $entityRel->setUserMaj($userId);
      $em->persist($entityRel);

      # Record of TaxonCurator
      foreach ($columnByTable["taxon_curator"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\TaxonCurator();
            $method = "setTaxonIdentificationFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataSite($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is site
   */
  public function importCSVDataSite($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_commune = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Site();
      foreach ($columnByTable["site"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($field == 'site.code') { // On teste pour savoir si le code a déja été créé.
            $record_station = $em->getRepository("App:Site")->findOneBy(array("code" => $dataColCsv));
            if ($record_station !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 1) . ": " . join(';', $data) . "<br>";
            }
          }
          // we adapt the format of long and lat
          if ($field == 'site.latDegDec' || $field == 'site.longDegDec') {
            $dataColCsv = ($dataColCsv != '') ? floatval(str_replace(",", ".", $dataColCsv)) : null;
          }
          if ($field == 'site.altitudeM') {
            $dataColCsv = ($dataColCsv != '') ? intval(str_replace(",", ".", $dataColCsv)) : null;
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) {
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case 'Commune':
              if ($dataColCsv != '') {
                $CodeCommune = $dataColCsv;
                if (array_key_exists($CodeCommune, $list_new_commune)) {
                  $municipality = $list_new_commune[$CodeCommune];
                } else { // if CodeCommune is null create a new commune with a codeCommune as Name|Region|Nom_Pays and field site_name = "Name" and municipality_name = "Region"
                  $municipality = new \App\Entity\Municipality();
                  $municipality->setCodeCommune($CodeCommune);
                  $list_field_commune = explode("|", $dataColCsv);
                  $municipality->setName(str_replace("_", " ", $list_field_commune[0]));
                  $municipality->setRegion(str_replace("_", " ", $list_field_commune[1]));
                  $municipality->setDateCre($DateImport);
                  $municipality->setDateMaj($DateImport);
                  $municipality->setUserCre($userId);
                  $municipality->setUserMaj($userId);
                  $country_fk = $em->getRepository("App:Country")->findOneBy(array("codePays" => $list_field_commune[2]));
                  if ($country_fk === NULL) {
                    $message .= $this->translator->trans('importfileService.ERROR bad code') . ' : ' . $list_field_commune[2] . '</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                  $municipality->setCountryFk($country_fk);
                  $em->persist($municipality);
                  $list_new_commune[$CodeCommune] = $municipality; // we keep in memory the communes created
                }
                $foreign_fieldName = $foreign_table . "Fk";
                $method = $importFileCsvService->TransformNameForSymfony($foreign_fieldName, 'set');
                $entity->$method($municipality);
              }
              break;
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . $foreign_table . "-" . $foreign_field . " <b>" . $dataColCsv . '</b> <br> ligne ' . (string) ($l + 1) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
            if ($foreign_table == 'Country') { // we memorize information about the country
              $code_pays = $foreign_record->getCodePays();
              $country_record = $foreign_record;
            }
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);
    }
    // A FAIRE : ajouter les champ municipality.name +municipality.region

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataInternalSequence($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is internal_sequence
   */
  public function importCSVDataInternalSequence($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_person = array();
    $comment = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\InternalSequence();
      //
      foreach ($columnByTable["internal_sequence"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'internal_sequence.code') {
            $record_entity = $em->getRepository("App:InternalSequence")->findOneBy(array("code" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $dataColCsv . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'internal_sequence.creation_date') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
        }
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($foreign_record);
          }
        }
      }
      $entity->setDateCre($DateImport);
      $entity->setDateMaj($DateImport);
      $entity->setUserCre($userId);
      $entity->setUserMaj($userId);
      $em->persist($entity);

      # Record of assembler
      foreach ($columnByTable["assembler"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalSequenceAssembler();
            $method = "setInternalSequenceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Enregistrement de InternalSequencePublication
      foreach ($columnByTable["publication"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalSequencePublication();
            $method = "setInternalSequenceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Enregistrement de InternalSequenceAssembly    (liaison aux chromatogramme)
      foreach ($columnByTable["assembly"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\InternalSequenceAssembly();
            $method = "setInternalSequenceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of TaxonIdentification
      $entityRel = new \App\Entity\TaxonIdentification();
      $entityEspeceIdentifie = $entityRel;
      $method = "setInternalSequenceFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["taxon_identification"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'taxon_identification.date_identification') {
            // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
            if ($dataColCsv != '') {
              if (count(explode("/", $dataColCsv)) == 2) {
                $dataColCsv = "01/" . $dataColCsv;
              }

              if (count(explode("/", $dataColCsv)) == 1) {
                $dataColCsv = "01/01/" . $dataColCsv;
              }

              $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
              if (!$eventDate) {
                $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                $dataColCsv = NULL;
              } else {
                $tabdate = explode("/", $dataColCsv);
                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                  $dataColCsv = date_format($eventDate, 'Y-m-d');
                  $dataColCsv = new \DateTime($dataColCsv);
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR bad date format') . '<b> : ' . $data[$ColCsv] . "-" . $dataColCsv . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  $dataColCsv = NULL;
                }
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign) {
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          $val_foreign_field = trim($dataColCsv);
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
            case "Voc":
              if ($data[$ColCsv] == '') {
                $foreign_record = NULL;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
              break;
            default:
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          } else {
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entityRel->$method($foreign_record);
          }
        }
      }
      $entityRel->setDateCre($DateImport);
      $entityRel->setDateMaj($DateImport);
      $entityRel->setUserCre($userId);
      $entityRel->setUserMaj($userId);
      $em->persist($entityRel);

      # Record of TaxonCurator
      foreach ($columnByTable["taxon_curator"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $varfield = explode(".", strstr($field, '(', true))[1];
        $linker = explode('.', trim($foreign_content[0], "()"));
        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
        $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
        if ($flag_foreign && trim($dataColCsv) != '') {
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\TaxonCurator();
            $method = "setTaxonIdentificationFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
            }
            if ($foreign_record === NULL) {
              switch ($foreign_table) {
              case "Voc":
                if ($data[$ColCsv] == '') {
                  $foreign_record = NULL;
                } else {
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
                break;
              default:
                $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $val_foreign_field . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
              $entityRel->$method($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataTaxon($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is taxon
   */
  public function importCSVDataTaxon($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_commune = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Taxon();
      if (array_key_exists("taxon", $columnByTable)) {
        foreach ($columnByTable["taxon"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $varfield = explode(".", $field)[1];
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($ColCsv == 'taxon.taxname') { // On teste pour savoir si le taxname a déja été créé.
            $record_entity = $em->getRepository("App:Taxon")->findOneBy(array("taxname" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          if ($ColCsv == 'taxon.validity') {
            if ($dataColCsv != '') {
              if ($dataColCsv == 'YES' || $dataColCsv == 'NO') {
                $dataColCsv = ($dataColCsv == 'YES') ? 1 : 0;
              } else {
                $message .= $this->translator->trans('importfileService.ERROR bad data YES-NO') . '<b> : ' . $ColCsv . " / " . $data[$ColCsv] . "</b>  <br> ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            }
          }
          if ($dataColCsv === '') {
            $dataColCsv = NULL;
          }
          // if there is no value, initialize the value to NULL
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv); // save the values ​​of the field
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataVoc($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is vocabulary
   */
  public function importCSVDataVoc($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_commune = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Voc();
      if (array_key_exists("voc", $columnByTable)) {
        foreach ($columnByTable["voc"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $varfield = explode(".", $field)[1];
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          // On teste pour savoir si le code_voc n existe pas deja pour ce parent
          if ($ColCsv == 'voc.parent') {
            $record_voc = $em->getRepository("App:Voc")->findOneBy(array("parent" => $dataColCsv, "code" => $code));
            if ($record_voc !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entity->$method($dataColCsv);
          if ($ColCsv == 'voc.code') {
            $code = $dataColCsv;
          }
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataPerson($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is person
   */
  public function importCSVDataPerson($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Enregistrement des données de Person
      $entity = new \App\Entity\Person();
      //
      if (array_key_exists("person", $columnByTable)) {
        foreach ($columnByTable["person"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($dataColCsv == '' || trim($dataColCsv) == '') {
            $dataColCsv = NULL;
          }

          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            if ($ColCsv == 'person.name') {
              $record_entity = $em->getRepository("App:Person")->findOneBy(array("name" => $dataColCsv));
              if ($record_entity !== NULL) {
                $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            }
            // control and standardization of field formats
            // save the values ​​of the field
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($dataColCsv);
          }
          if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
            $varfield = explode(".", strstr($field, '(', true))[1];
            $linker = explode('.', trim($foreign_content[0], "()"));
            $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
            $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
            if (!is_null($dataColCsv)) {
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                case 'institution':
                  // la valeur NULL est permise
                  break;
                case "Voc":
                  if ($data[$ColCsv] == '') {
                    $foreign_record = NULL;
                  } else {
                    $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                  break;
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
              } else {
                $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                $entity->$method($foreign_record);
              }
            }
          }
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    # FLUSH
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }

  /**
   *  importCSVDataCommune($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is municipality
   */
  public function importCSVDataCommune($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      # Enregistrement des données de Person
      $entity = new \App\Entity\Municipality();
      //
      if (array_key_exists("commune", $columnByTable)) {
        foreach ($columnByTable["commune"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($dataColCsv == '' || trim($dataColCsv) == '') {
            $dataColCsv = NULL;
          }

          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            if ($ColCsv == 'municipality.code_commune') {
              $record_entity = $em->getRepository("App:Municipality")->findOneBy(array("codeCommune" => $dataColCsv));
              if ($record_entity !== NULL) {
                $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            }
            // control and standardization of field formats
            // save the values ​​of the field
            $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
            $entity->$method($dataColCsv);
          }
          if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name)
            $varfield = explode(".", strstr($field, '(', true))[1];
            $linker = explode('.', trim($foreign_content[0], "()"));
            $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
            $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
            if (!is_null($dataColCsv)) {
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App:" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                case "Voc":
                  if ($data[$ColCsv] == '') {
                    $foreign_record = NULL;
                  } else {
                    $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $foreign_table . "." . $foreign_field . "." . $varfield_parent . " <b>[" . $data[$ColCsv] . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                  }
                  break;
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $field . ' : ' . $foreign_table . "." . $foreign_field . " <b>[" . $dataColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                }
              } else {
                $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
                $entity->$method($foreign_record);
              }
            } else {
              // cas des valeurs NULL
              switch ($foreign_table) {
              case 'Country':
                $message .= $this->translator->trans('importfileService.ERROR NULL value') . ' : ' . $field . " <b>[" . $ColCsv . ']</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                break;
              }
            }
          }
        }
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
      } else {
        return ($this->translator->trans('importfileService.ERROR bad columns in CSV'));
        exit;
      }
    }
    # FLUSH
    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Doctrine\DBAL\DBALException $e) {
        $exception_message = addslashes(
          html_entity_decode(strval($e), ENT_QUOTES, 'UTF-8')
        );
        $message = $this->translator->trans('importfileService.Problem of FLUSH') . ' : </br>' . explode("\n", $exception_message)[0];
        if (count(explode("\n", $exception_message)) > 1) {
          $message .= ' : </br>' . explode("\n", $exception_message)[1];
        }

        return $message;
      }
    } else {
      return $info . '</br>' . $message;
    }
  }
}
