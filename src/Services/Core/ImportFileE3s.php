<?php

namespace App\Services\Core;

use App\Entity\Motu;
use App\Services\Core\ImportFileCsv;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Adn;
use App\Entity\Boite;
use App\Entity\IndividuLame;
use App\Entity\LotMateriel;
use App\Entity\Source;
use App\Entity\LotEstPublieDans;
use App\Entity\SequenceAssemblee;
use App\Entity\SqcEstPublieDans;
use App\Entity\Pcr;
use App\Entity\Chromatogramme;
use App\Entity\Programme;
use App\Entity\Collecte;
use App\Entity\Individu;
use App\Entity\Voc;
use App\Entity\SequenceAssembleeExt;
use App\Entity\Etablissement;
use App\Entity\Pays;
use App\Entity\LotMaterielExt;
use App\Entity\Station;
use App\Entity\ReferentielTaxon;
use App\Entity\Personne;
use App\Entity\Commune;

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
   *  importCSVDataAdnDeplace($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA_move
   */
  public function importCSVDataAdnDeplace($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataAdnRange = $importFileCsvService->readCSV($fichier);
    //$columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataAdnRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_adn = $em->getRepository(Adn::class)->createQueryBuilder('adn')
        ->where('adn.codeAdn  LIKE :code_adn')
        ->setParameter('code_adn', $data["code_adn"])
        ->getQuery()
        ->getResult();
      $flagAdn = count($query_adn);
      if ($flagAdn == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_adn"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagAdn && $flagBoite) {
        if ($flagBoiteAffecte) {
          $query_adn[0]->setBoiteFk($query_boite[0]);
          $query_adn[0]->setDateMaj($DateImport);
          $query_adn[0]->setUserMaj($userId);
          $em->persist($query_adn[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        } else {
          $query_adn[0]->setBoiteFk(null);
          $query_adn[0]->setDateMaj($DateImport);
          $query_adn[0]->setUserMaj($userId);
          $em->persist($query_adn[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataAdnRange) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataAdnRange($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA_store
   */
  public function importCSVDataAdnRange($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataAdnRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataAdnRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_adn = $em->getRepository(Adn::class)->createQueryBuilder('adn')
        ->where('adn.codeAdn  LIKE :code_adn')
        ->setParameter('code_adn', $data["code_adn"])
        ->getQuery()
        ->getResult();
      $flagAdn = count($query_adn);
      if ($flagAdn == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_adn"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no box code") . '<b> : ' . $data["code_lame_coll"] . " </b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagAdn && $flagBoite && $flagBoiteAffecte) {
        if ($query_adn[0]->getBoiteFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR adn already store') . '<b> : ' . $data["code_adn"] . '</b> / ' . $query_adn[0]->getBoiteFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_adn[0]->setBoiteFk($query_boite[0]);
          $query_adn[0]->setDateMaj($DateImport);
          $query_adn[0]->setUserMaj($userId);
          $em->persist($query_adn[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataAdnRange) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataIndividuLameDeplace($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is slide_move
   */
  public function importCSVDataIndividuLameDeplace($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataIndividuLamelRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lame = $em->getRepository(IndividuLame::class)->createQueryBuilder('lame')
        ->where('lame.codeLameColl  LIKE :code_lame_coll')
        ->setParameter('code_lame_coll', $data["code_lame_coll"])
        ->getQuery()
        ->getResult();
      $flagLame = count($query_lame);
      if ($flagLame == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_lame_coll"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLame && $flagBoite) {
        if ($flagBoiteAffecte) {
          $query_lame[0]->setBoiteFk($query_boite[0]);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        } else {
          $query_lame[0]->setBoiteFk(null);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataIndividuLamelRange) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataIndividuLameRange($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is slide_store
   */
  public function importCSVDataIndividuLameRange($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    foreach ($csvDataIndividuLamelRange as $l => $data) { // 1- Line-to-line data processing ($ l)
      $query_lame = $em->getRepository(IndividuLame::class)->createQueryBuilder('lame')
        ->where('lame.codeLameColl  LIKE :code_lame_coll')
        ->setParameter('code_lame_coll', $data["code_lame_coll"])
        ->getQuery()
        ->getResult();
      $flagLame = count($query_lame);
      if ($flagLame == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_lame_coll"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no box code") . '<b> : ' . $data["code_lame_coll"] . " </b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLame && $flagBoite && $flagBoiteAffecte) {
        if ($query_lame[0]->getBoiteFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR slide already store') . '<b> : ' . $data["code_lame_coll"] . '</b> / ' . $query_lame[0]->getBoiteFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_lame[0]->setBoiteFk($query_boite[0]);
          $query_lame[0]->setDateMaj($DateImport);
          $query_lame[0]->setUserMaj($userId);
          $em->persist($query_lame[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataIndividuLamelRange) . '</br>' . $info;
      } catch (\Exception $e) {
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
      $query_lot = $em->getRepository(LotMateriel::class)->createQueryBuilder('lot')
        ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
        ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
        ->getQuery()
        ->getResult();
      $flagLot = count($query_lot);
      if ($flagLot == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_lot_materiel"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLot && $flagBoite) {
        if ($flagBoiteAffecte) {
          $query_lot[0]->setBoiteFk($query_boite[0]);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        } else {
          $query_lot[0]->setBoiteFk(null);
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
      } catch (\Exception $e) {
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
      $query_lot = $em->getRepository(LotMateriel::class)->createQueryBuilder('lot')
        ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
        ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
        ->getQuery()
        ->getResult();
      $flagLot = count($query_lot);
      if ($flagLot == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_lot_materiel"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      $flagBoite = 1;
      $flagBoiteAffecte = 0;
      if ($data["code_boite"] != null || $data["code_boite"] != '') {
        $flagBoiteAffecte = 1;
        $query_boite = $em->getRepository(Boite::class)->createQueryBuilder('boite')
          ->where('boite.codeBoite LIKE :code_boite')
          ->setParameter('code_boite', $data["code_boite"])
          ->getQuery()
          ->getResult();
        $flagBoite = count($query_boite);
      }
      if ($flagBoiteAffecte == 0) {
        $message .= $this->translator->trans("importfileService.ERROR no box code for material") . '<b> : ' . $data["code_lot_materiel"] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagBoiteAffecte && $flagBoite == 0) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_boite"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
      }

      if ($flagLot && $flagBoite && $flagBoiteAffecte) {
        if ($query_lot[0]->getBoiteFk() != null) {
          $message .= $this->translator->trans('importfileService.ERROR lot already store') . '<b> : ' . $data["code_lot_materiel"] . '</b> / ' . $query_lot[0]->getBoiteFk()->getCodeBoite() . ' <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $query_lot[0]->setBoiteFk($query_boite[0]);
          $query_lot[0]->setDateMaj($DateImport);
          $query_lot[0]->setUserMaj($userId);
          $em->persist($query_lot[0]);
          $query_boite[0]->setDateMaj($DateImport);
          $query_boite[0]->setUserMaj($userId);
          $em->persist($query_boite[0]);
        }
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataLotMaterielRange) . '</br>' . $info;
      } catch (\Exception $e) {
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
      $query_lot = $em->getRepository(LotMateriel::class)->createQueryBuilder('lot')
        ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
        ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
        ->getQuery()
        ->getResult();
      $query_source = $em->getRepository(Source::class)->createQueryBuilder('source')
        ->where('source.codeSource LIKE :code_source')
        ->setParameter('code_source', $data["source.code_source"])
        ->getQuery()
        ->getResult();
      if (count($query_lot) == 0 || count($query_source) == 0) {
        if (count($query_lot) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_lot_materiel"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

        if (count($query_source) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["source.code_source"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
      } else {
        $query_lepd = $em->getRepository(LotEstPublieDans::class)->createQueryBuilder('lepd')
          ->where('lepd.lotMaterielFk = :id_lot')
          ->setParameter('id_lot', $query_lot[0]->getId())
          ->andwhere('source.codeSource = :code_source')
          ->setParameter('code_source', $data["source.code_source"])
          ->leftJoin('App\\Entity\\Source', 'source', 'WITH', 'lepd.sourceFk = source.id')
          ->getQuery()
          ->getResult();
        if (count($query_lepd) != 0) {
          $message .= $this->translator->trans('importfileService.ERROR lot already publish') . '<b> : ' . $data["source.code_source"] . ' / ' . $data["code_lot_materiel"] . ' </b><br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        } else {
          $entityRel = new \App\Entity\LotEstPublieDans();
          $method = "setSourceFk";
          $entityRel->$method($query_source[0]);
          $method = "setLotMaterielFk";
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
      } catch (\Exception $e) {
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
      $query_sa = $em->getRepository(SequenceAssemblee::class)->createQueryBuilder('sa')
        ->where('sa.codeSqcAss LIKE :code_sqc_ass')
        ->setParameter('code_sqc_ass', $data["code_sqc_ass"])
        ->getQuery()
        ->getResult();
      $query_source = $em->getRepository(Source::class)->createQueryBuilder('source')
        ->where('source.codeSource LIKE :code_source')
        ->setParameter('code_source', $data["source.code_source"])
        ->getQuery()
        ->getResult();
      if (count($query_sa) == 0 || count($query_source) == 0) {
        if (count($query_sa) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["code_sqc_ass"] . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }

        if (count($query_source) == 0) {
          $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data["source.code_source"] . '</b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
      } else {
        $query_lepd = $em->getRepository(SqcEstPublieDans::class)->createQueryBuilder('sepd')
          ->where('sepd.sequenceAssembleeFk = :id_sa')
          ->setParameter('id_sa', $query_sa[0]->getId())
          ->getQuery()
          ->getResult();
        if (count($query_lepd) != 0 || $query_sa[0]->getAccessionNumber() != '') {
          if (count($query_lepd) != 0) {
            $message .= $this->translator->trans('importfileService.ERROR sqc already publish') . '<b> : ' . $data["source.code_source"] . ' / ' . $data["code_sqc_ass"] . ' </b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }

          if ($query_sa[0]->getAccessionNumber() != '') {
            $message .= $this->translator->trans('importfileService.ERROR assession number already assign') . '<b> : ' . $query_sa[0]->getAccessionNumber() . ' / ' . $data["code_sqc_ass"] . ' </b> <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
        } else {
          $method = "setAccessionNumber";
          $query_sa[0]->$method($data["accession_number"]);
          $query_sa[0]->setDateMaj($DateImport);
          $query_sa[0]->setUserMaj($userId);
          $em->persist($query_sa[0]);
          $entityRel = new \App\Entity\SqcEstPublieDans();
          $method = "setSourceFk";
          $entityRel->$method($query_source[0]);
          $method = "setSequenceAssembleeFk";
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
      } catch (\Exception $e) {
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
            $record_entity = $em->getRepository(Source::class)->findOneBy(array("codeSource" => $dataColCsv));
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of  SourceAEteIntegrePar
      foreach ($columnByTable["source_a_ete_integre_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\SourceAEteIntegrePar();
            $method = "setSourceFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
          if ($ColCsv == 'pcr.code_pcr') {
            $record_entity = $em->getRepository(Pcr::class)->findOneBy(array("codePcr" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'pcr.date_pcr') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
      if (array_key_exists($data['pcr.code_pcr'], $list_new_pcr)) {
        $flag_new_pcr = 0;
        $entity = $list_new_pcr[$data['pcr.code_pcr']];
      } else {
        $entity->setDateCre($DateImport);
        $entity->setDateMaj($DateImport);
        $entity->setUserCre($userId);
        $entity->setUserMaj($userId);
        $em->persist($entity);
        $list_new_pcr[$data['pcr.code_pcr']] = $entity;
      }

      # Record of PcrEstRealisePar
      if ($flag_new_pcr) {
        foreach ($columnByTable["pcr_est_realise_par"] as $ColCsv) {
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
              $entityRel = new \App\Entity\PcrEstRealisePar();
              $method = "setPcrFk";
              $entityRel->$method($entity);
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
              } else {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of chromatogramme
      $entityRel = new \App\Entity\Chromatogramme();
      $method = "setPcrFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["chromatogramme"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          if ($ColCsv == 'chromatogramme.code_chromato') { // On teste pour savoir si le chromatogramme.code_chromato a déja été créé.
            $record_entity = $em->getRepository(Chromatogramme::class)->findOneBy(array("codeChromato" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          // save the values ​​of the field
          $method = $importFileCsvService->TransformNameForSymfony($varfield, 'set');
          $entityRel->$method($dataColCsv);
        }
        if ($flag_foreign && $ColCsv != 'chromatogramme.pcr_fk(pcr.code_pcr)') { // case of a foreign key (where there are parentheses in the field name)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
          if ($ColCsv == 'pcr.code_pcr') {
            $record_entity = $em->getRepository(Pcr::class)->findOneBy(array("codePcr" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'pcr.date_pcr') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of PcrEstRealisePar
      foreach ($columnByTable["pcr_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\PcrEstRealisePar();
            $method = "setPcrFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
      $entity = new \App\Entity\Chromatogramme();
      foreach ($columnByTable["chromatogramme"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'chromatogramme.code_chromato') {
            $record_entity = $em->getRepository(Chromatogramme::class)->findOneBy(array("codeChromato" => $dataColCsv));
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataAdn($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is DNA
   */
  public function importCSVDataAdn($fichier, $userId = null) {
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
      $entity = new \App\Entity\Adn();
      //
      foreach ($columnByTable["adn"] as $ColCsv) {
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
          if ($ColCsv == 'adn.code_adn') {
            $record_entity = $em->getRepository(Adn::class)->findOneBy(array("codeAdn" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }

          // control and standardization of field formats
          if ($ColCsv == 'adn.concentration_ng_microlitre' && !is_null($dataColCsv)) {
            $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
          }
          // test of the date format
          if ($ColCsv == 'adn.date_adn') {
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of AdnEstRealisePar
      foreach ($columnByTable["adn_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\AdnEstRealisePar();
            $method = "setAdnFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataProgramme($fichier, $userId = null)
   * $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is program
   */
  public function importCSVDataProgramme($fichier, $userId = null) {
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
      $entity = new \App\Entity\Programme();
      if (array_key_exists("programme", $columnByTable)) {
        foreach ($columnByTable["programme"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          $varfield = explode(".", $field)[1];
          if ($field == 'programme.codeProgramme') {
            $record_entity = $em->getRepository(Programme::class)->findOneBy(array("codeProgramme" => $dataColCsv));
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
      } catch (\Exception $e) {
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
   *  importCSVDataCollecte($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is sampling
   */
  public function importCSVDataCollecte($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // appel du manager de Doctrine
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_personne = array();
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      $entity = new \App\Entity\Collecte();
      foreach ($columnByTable["collecte"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($field == 'collecte.codeCollecte') {
            $record_entity = $em->getRepository(Collecte::class)->findOneBy(array("codeCollecte" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $dataColCsv . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'collecte.conductivite_micro_sie_cm' || $ColCsv == 'collecte.temperature_c') {
            if ($dataColCsv != '') {
              $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
              if ($dataColCsv == '') {
                $message .= $this->translator->trans('importfileService.ERROR bad float format') . '<b> : ' . $data[$ColCsv] . "</b>  <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'collecte.duree_echantillonnage_mn') {
            if ($dataColCsv != '') {
              $dataColCsv = intval(str_replace(",", ".", $dataColCsv));
            } else {
              $dataColCsv = NULL;
            }
          }
          if ($ColCsv == 'collecte.a_faire') {
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
          if ($ColCsv == 'collecte.date_collecte') {
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
        if ($flag_foreign) { // case of a foreign key (where there are parentheses in the field name) ex. : station.commune(commune.nom_commune)
          $varfield = explode(".", strstr($field, '(', true))[1];
          $linker = explode('.', trim($foreign_content[0], "()"));
          $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0], 'table');
          $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1], 'field');
          //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
          $varfield_parent = strstr($varfield, 'Voc', true);
          // var_dump($varfield); var_dump($varfield_parent); var_dump($field);
          if (!$varfield_parent) {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of ACibler
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
            $entityRel = new \App\Entity\ACibler();
            $method = "setCollecteFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of APourFixateur
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
            $entityRel = new \App\Entity\APourFixateur();
            $method = "setCollecteFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of APourSamplingMethod
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
            $entityRel = new \App\Entity\APourSamplingMethod();
            $method = "setCollecteFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstEffectuePar
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
            $entityRel = new \App\Entity\EstEffectuePar();
            $method = "setCollecteFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstFinancePar
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
            $entityRel = new \App\Entity\EstFinancePar();
            $method = "setCollecteFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
      $entity = new \App\Entity\IndividuLame();
      //
      foreach ($columnByTable["individu_lame"] as $ColCsv) {
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
          if ($ColCsv == 'individu_lame.code_lame_coll') {
            $record_entity = $em->getRepository(IndividuLame::class)->findOneBy(array("codeLameColl" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'individu_lame.date_lame') {
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of IndividuLameEstRealisePar
      foreach ($columnByTable["individu_lame_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\IndividuLameEstRealisePar();
            $method = "setIndividuLameFk";
            $entityRel->$method($entity);
            if (!is_null($val_foreign_field) && $val_foreign_field != '') {
              //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
              $varfield_parent = strstr($varfield, 'Voc', true);
              if (!$varfield_parent) {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
              } else {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataIndividu($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is specimen
   */
  public function importCSVDataIndividu($fichier, $userId = null) {
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
      # Enregistrement des données de Individu
      $entity = new \App\Entity\Individu();
      //
      foreach ($columnByTable["individu"] as $ColCsv) {
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
          if ($ColCsv == 'individu.code_ind_biomol' && !is_null($dataColCsv)) {
            $record_entity = $em->getRepository(Individu::class)->findOneBy(array("codeIndBiomol" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          if ($ColCsv == 'code_ind_tri_morpho' && !is_null($dataColCsv)) {
            $record_entity = $em->getRepository(Individu::class)->findOneBy(array("codeIndTriMorpho" => $dataColCsv));
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of EspeceIdentifiee
      $key_taxname = array_keys($columnByTable["espece_identifiee"], "espece_identifiee.referentiel_taxon_fk(referentiel_taxon.taxname)")[0];
      // var_dump($data[$columnByTable["espece_identifiee"][$key_taxname]]);
      $entityEspeceIdentifie = NULL;
      if ($data[$columnByTable["espece_identifiee"][$key_taxname]] != '') {
        $entityRel = new \App\Entity\EspeceIdentifiee();
        $entityEspeceIdentifie = $entityRel;
        $method = "setIndividuFk";
        $entityRel->$method($entity);
        foreach ($columnByTable["espece_identifiee"] as $ColCsv) {
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
            if ($ColCsv == 'espece_identifiee.date_identification') {
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
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of EstIdentifiePar
      if (!is_null($entityEspeceIdentifie)) {
        foreach ($columnByTable["est_identifie_par"] as $ColCsv) {
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
              $entityRel = new \App\Entity\EstIdentifiePar();
              $method = "setEspeceIdentifieeFk";
              $entityRel->$method($entityEspeceIdentifie);
              if (!is_null($val_foreign_field) && $val_foreign_field != '') {
                //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                $varfield_parent = strstr($varfield, 'Voc', true);
                if (!$varfield_parent) {
                  $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
                } else {
                  $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataBoite($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is box
   */
  public function importCSVDataBoite($fichier, $userId = null) {
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
      $entity = new \App\Entity\Boite();
      if (array_key_exists("boite", $columnByTable)) {
        foreach ($columnByTable["boite"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if (!$flag_foreign) {
            $varfield = explode(".", $field)[1];
            if ($field == 'boite.codeBoite') {
              $record_entity = $em->getRepository(Boite::class)->findOneBy(array("codeBoite" => $dataColCsv));
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
    $list_new_personne = array();
    $commentaireCompoLotMateriel = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;

      #
      $entity = new \App\Entity\LotMateriel();
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
          if ($ColCsv == 'lot_materiel.code_lot_materiel') {
            $record_entity = $em->getRepository(LotMateriel::class)->findOneBy(array("codeLotMateriel" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'lot_materiel.date_lot_materiel') {
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
          if ($ColCsv == 'lot_materiel.a_faire') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
              case "Boite":
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

      # Record of LotMaterielEstRealisePar
      foreach ($columnByTable["lot_materiel_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\LotMaterielEstRealisePar();
            $method = "setLotMaterielFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of LotEstPublieDans
      foreach ($columnByTable["lot_est_publie_dans"] as $ColCsv) {
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
            $entityRel = new \App\Entity\LotEstPublieDans();
            $method = "setLotMaterielFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of CompositionLotMateriel
      foreach ($columnByTable["composition_lot_materiel"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if ($ColCsv == 'composition_lot_materiel.commentaire_compo_lot_materiel') {
          $commentaireCompoLotMateriel = $dataColCsv;
        }

        if ($ColCsv == 'composition_lot_materiel.nb_individus+type_individu_voc_fk(voc.code)') {
          $tab_foreign_field = explode("$", $dataColCsv); // We transform the contents of the field into a table
          foreach ($tab_foreign_field as $val_foreign_field) {
            $val_foreign_field = trim($val_foreign_field);
            $entityRel = new \App\Entity\CompositionLotMateriel();
            $method = "setLotMaterielFk";
            $entityRel->$method($entity);
            $entityRel->setCommentaireCompoLotMateriel($commentaireCompoLotMateriel);
            // We split the information into two variable $nb_individus & $type_individu
            $nb_individu = (int) preg_replace('/[^0-9]/', '', $val_foreign_field);
            $type_individu = preg_replace('/[0-9]/', '', $val_foreign_field);
            $type_individu = trim($type_individu);
            if ($nb_individu == 0) {
              $nb_individu = NULL;
            }

            $entityRel->setNbIndividus($nb_individu);
            $foreign_record = $em->getRepository(Voc::class)->findOneBy(array("code" => $type_individu, "parent" => 'typeIndividu'));
            if ($foreign_record === NULL) {
              switch ("Voc") {
                default:
                  $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $type_individu . '</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
              }
            } else {
              $entityRel->setTypeIndividuVocFk($foreign_record);
            }
            $entityRel->setDateCre($DateImport);
            $entityRel->setDateMaj($DateImport);
            $entityRel->setUserCre($userId);
            $entityRel->setUserMaj($userId);
            $em->persist($entityRel);
          }
        }
      }

      # Record of EspeceIdentifiee
      $entityRel = new \App\Entity\EspeceIdentifiee();
      $entityEspeceIdentifie = $entityRel;
      $method = "setLotMaterielFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["espece_identifiee"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'espece_identifiee.date_identification') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstIdentifiePar
      foreach ($columnByTable["est_identifie_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\EstIdentifiePar();
            $method = "setEspeceIdentifieeFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataMotuFile($fichier, ,\App\Entity\Motu $motu, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is MOTU
   */
  public function importCSVDataMotuFile($fichier, \App\Entity\Motu $motu, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvDataMotu = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvDataMotu); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    // line by line processing of the csv file
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');

    $entity = $motu;
    foreach ($csvDataMotu as $l2 => $data2) { // 1- Line-to-line data processing ($ l)
      $flagSeq = 0;
      $flagSeqExt = 0;
      $record_entity_sqc_ass = $em->getRepository(SequenceAssemblee::class)->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"]));
      if ($record_entity_sqc_ass !== NULL) {
        $flagSeq = 1;
        $entityRel = new \App\Entity\Assigne();
        $method = "setMotuFk";
        $entityRel->$method($entity);
        $method = "setSequenceAssembleeFk";
        $entityRel->$method($record_entity_sqc_ass);
        $method = "setNumMotu";
        $entityRel->$method($data2["num_motu"]);
        $foreign_record = $em->getRepository(Voc::class)->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
        if ($foreign_record === NULL) {
          $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
        }

        $method = "setMethodeMotuVocFk";
        $entityRel->$method($foreign_record);
        $entityRel->setDateCre($DateImport);
        $entityRel->setDateMaj($DateImport);
        $entityRel->setUserCre($userId);
        $entityRel->setUserMaj($userId);
        $em->persist($entityRel);
      }
      $record_entity_sqc_ass_ext = $em->getRepository(SequenceAssembleeExt::class)->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"]));
      if ($record_entity_sqc_ass_ext !== NULL) {
        $flagSeqExt = 1;
        $entityRel = new \App\Entity\Assigne();
        $method = "setMotuFk";
        $entityRel->$method($entity);
        $method = "setSequenceAssembleeExtFk";
        $entityRel->$method($record_entity_sqc_ass_ext);
        $method = "setNumMotu";
        $entityRel->$method($data2["num_motu"]);
        $foreign_record = $em->getRepository(Voc::class)->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
        if ($foreign_record === NULL) {
          $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
        }

        $method = "setMethodeMotuVocFk";
        $entityRel->$method($foreign_record);
        $entityRel->setDateCre($DateImport);
        $entityRel->setDateMaj($DateImport);
        $entityRel->setUserCre($userId);
        $entityRel->setUserMaj($userId);
        $em->persist($entityRel);
      }

      if (!$flagSeq && !$flagSeqExt) {
        $message .= $this->translator->trans('importfileService.ERROR bad code') . '<b> : ' . $data2["code_seq_ass"] . '</b> <br>ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
      }

      if ($flagSeq && $flagSeqExt) {
        $message .= $this->translator->trans('importfileService.ERROR duplicate code sqc sqcext') . '<b> : ' . $data2["code_seq_ass"] . '</b>  <br>ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
      }
    }

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataMotu) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataMotu($fichier, $fichier_motu)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import IS NOT YET SUPPORTED in V1.1
   */
  public function importCSVDataMotu($fichier, $fichier_motu) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $csvDataMotu = $importFileCsvService->readCSV($fichier_motu);
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
      $entity = new \App\Entity\Motu();
      //
      foreach ($columnByTable["motu"] as $ColCsv) {
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
          if ($ColCsv == 'motu.nom_fichier_csv') {
            $nom_fichier_csv = $dataColCsv;
          }
          // we adapt the date format of the column motu.date_motu
          if ($ColCsv == 'motu.date_motu') {
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
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of MotuEstGenerePar
      foreach ($columnByTable["motu_est_genere_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\MotuEstGenerePar();
            $method = "setMotuFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      if (array_key_exists("code_seq_ass", $csvDataMotu[0]) && array_key_exists("num_motu", $csvDataMotu[0]) && array_key_exists("code_methode_motu", $csvDataMotu[0])) {
        foreach ($csvDataMotu as $l2 => $data2) { // 1- Line-to-line data processing ($ l)
          $flagSeq = 0;
          $flagSeqExt = 0;
          $record_entity_sqc_ass = $em->getRepository(SequenceAssemblee::class)->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"]));
          if ($record_entity_sqc_ass !== NULL) {
            $flagSeq = 1;
            $entityRel = new \App\Entity\Assigne();
            $method = "setMotuFk";
            $entityRel->$method($entity);
            $method = "setSequenceAssembleeFk";
            $entityRel->$method($record_entity_sqc_ass);
            $method = "setNumMotu";
            $entityRel->$method($data2["num_motu"]);
            $foreign_record = $em->getRepository(Voc::class)->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
            if ($foreign_record === NULL) {
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b>  <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
            }

            $method = "setMethodeMotuVocFk";
            $entityRel->$method($foreign_record);
          }
          $record_entity_sqc_ass_ext = $em->getRepository(SequenceAssembleeExt::class)->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"]));
          if ($record_entity_sqc_ass_ext !== NULL) {
            $flagSeqExt = 1;
            $entityRel = new \App\Entity\Assigne();
            $method = "setMotuFk";
            $entityRel->$method($entity);
            $method = "setSequenceAssembleeExtFk";
            $entityRel->$method($record_entity_sqc_ass_ext);
            $method = "setNumMotu";
            $entityRel->$method($data2["num_motu"]);
            $foreign_record = $em->getRepository(Voc::class)->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
            if ($foreign_record === NULL) {
              $message .= $this->translator->trans('importfileService.ERROR unknown record') . ' : ' . $data2["code_methode_motu"] . '</b> INCONNU <br> ligne ' . (string) ($l2 + 2) . ": " . join(';', $data2) . "<br>";
            }

            $method = "setMethodeMotuVocFk";
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
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvDataMotu) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataEtablissement($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is institution
   */
  public function importCSVDataEtablissement($fichier, $userId = null) {
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
      $entity = new \App\Entity\Etablissement();
      //
      if (array_key_exists("etablissement", $columnByTable)) {
        foreach ($columnByTable["etablissement"] as $ColCsv) {
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
            if ($ColCsv == 'etablissement.nom_etablissement') {
              $record_entity = $em->getRepository(Etablissement::class)->findOneBy(array("nomEtablissement" => $dataColCsv));
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
      } catch (\Exception $e) {
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
      $entity = new \App\Entity\Pays();
      if (array_key_exists("pays", $columnByTable)) {
        foreach ($columnByTable["pays"] as $ColCsv) {
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $varfield = explode(".", $field)[1];
          if ($field == 'pays.codePays') {
            $record_pays = $em->getRepository(Pays::class)->findOneBy(array("codePays" => $dataColCsv));
            if ($record_pays !== NULL) {
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
      } catch (\Exception $e) {
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
   *  importCSVDataSequenceAssembleeExt($fichier, $userId = null)
   * $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is external_sequence
   */
  public function importCSVDataSequenceAssembleeExt($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_personne = array();
    $commentaireCompoLotMateriel = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\SequenceAssembleeExt();
      //
      foreach ($columnByTable["sequence_assemblee_ext"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'sequence_assemblee_ext.code_sqc_ass_ext') {
            $record_entity = $em->getRepository(SequenceAssembleeExt::class)->findOneBy(array("codeSqcAssExt" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'sequence_assemblee_ext.date_creation_sqc_ass_ext') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
      foreach ($columnByTable["sqc_ext_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\SqcExtEstRealisePar;
            $method = "setSequenceAssembleeExtFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Enregistrement de SqcExtEstReferenceDans
      foreach ($columnByTable["sqc_ext_est_reference_dans"] as $ColCsv) {
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
            $entityRel = new \App\Entity\SqcExtEstReferenceDans();
            $method = "setSequenceAssembleeExtFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EspeceIdentifiee
      $entityRel = new \App\Entity\EspeceIdentifiee();
      $entityEspeceIdentifie = $entityRel;
      $method = "setSequenceAssembleeExtFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["espece_identifiee"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'espece_identifiee.date_identification') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstIdentifiePar
      foreach ($columnByTable["est_identifie_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\EstIdentifiePar();
            $method = "setEspeceIdentifieeFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataLotMaterielExt($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is external_biological_external
   */
  public function importCSVDataLotMaterielExt($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_personne = array();
    $commentaireCompoLotMateriel = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\LotMaterielExt();
      //
      foreach ($columnByTable["lot_materiel_ext"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'lot_materiel_ext.code_lot_materiel_ext') {
            $record_entity = $em->getRepository(LotMaterielExt::class)->findOneBy(array("codeLotMaterielExt" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'lot_materiel_ext.date_creation_lot_materiel_ext') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of lot_materiel_ext_est_realise_par
      foreach ($columnByTable["lot_materiel_ext_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\LotMaterielExtEstRealisePar;
            $method = "setLotMaterielExtFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Enregistrement de LotMaterielExtEstReferenceDans
      foreach ($columnByTable["lot_materiel_ext_est_reference_dans"] as $ColCsv) {
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
            $entityRel = new \App\Entity\LotMaterielExtEstReferenceDans();
            $method = "setLotMaterielExtFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EspeceIdentifiee
      $entityRel = new \App\Entity\EspeceIdentifiee();
      $entityEspeceIdentifie = $entityRel;
      $method = "setLotMaterielExtFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["espece_identifiee"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'espece_identifiee.date_identification') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstIdentifiePar
      foreach ($columnByTable["est_identifie_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\EstIdentifiePar();
            $method = "setEspeceIdentifieeFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataStation($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is site
   */
  public function importCSVDataStation($fichier, $userId = null) {
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
      $entity = new \App\Entity\Station();
      foreach ($columnByTable["station"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($field == 'station.codeStation') { // On teste pour savoir si le code_station a déja été créé.
            $record_station = $em->getRepository(Station::class)->findOneBy(array("codeStation" => $dataColCsv));
            if ($record_station !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b> <br>ligne ' . (string) ($l + 1) . ": " . join(';', $data) . "<br>";
            }
          }
          // we adapt the format of long and lat
          if ($field == 'station.latDegDec' || $field == 'station.longDegDec') {
            $dataColCsv = ($dataColCsv != '') ? floatval(str_replace(",", ".", $dataColCsv)) : null;
          }
          if ($field == 'station.altitudeM') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
          }
          if ($foreign_record === NULL) {
            switch ($foreign_table) {
              case 'Commune':
                if ($dataColCsv != '') {
                  $CodeCommune = $dataColCsv;
                  if (array_key_exists($CodeCommune, $list_new_commune)) {
                    $commune = $list_new_commune[$CodeCommune];
                  } else { // if CodeCommune is null create a new commune with a codeCommune as Nom_Commune|Nom_Region|Nom_Pays and field site_name = "Nom Commune" and municipality_name = "Nom Region"
                    $commune = new \App\Entity\Commune();
                    $commune->setCodeCommune($CodeCommune);
                    $list_field_commune = explode("|", $dataColCsv);
                    $commune->setNomCommune(str_replace("_", " ", $list_field_commune[0]));
                    $commune->setNomRegion(str_replace("_", " ", $list_field_commune[1]));
                    $commune->setDateCre($DateImport);
                    $commune->setDateMaj($DateImport);
                    $commune->setUserCre($userId);
                    $commune->setUserMaj($userId);
                    $pays_fk = $em->getRepository(Pays::class)->findOneBy(array("codePays" => $list_field_commune[2]));
                    if ($pays_fk === NULL) {
                      $message .= $this->translator->trans('importfileService.ERROR bad code') . ' : ' . $list_field_commune[2] . '</b>  <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
                    }
                    $commune->setPaysFk($pays_fk);
                    $em->persist($commune);
                    $list_new_commune[$CodeCommune] = $commune; // we keep in memory the communes created
                  }
                  $foreign_fieldName = $foreign_table . "Fk";
                  $method = $importFileCsvService->TransformNameForSymfony($foreign_fieldName, 'set');
                  $entity->$method($commune);
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
            if ($foreign_table == 'Pays') { // we memorize information about the country
              $code_pays = $foreign_record->getCodePays();
              $pays_record = $foreign_record;
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
    // A FAIRE : ajouter les champ commune.nom_commune +commune.nom_region

    if ($message == '') {
      try {
        $flush = $em->flush();
        return $this->translator->trans('importfileService.Import OK') . ' = ' . count($csvData) . '</br>' . $info;
      } catch (\Exception $e) {
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
   *  importCSVDataSequenceAssemblee($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is internal_sequence
   */
  public function importCSVDataSequenceAssemblee($fichier, $userId = null) {
    $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
    $csvData = $importFileCsvService->readCSV($fichier);
    $columnByTable = $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
    $DateImport = $importFileCsvService->GetCurrentTimestamp();
    $em = $this->entityManager; // call of Doctrine manager
    $compt = 0;
    $message = '';
    $info = $this->translator->trans('importfileService.Date of data set import') . ' : ' . $DateImport->format('Y-m-d H:i:s');
    $list_new_personne = array();
    $commentaireCompoLotMateriel = "";
    foreach ($csvData as $l => $data) { // 1- Line-to-line data processing ($ l)
      $compt++;
      #
      $entity = new \App\Entity\SequenceAssemblee();
      //
      foreach ($columnByTable["sequence_assemblee"] as $ColCsv) {
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // var_dump($ColCsv); var_dump($field); exit;
          if ($ColCsv == 'sequence_assemblee.code_sqc_ass') {
            $record_entity = $em->getRepository(SequenceAssemblee::class)->findOneBy(array("codeSqcAss" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $dataColCsv . "</b> <br>ligne " . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          // control and standardization of field formats
          if ($ColCsv == 'sequence_assemblee.date_creation_sqc_ass') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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

      # Record of sequence_assemblee_est_realise_par
      foreach ($columnByTable["sequence_assemblee_est_realise_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\SequenceAssembleeEstRealisePar();
            $method = "setSequenceAssembleeFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Enregistrement de SqcEstPublieDans
      foreach ($columnByTable["sqc_est_publie_dans"] as $ColCsv) {
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
            $entityRel = new \App\Entity\SqcEstPublieDans();
            $method = "setSequenceAssembleeFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Enregistrement de EstAligneEtTraite    (liaison aux chromatogramme)
      foreach ($columnByTable["est_aligne_et_traite"] as $ColCsv) {
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
            $entityRel = new \App\Entity\EstAligneEtTraite();
            $method = "setSequenceAssembleeFk";
            $entityRel->$method($entity);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EspeceIdentifiee
      $entityRel = new \App\Entity\EspeceIdentifiee();
      $entityEspeceIdentifie = $entityRel;
      $method = "setSequenceAssembleeFk";
      $entityRel->$method($entity);
      foreach ($columnByTable["espece_identifiee"] as $ColCsv) {
        $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
        if ($dataColCsv !== $data[$ColCsv]) {
          $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
        }
        $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
        $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content); // flag to know if 1) it is a foreign key
        if (!$flag_foreign) {
          $varfield = explode(".", $field)[1];
          // control and standardization of field formats
          if ($ColCsv == 'espece_identifiee.date_identification') {
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
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
          } else {
            $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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

      # Record of EstIdentifiePar
      foreach ($columnByTable["est_identifie_par"] as $ColCsv) {
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
            $entityRel = new \App\Entity\EstIdentifiePar();
            $method = "setEspeceIdentifieeFk";
            $entityRel->$method($entityEspeceIdentifie);
            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
            $varfield_parent = strstr($varfield, 'Voc', true);
            if (!$varfield_parent) {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));
            } else {
              $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));
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
      } catch (\Exception $e) {
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
   *  importCSVDataReferentielTaxon($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is taxon
   */
  public function importCSVDataReferentielTaxon($fichier, $userId = null) {
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
      $entity = new \App\Entity\ReferentielTaxon();
      if (array_key_exists("referentiel_taxon", $columnByTable)) {
        foreach ($columnByTable["referentiel_taxon"] as $ColCsv) {
          $field = $importFileCsvService->TransformNameForSymfony($ColCsv, 'field');
          $varfield = explode(".", $field)[1];
          $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv], 'tnrOx');
          if ($dataColCsv !== $data[$ColCsv]) {
            $message .= $this->translator->trans('importfileService.ERROR bad character') . '<b> : ' . $data[$ColCsv] . '</b> <br> ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
          }
          if ($ColCsv == 'referentiel_taxon.taxname') { // On teste pour savoir si le taxname a déja été créé.
            $record_entity = $em->getRepository(ReferentielTaxon::class)->findOneBy(array("taxname" => $dataColCsv));
            if ($record_entity !== NULL) {
              $message .= $this->translator->trans('importfileService.ERROR duplicate code') . '<b> : ' . $data[$ColCsv] . " / " . $ColCsv . '</b>  <br>ligne ' . (string) ($l + 2) . ": " . join(';', $data) . "<br>";
            }
          }
          if ($ColCsv == 'referentiel_taxon.validity') {
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
      } catch (\Exception $e) {
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
            $record_voc = $em->getRepository(Voc::class)->findOneBy(array("parent" => $dataColCsv, "code" => $code));
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
      } catch (\Exception $e) {
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
   *  importCSVDataPersonne($fichier, $userId = null)
   *  $fichier : path to the download csv file
   *  NOTE : the template of csv file to import is person
   */
  public function importCSVDataPersonne($fichier, $userId = null) {
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
      # Enregistrement des données de Personne
      $entity = new \App\Entity\Personne();
      //
      if (array_key_exists("personne", $columnByTable)) {
        foreach ($columnByTable["personne"] as $ColCsv) {
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
            if ($ColCsv == 'personne.nom_personne') {
              $record_entity = $em->getRepository(Personne::class)->findOneBy(array("nomPersonne" => $dataColCsv));
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
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
              }
              if ($foreign_record === NULL) {
                switch ($foreign_table) {
                  case 'etablissement':
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
      } catch (\Exception $e) {
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
      # Enregistrement des données de Personne
      $entity = new \App\Entity\Commune();
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
            if ($ColCsv == 'commune.code_commune') {
              $record_entity = $em->getRepository(Commune::class)->findOneBy(array("codeCommune" => $dataColCsv));
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
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv));
              } else {
                $foreign_record = $em->getRepository("App\\Entity\\" . $foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));
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
                case 'Pays':
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
      } catch (\Exception $e) {
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
