<?php

namespace Bbees\E3sBundle\Services;

use Doctrine\ORM\EntityManager;
use Bbees\E3sBundle\Services\ImportFileCsv;
use Bbees\E3sBundle\Entity\Motu;


/**
* Service ImportFileE3s
*/
class ImportFileE3s 
{
    private $entityManager;
    private $importFileCsv;
    
    /**
    *  __construct(EntityManager $manager,ImportFileCsv $importFileCsv )
    * constructeur  du service ImportFileE3s
    * $manager : le service manager de Doctrine ( @doctrine.orm.entity_manager )
    * $importFileCsv : le service d'import de fichier csv
    */ 
    public function __construct(EntityManager $manager,ImportFileCsv $importFileCsv ) {
       $this->entityManager = $manager ;
       $this->importFileCsv = $importFileCsv ;
    }

    /**
    *  importCSVDataAdnDeplace($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.adn-range
    */ 
    public function importCSVDataAdnDeplace($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataAdnRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_adn", "code_adn")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.adn-range</b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataAdnRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_adn = $em->getRepository("BbeesE3sBundle:Adn")->createQueryBuilder('adn')
            ->where('adn.codeAdn  LIKE :code_adn')
            ->setParameter('code_adn', $data["code_adn"])
            ->getQuery()
            ->getResult();
            $flagAdn = count($query_adn);
            if ($flagAdn == 0) $message .= "ERROR : le code adn <b>".$data["code_adn"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagAdn && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
                    $query_adn[0]->setBoiteFk($query_boite[0]);
                    $query_adn[0]->setDateMaj($DateImport); 
                    $em->persist($query_adn[0]); 
                } else {                
                    $query_adn[0]->setBoiteFk(null);
                    $query_adn[0]->setDateMaj($DateImport); 
                    $em->persist($query_adn[0]);   
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataAdnRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataIndividuLameRange($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.adn-range
    */ 
    public function importCSVDataAdnRange($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataAdnRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_adn", "code_adn")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.adn-range </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataAdnRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_adn = $em->getRepository("BbeesE3sBundle:Adn")->createQueryBuilder('adn')
            ->where('adn.codeAdn  LIKE :code_adn')
            ->setParameter('code_adn', $data["code_adn"])
            ->getQuery()
            ->getResult();
            $flagAdn = count($query_adn);
            if ($flagAdn == 0) $message .= "ERROR : le code lame coll <b>".$data["code_adn"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= "ERROR : le code boite  n a pas été renseigné pour le code lamme coll <b>".$data["code_lame_coll"]." <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagAdn && $flagBoite && $flagBoiteAffecte) { 
                if ($query_adn[0]->getBoiteFk() != null) {
                     $message .= 'ERROR : l adn <b>'.$data["code_adn"].'</b> a déjà été affecté à une boite : '.$query_adn[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
                } else {
                    $query_adn[0]->setBoiteFk($query_boite[0]);
                    $query_adn[0]->setDateMaj($DateImport); 
                    $em->persist($query_adn[0]); 
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataAdnRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

  
    
    /**
    *  importCSVDataIndividuLameDeplace($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.individu_lame-range
    */ 
    public function importCSVDataIndividuLameDeplace($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_lame_coll", "code_lame_coll")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.individu_lame-range </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataIndividuLamelRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_lame = $em->getRepository("BbeesE3sBundle:IndividuLame")->createQueryBuilder('lame')
            ->where('lame.codeLameColl  LIKE :code_lame_coll')
            ->setParameter('code_lame_coll', $data["code_lame_coll"])
            ->getQuery()
            ->getResult();
            $flagLame = count($query_lame);
            if ($flagLame == 0) $message .= "ERROR : le code lame coll <b>".$data["code_lame_coll"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLame && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
                    $query_lame[0]->setBoiteFk($query_boite[0]);
                    $query_lame[0]->setDateMaj($DateImport); 
                    $em->persist($query_lame[0]); 
                } else {                
                    $query_lame[0]->setBoiteFk(null);
                    $query_lame[0]->setDateMaj($DateImport); 
                    $em->persist($query_lame[0]);   
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataIndividuLamelRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataIndividuLameRange($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.individu_lame-range
    */ 
    public function importCSVDataIndividuLameRange($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_lame_coll", "code_lame_coll")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.individu_lame-range </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataIndividuLamelRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_lame = $em->getRepository("BbeesE3sBundle:IndividuLame")->createQueryBuilder('lame')
            ->where('lame.codeLameColl  LIKE :code_lame_coll')
            ->setParameter('code_lame_coll', $data["code_lame_coll"])
            ->getQuery()
            ->getResult();
            $flagLame = count($query_lame);
            if ($flagLame == 0) $message .= "ERROR : le code lame coll <b>".$data["code_lame_coll"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= "ERROR : le code boite  n a pas été renseigné pour le code lamme coll <b>".$data["code_lame_coll"]." <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLame && $flagBoite && $flagBoiteAffecte) { 
                if ($query_lame[0]->getBoiteFk() != null) {
                     $message .= 'ERROR : la lame <b>'.$data["code_lame_coll"].'</b> a déjà été affecté à une boite : '.$query_lame[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
                } else {
                    $query_lame[0]->setBoiteFk($query_boite[0]);
                    $query_lame[0]->setDateMaj($DateImport); 
                    $em->persist($query_lame[0]); 
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataIndividuLamelRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

  
    
    /**
    *  importCSVDataLotMaterielDeplace($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.lot_materiel-range
    */ 
    public function importCSVDataLotMaterielDeplace($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_lot_materiel", "code_lot_materiel") || !$importFileCsvService->testNameColumnCSV($columnByTable,"code_boite", "code_boite")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.lot_materiel-range </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataLotMaterielRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_lot = $em->getRepository("BbeesE3sBundle:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
            ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
            ->getQuery()
            ->getResult();
            $flagLot = count($query_lot);
            if ($flagLot == 0) $message .= "ERROR : le code lot materiel <b>".$data["code_lot_materiel"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLot && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
                    $query_lot[0]->setBoiteFk($query_boite[0]);
                    $query_lot[0]->setDateMaj($DateImport); 
                    $em->persist($query_lot[0]); 
                } else {                
                    $query_lot[0]->setBoiteFk(null);
                    $query_lot[0]->setDateMaj($DateImport); 
                    $em->persist($query_lot[0]);   
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataLotMaterielRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataLotMaterielRange($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.lot_materiel-range
    */ 
    public function importCSVDataLotMaterielRange($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_lot_materiel", "code_lot_materiel") || !$importFileCsvService->testNameColumnCSV($columnByTable,"code_boite", "code_boite")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.lot_materiel-range </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataLotMaterielRange as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_lot = $em->getRepository("BbeesE3sBundle:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
            ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
            ->getQuery()
            ->getResult();
            $flagLot = count($query_lot);
            if ($flagLot == 0) $message .= "ERROR : le code lot materiel <b>".$data["code_lot_materiel"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            //$query_boite
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["code_boite"] != null || $data["code_boite"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("BbeesE3sBundle:Boite")->createQueryBuilder('boite')
                ->where('boite.codeBoite LIKE :code_boite')
                ->setParameter('code_boite', $data["code_boite"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= "ERROR : le code boite  n a pas été renseigné pour le code lot materiel <b>".$data["code_lot_materiel"]." <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= "ERROR : le code boite <b>".$data["code_boite"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLot && $flagBoite && $flagBoiteAffecte) { 
                if ($query_lot[0]->getBoiteFk() != null) {
                     $message .= 'ERROR : le  lot materiel <b>'.$data["code_lot_materiel"].'</b> a déjà été affecté à une boite : '.$query_lot[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
                } else {
                    $query_lot[0]->setBoiteFk($query_boite[0]);
                    $query_lot[0]->setDateMaj($DateImport); 
                    $em->persist($query_lot[0]); 
                }
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataLotMaterielRange). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataLotMaterielPublie($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.lot_materiel-publie
    */ 
    public function importCSVDataLotMaterielPublie($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataLotMaterielPublie = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielPublie); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_lot_materiel", "code_lot_materiel") || !$importFileCsvService->testNameColumnCSV($columnByTable,"source", "source.code_source")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template import.lot_materiel-publie </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataLotMaterielPublie as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_lot = $em->getRepository("BbeesE3sBundle:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :code_lot_materiel')
            ->setParameter('code_lot_materiel', $data["code_lot_materiel"])
            ->getQuery()
            ->getResult();
            //$query_source
            $query_source = $em->getRepository("BbeesE3sBundle:Source")->createQueryBuilder('source')
            ->where('source.codeSource LIKE :code_source')
            ->setParameter('code_source', $data["source.code_source"])
            ->getQuery()
            ->getResult();                
            if (count($query_lot) == 0 || count($query_source) == 0) {
                if (count($query_lot) == 0) $message .= "ERROR : le code lot materiel <b>".$data["code_lot_materiel"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                if (count($query_source) == 0) $message .= "ERROR : le code source <b>".$data["source.code_source"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            } else {
                // recherche du nombre de sequence_assemblee associée au chromato  id (cf. table EstAligneEtTraite)                    //$query_lepd = $em->createQuery('SELECT lepd.id as id_lepd FROM BbeesE3sBundle:LotEstPublieDans lepd JOIN lepd.sourceFk s WHERE lepd.lotMaterielFk = '.$query[0]['id_lot'].' AND s.codeSource = '.$data["source.code_source"])->getResult();                    
                $query_lepd = $em->getRepository("BbeesE3sBundle:LotEstPublieDans")->createQueryBuilder('lepd')
                        ->where('lepd.lotMaterielFk = :id_lot')
                        ->setParameter('id_lot', $query_lot[0]->getId())
                        ->andwhere('source.codeSource = :code_source')
                        ->setParameter('code_source', $data["source.code_source"])
                        ->leftJoin('BbeesE3sBundle:Source', 'source', 'WITH', 'lepd.sourceFk = source.id')
                        ->getQuery()
                        ->getResult();              
                if (count($query_lepd) != 0 ) {
                    $message .= 'ERROR : le code source <b>'.$data["source.code_source"].'</b> existe déja dans la bdd pour le lot materiel : '.$data["code_lot_materiel"].' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                } else {
                    $entityRel = new \Bbees\E3sBundle\Entity\LotEstPublieDans();
                    $method = "setSourceFk";
                    $entityRel->$method($query_source[0]);
                    $method = "setLotMaterielFk";
                    $entityRel->$method($query_lot[0]);
                    $entityRel->setDateCre($DateImport);
                    $entityRel->setDateMaj($DateImport); 
                    $em->persist($entityRel);
                }                             
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataLotMaterielPublie). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataSqcAssembleePublie($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.sqc_assemblee-publie
    */ 
    public function importCSVDataSqcAssembleePublie($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataSqcAssembleePublie = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataSqcAssembleePublie); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_sqc_ass", "code_sqc_ass") || !$importFileCsvService->testNameColumnCSV($columnByTable,"accession_number", "accession_number") || !$importFileCsvService->testNameColumnCSV($columnByTable,"source", "source.code_source")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template  import.sqc_assemblee-publie </b>");             
            exit;
        }     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
 
        foreach($csvDataSqcAssembleePublie as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $query_sa = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->createQueryBuilder('sa')
            ->where('sa.codeSqcAss LIKE :code_sqc_ass')
            ->setParameter('code_sqc_ass', $data["code_sqc_ass"])
            ->getQuery()
            ->getResult();
            //$query_source
            $query_source = $em->getRepository("BbeesE3sBundle:Source")->createQueryBuilder('source')
            ->where('source.codeSource LIKE :code_source')
            ->setParameter('code_source', $data["source.code_source"])
            ->getQuery()
            ->getResult();                
            if (count($query_sa) == 0 || count($query_source) == 0) {
                if (count($query_sa) == 0) $message .= "ERROR : le code s&quence assemblée <b>".$data["code_sqc_ass"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                if (count($query_source) == 0) $message .= "ERROR : le code source <b>".$data["source.code_source"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            } else {
                // recherche du nombre de sequence_assemblee associée au chromato  id (cf. table EstAligneEtTraite)                    //$query_lepd = $em->createQuery('SELECT lepd.id as id_lepd FROM BbeesE3sBundle:LotEstPublieDans lepd JOIN lepd.sourceFk s WHERE lepd.lotMaterielFk = '.$query[0]['id_lot'].' AND s.codeSource = '.$data["source.code_source"])->getResult();                    
                $query_lepd = $em->getRepository("BbeesE3sBundle:SqcEstPublieDans")->createQueryBuilder('sepd')
                        ->where('sepd.sequenceAssembleeFk = :id_sa')
                        ->setParameter('id_sa', $query_sa[0]->getId())
                        ->getQuery()
                        ->getResult();              
                if (count($query_lepd) != 0 ||  $query_sa[0]->getAccessionNumber() != '') {
                    if (count($query_lepd) != 0)  $message .= 'ERROR : un code source existe déja dans la bdd pour la sequence assemblee: '.$data["code_sqc_ass"].' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                    if ( $query_sa[0]->getAccessionNumber() != '')  $message .= 'ERROR :un accession number <b>'.$query_sa[0]->getAccessionNumber().'</b> existe déja dans la bdd pour la sequence assemblee : '.$data["code_sqc_ass"].' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";    
                } else {
                    $method = "setAccessionNumber";
                    $query_sa[0]->$method($data["accession_number"]);
                    $query_sa[0]->setDateMaj($DateImport); 
                    $entityRel = new \Bbees\E3sBundle\Entity\SqcEstPublieDans();
                    $method = "setSourceFk";
                    $entityRel->$method($query_source[0]);
                    $method = "setSequenceAssembleeFk";
                    $entityRel->$method($query_sa[0]);
                    $entityRel->setDateCre($DateImport);
                    $entityRel->setDateMaj($DateImport); 
                    $em->persist($entityRel);
                }                             
            }
        }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataSqcAssembleePublie). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataSource($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template source
    */ 
    public function importCSVDataSource($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"adn", "adn.code_adn")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template adn </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\Source();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["source"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'source.code_source') { // On teste pour savoir si le adn.code_source a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Source")->findOneBy(array("codeSource" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_source <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    
                    // on adapte les format des types float
                    if ($ColCsv == 'source.annee_source' && !is_null($dataColCsv)) {$dataColCsv = intval(str_replace(",", ".", $dataColCsv));}
                    // on adapte les formats de date
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                    if (!is_null($dataColCsv)) {
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break;   
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b> INCONNU <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entity->$method($foreign_record);
                        }
                    }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du  SourceAEteIntegrePar                    
             foreach($columnByTable["source_a_ete_integre_par"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\SourceAEteIntegrePar();
                        $method = "setSourceFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataPcrChromato($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template adn
    */ 
    public function importCSVDataPcrChromato($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"pcr", "pcr.code_pcr") || !$importFileCsvService->testNameColumnCSV($columnByTable,"chromatogramme", "chromatogramme.code_chromato")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template pcr_chromato </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_pcr = array(); 
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;   
            $flag_new_pcr = 1;
            # Enregistrement des données de pcr
            $entity = new \Bbees\E3sBundle\Entity\Pcr();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["pcr"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'pcr.code_pcr') { // On teste pour savoir si la pcr avec un code_pcr existait déja dans la bdd
                        $record_entity = $em->getRepository("BbeesE3sBundle:Pcr")->findOneBy(array("codePcr" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_pcr <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } 
                    }
                    // on adapte les formats
                    if ($ColCsv == 'pcr.date_pcr' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }
                              break;
                            default:
                              $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            // gestion des pcr qui ont donné lieu a n plusieurs chromato (n lignes)
            if (array_key_exists($data['pcr.code_pcr'], $list_new_pcr)) {
               $flag_new_pcr = 0;
               $entity = $list_new_pcr[$data['pcr.code_pcr']];
               // var_dump($compt); var_dump($data['pcr.code_pcr']);
            } else {
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $em->persist($entity);
               $list_new_pcr[$data['pcr.code_pcr']] = $entity ;
            }

            # Enregistrement du PcrEstRealisePar 
            if ($flag_new_pcr) {
                foreach($columnByTable["pcr_est_realise_par"] as $ColCsv){   
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                   $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                   if($flag_foreign && trim($dataColCsv) != ''){ 
                       foreach($tab_foreign_field as $val_foreign_field){ 
                           $val_foreign_field = trim($val_foreign_field);
                           $entityRel = new \Bbees\E3sBundle\Entity\PcrEstRealisePar();
                           $method = "setPcrFk";
                           $entityRel->$method($entity);
                           // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                           $varfield_parent = strstr($varfield, 'Voc', true);
                           if (!$varfield_parent) {
                             $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                           } else {
                              $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                           }  
                           if($foreign_record === NULL){  
                              switch ($foreign_table) {
                                 case "Voc":
                                    $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                 break;
                                   default:
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                               }
                            } else {
                               $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                               $entityRel->$method($foreign_record);                               
                           } 
                           $entityRel->setDateCre($DateImport);                           
                           $entityRel->setDateMaj($DateImport);     
                           $em->persist($entityRel);
                       }
                   } 
                }  
            }

            # Enregistrement du chromatogramme   
            $entityRel = new \Bbees\E3sBundle\Entity\Chromatogramme();
            $method = "setPcrFk";
            $entityRel->$method($entity);             
            foreach($columnByTable["chromatogramme"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'chromatogramme.code_chromato') { // On teste pour savoir si le chromatogramme.code_chromato a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Chromatogramme")->findOneBy(array("codeChromato" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_chromato <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign && $ColCsv != 'chromatogramme.pcr_fk(pcr.code_pcr)'){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }                              break;
                            default:
                              $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entityRel->$method($foreign_record);
                   }
                   $entityRel->setDateCre($DateImport);
                   $entityRel->setDateMaj($DateImport); 
                   $em->persist($entityRel);
                }   
             }                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

 
    /**
    *  importCSVDataPcr($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template adn
    */ 
    public function importCSVDataPcr($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"pcr", "pcr.code_pcr")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template pcr </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;   
            # Enregistrement des données de pcr
            $entity = new \Bbees\E3sBundle\Entity\Pcr();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["pcr"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'pcr.code_pcr') { // On teste pour savoir si la pcr avec un code_pcr existait déja dans la bdd
                        $record_entity = $em->getRepository("BbeesE3sBundle:Pcr")->findOneBy(array("codePcr" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_pcr <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } 
                    }
                    // on adapte les formats
                    if ($ColCsv == 'pcr.date_pcr' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }
                              break;
                            default:
                              $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            // persist de la pcr (1 pcr /ligne)
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);

            # Enregistrement du PcrEstRealisePar 
            foreach($columnByTable["pcr_est_realise_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\PcrEstRealisePar();
                       $method = "setPcrFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                             case "Voc":
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                             break;
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    * importCSVDataChromato(array $csvData)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template pcr_chromato
    */ 
    public function importCSVDataChromato($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"chromatogramme", "chromatogramme.code_chromato")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template chromatogramme </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');           
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;   
            # Enregistrement du chromatogramme   
            $entity = new \Bbees\E3sBundle\Entity\Chromatogramme();            
            foreach($columnByTable["chromatogramme"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'chromatogramme.code_chromato') { // On teste pour savoir si le chromatogramme.code_chromato a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Chromatogramme")->findOneBy(array("codeChromato" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_chromato <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }                             
                              break;
                            default:
                              $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                   $entity->setDateCre($DateImport);
                   $entity->setDateMaj($DateImport);
                   $em->persist($entity);
                }   

             } 
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataAdn($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template adn
    */ 
    public function importCSVDataAdn($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"adn", "adn.code_adn")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template adn </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\Adn();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["adn"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'adn.code_adn') { // On teste pour savoir si le adn.code_adn a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Adn")->findOneBy(array("codeAdn" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_adn <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    
                    // on adapte les format des types float
                    if ($ColCsv == 'adn.concentration_ng_microlitre' && !is_null($dataColCsv)) {$dataColCsv = floatval(str_replace(",", ".", $dataColCsv));}
                    // on adapte les formats de date
                    if ($ColCsv == 'adn.date_adn' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if (!is_null($dataColCsv)){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                    if (!is_null($dataColCsv)) {
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break;   
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b> INCONNU <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entity->$method($foreign_record);
                        }
                    }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du AdnEstRealisePar                     
             foreach($columnByTable["adn_est_realise_par"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\AdnEstRealisePar();
                        $method = "setAdnFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataProgramme($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template programme
    */ 
    public function importCSVDataProgramme($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"programme", "programme.code_programme")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template programme </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();            
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            $entity = new \Bbees\E3sBundle\Entity\Programme();
            if (array_key_exists("programme" ,$columnByTable)) {
               foreach($columnByTable["programme"] as $ColCsv){ 
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }              
                   $varfield = explode(".", $field)[1];
                   if($field == 'programme.codeProgramme') { // On teste pour savoir si le code_programme a déja été créé. 
                       $record_entity = $em->getRepository("BbeesE3sBundle:Programme")->findOneBy(array("codeProgramme" => $dataColCsv)); 
                       if($record_entity !== NULL){ 
                          $message .= "ERROR : le code Programme <b>".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                       }
                   }
                   if ($dataColCsv === '') $dataColCsv = NULL; // si il n'y a pas de valeur on initialise la valeur a NULL
                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                   $entity->$method($dataColCsv);   // on enregistre la valeurs du champ                   
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $em->persist($entity);  
           } else {
              return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
              exit;
           }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return "Import de ". count($csvData). " données  ! </br>".$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
               return 'probleme de FLUSH : </br>'.strval($e);
           }          
       } else {
           return $info.'</br>'.$message;
       }
    }
      
    /**
    *  importCSVDataCollecte($fichier)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template collecte
    */ 
    public function importCSVDataCollecte($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"collecte", "collecte.code_collecte")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template collecte </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array();      
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\Collecte();   
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            // var_dump($columnByTable["collecte"]); exit;
            foreach($columnByTable["collecte"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($field == 'collecte.codeCollecte') { // On teste pour savoir si le code_collecte a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Collecte")->findOneBy(array("codeCollecte" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code Collecte <b>".$dataColCsv."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les format 
                    if ($ColCsv == 'collecte.conductivite_micro_sie_cm' || $ColCsv == 'collecte.temperature_c') {
                        if ($dataColCsv != '') {
                            $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
                            if ($dataColCsv == '') $message .= "ERROR : format float <b>".$data[$ColCsv]."</b>  <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'collecte.duree_echantillonnage_mn' ) { 
                         if ($dataColCsv != '') {
                            $dataColCsv = intval(str_replace(",", ".", $dataColCsv)); 
                         } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'collecte.a_faire') { 
                        if ($dataColCsv != '') {
                            if ($dataColCsv == 'OUI' || $dataColCsv == 'NON') {
                                $dataColCsv = ($dataColCsv=='OUI') ? 1 : 0; 
                            } else {
                                $message .= "ERROR : le contenu de ".$ColCsv." n est pas valide  <b>".$data[$ColCsv]."</b> # OUI/NON: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";    
                            }
                        } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'collecte.date_collecte' ) {
                        if ($dataColCsv != ''){
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) ex. : station.commune(commune.nom_commune)
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On prévoit la possibilité 
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   // var_dump($varfield); var_dump($varfield_parent); var_dump($field);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));  
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                           default:
                              $message .= "ERROR : ".$field."-".$foreign_table.".".$foreign_field." <b>[" . $dataColCsv. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du ACibler                      
             foreach($columnByTable["a_cibler"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\ACibler();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }
  
             # Enregistrement du APourFixateur                      
             foreach($columnByTable["a_pour_fixateur"] as $ColCsv){
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\APourFixateur();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= "ERROR : ".$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }
            
            # Enregistrement du APourSamplingMethod                     
             foreach($columnByTable["a_pour_sampling_method"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\APourSamplingMethod();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= "ERROR : ".$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                }
             }    
             
            # Enregistrement du EstEffectuePar                     
             foreach($columnByTable["est_effectue_par"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\EstEffectuePar();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= "ERROR : ".$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        } 
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }  
             
            # Enregistrement du EstFinancePar                     
             foreach($columnByTable["est_finance_par"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\EstFinancePar();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= "ERROR : ".$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }  
             
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataLame($fichier)
     * $fichier : le path vers le fichiers csv downloader
     * importation des données csv : template individu_lame
    */ 
    public function importCSVDataLame($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"individu_lame", "individu_lame.code_lame_coll")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template individu_lame </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine 
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;         
            # Enregistrement des données de lame
            $entity = new \Bbees\E3sBundle\Entity\IndividuLame();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["individu_lame"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($field); var_dump($ColCsv); 
                    if($ColCsv == 'individu_lame.code_lame_coll') { // On teste pour savoir si le code_lame_coll a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:IndividuLame")->findOneBy(array("codeLameColl" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_lame_coll <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    if ($ColCsv == 'individu_lame.date_lame' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                    if (!is_null($dataColCsv)) { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entity->$method($foreign_record);                               
                        }  
                    }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du IndividuLameEstRealisePar                     
            foreach($columnByTable["individu_lame_est_realise_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\IndividuLameEstRealisePar();
                       $method = "setIndividuLameFk";
                       $entityRel->$method($entity);
                       if (!is_null($val_foreign_field) && $val_foreign_field != '') { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                            // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                            } else {
                               $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                }
                             } else {
                                $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                                $entityRel->$method($foreign_record);                               
                            } 
                       }
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               }
            }  
        }  
        # FLUSH si il n'y a pas de message d'erreur
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
   
   /**
    *  importCSVDataIndividu($fichier)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template individu
    */ 
    public function importCSVDataIndividu($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"individu", "individu.code_ind_biomol")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template individu </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine      
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;           
            # Enregistrement des données de Individu
            $entity = new \Bbees\E3sBundle\Entity\Individu();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["individu"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'individu.code_ind_biomol' && !is_null($dataColCsv)) { // On teste pour savoir si le code_ind_biomol a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Individu")->findOneBy(array("codeIndBiomol" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_ind_biomol <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    if($ColCsv == 'code_ind_tri_morpho' && !is_null($dataColCsv)) { // On teste pour savoir si le code_ind_tri_morpho a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:Individu")->findOneBy(array("codeIndTriMorpho" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_ind_tri_morpho <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats des champs DATE ou FLOAT
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   if (!is_null($dataColCsv)) { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entity->$method($foreign_record);                               
                        }  
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du EspeceIdentifiee 
            $key_taxname = array_keys($columnByTable["espece_identifiee"], "espece_identifiee.referentiel_taxon_fk(referentiel_taxon.taxname)")[0];
            // var_dump($data[$columnByTable["espece_identifiee"][$key_taxname]]);
            $entityEspeceIdentifie = NULL;
            if ($data[$columnByTable["espece_identifiee"][$key_taxname]] != '') { // pour les taxname non null
                $entityRel = new \Bbees\E3sBundle\Entity\EspeceIdentifiee();
                $entityEspeceIdentifie = $entityRel;
                $method = "setIndividuFk";
                $entityRel->$method($entity);
                foreach($columnByTable["espece_identifiee"] as $ColCsv){ 
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   if ($dataColCsv === '') $dataColCsv = NULL;
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                   if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                       $varfield = explode(".", $field)[1];
                       // on adapte les formats
                       if ($ColCsv == 'espece_identifiee.date_identification' ) {
                           // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                           if (!is_null($dataColCsv)){
                               if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                               if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                               $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                               if (!$eventDate) {
                                   $message .= "ERROR : espece identifiee :le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                   $dataColCsv = NULL; 
                               } else {
                                   $tabdate = explode("/",$dataColCsv);
                                   if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                       $dataColCsv = date_format($eventDate, 'Y-m-d');
                                       $dataColCsv = new \DateTime($dataColCsv);
                                   } else {
                                       $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                       $dataColCsv = NULL; 
                                   }
                               }
                               //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                           } 
                       }
                       // on enregistre la valeurs du champ
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entityRel->$method($dataColCsv);                     
                   }
                   if($flag_foreign){ 
                       $varfield = explode(".", strstr($field, '(', true))[1];
                       $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                       $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                       $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                       if (!is_null($dataColCsv)) { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                            // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                            } else {
                               $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                }
                             } else {
                                $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                                $entityRel->$method($foreign_record);                               
                            }  
                       }
                   }    
                }
                $entityRel->setDateCre($DateImport);
                $entityRel->setDateMaj($DateImport); 
                $em->persist($entityRel);
            }
            
            # Enregistrement du EstIdentifiePar  (liste de personnes qui ont effectuées l'identification)   
            if (!is_null($entityEspeceIdentifie)) { // si il y a une espece identifiee de cree
                foreach($columnByTable["est_identifie_par"] as $ColCsv){  
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                   $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                   if($flag_foreign && !is_null($dataColCsv)){ 
                       foreach($tab_foreign_field as $val_foreign_field){
                           $val_foreign_field = trim($val_foreign_field);
                           $entityRel = new \Bbees\E3sBundle\Entity\EstIdentifiePar();
                           $method = "setEspeceIdentifieeFk";
                           $entityRel->$method($entityEspeceIdentifie);
                           if (!is_null($val_foreign_field) && $val_foreign_field != '') { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                               // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                               $varfield_parent = strstr($varfield, 'Voc', true);
                               if (!$varfield_parent) {
                                 $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                               } else {
                                  $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                               }  
                               if($foreign_record === NULL){  
                                  switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                    break; 
                                       default:
                                          $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                   }
                                } else {
                                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                                   $entityRel->$method($foreign_record);                               
                               } 
                           }
                           $entityRel->setDateCre($DateImport);
                           $entityRel->setDateMaj($DateImport); 
                           $em->persist($entityRel);
                       }
                   } 
                } 
            }
                         
        }         
        # FLUSH si il n'y a pas de message d'erreur
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataBoite($fichier)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template boite
    */ 
    public function importCSVDataBoite($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"boite", "boite.code_boite")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template boite </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine     
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');          
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            $entity = new \Bbees\E3sBundle\Entity\Boite();
            if (array_key_exists("boite" ,$columnByTable)) {
               foreach($columnByTable["boite"] as $ColCsv){  
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère  
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                   if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                      $varfield = explode(".", $field)[1];
                      if($field == 'boite.codeBoite') { // On teste pour savoir si le code_programme a déja été créé. 
                          $record_entity = $em->getRepository("BbeesE3sBundle:Boite")->findOneBy(array("codeBoite" => $dataColCsv)); 
                          if($record_entity !== NULL){ 
                             $message .= "ERROR : le code Boite <b>".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                          }
                      }
                      if ($dataColCsv === '') $dataColCsv = NULL; // si il n'y a pas de valeur on initialise la valeur a NULL
                      $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                      $entity->$method($dataColCsv);   // on enregistre la valeurs du champ                        
                   }
                   if($flag_foreign){ 
                       $varfield = explode(".", strstr($field, '(', true))[1];
                       $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                       $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                       $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field." parent=".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entity->$method($foreign_record);
                       } 
                   } 
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $em->persist($entity);    
           } else {
              return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
              exit;
           }  
        }        
        if ($message ==''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }   
    
    /**
    *  importCSVDataLotMateriel($fichier)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template lot_materiel
    */ 
    public function importCSVDataLotMateriel($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"lot_materiel", "lot_materiel.code_lot_materiel")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template lot_materiel </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine         
        // traitement ligne par ligne du fichier csv
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\LotMateriel();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["lot_materiel"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'lot_materiel.code_lot_materiel') { // On teste pour savoir si le code_lot_materiel a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:LotMateriel")->findOneBy(array("codeLotMateriel" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_lot_materiel <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    if ($ColCsv == 'lot_materiel.date_lot_materiel' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Boite":
                                break;
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                              $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du LotMaterielEstRealisePar                     
             foreach($columnByTable["lot_materiel_est_realise_par"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\LotMaterielEstRealisePar();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }  
             
            # Enregistrement du LotEstPublieDans                     
             foreach($columnByTable["lot_est_publie_dans"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\LotEstPublieDans();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }  
             
            # Enregistrement du CompositionLotMateriel                      
             foreach($columnByTable["composition_lot_materiel"] as $ColCsv){  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                if ($ColCsv == 'composition_lot_materiel.commentaire_compo_lot_materiel' ) {
                    $commentaireCompoLotMateriel = $dataColCsv;
                }
                
                if ($ColCsv == 'composition_lot_materiel.nb_individus+type_individu_voc_fk(voc.code)' ) {
                    $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\CompositionLotMateriel();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        $entityRel->setCommentaireCompoLotMateriel($commentaireCompoLotMateriel);
                        // On décompose l'information en deux variable $nb_individus & $type_individu
                        $nb_individu = (int) ereg_replace("[^0-9]","",$val_foreign_field); 
                        $type_individu =  ereg_replace("[0-9]","",$val_foreign_field); 
                        $type_individu = trim($type_individu);
                        // var_dump($val_foreign_field); var_dump($type_individu); var_dump($nb_individu);
                        if ($nb_individu == 0)  $nb_individu = NULL;
                        $entityRel->setNbIndividus($nb_individu);
                        $foreign_record = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("code" => $type_individu, "parent" => 'typeIndividu'));  
                        if($foreign_record === NULL){  
                           switch ("Voc") {
                                default:
                                   $message .= "ERROR :  Voc.code (typeIndividu)[" . $type_individu. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $entityRel->setTypeIndividuVocFk($foreign_record);                               
                        }
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                }

             }
  
            # Enregistrement du EspeceIdentifiee 
            $entityRel = new \Bbees\E3sBundle\Entity\EspeceIdentifiee();
            $entityEspeceIdentifie = $entityRel;
            $method = "setLotMaterielFk";
            $entityRel->$method($entity);
             foreach($columnByTable["espece_identifiee"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // on adapte les formats
                    if ($ColCsv == 'espece_identifiee.date_identification' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : espece identifiee :le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                    $val_foreign_field = trim($dataColCsv);
                    // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                               $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entityRel->$method($foreign_record);                               
                    }  
                }    
             }
             $entityRel->setDateCre($DateImport);
             $entityRel->setDateMaj($DateImport); 
             $em->persist($entityRel);
            
            # Enregistrement du EstIdentifiePar                     
             foreach($columnByTable["est_identifie_par"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \Bbees\E3sBundle\Entity\EstIdentifiePar();
                        $method = "setEspeceIdentifieeFk";
                        $entityRel->$method($entityEspeceIdentifie);
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entityRel->$method($foreign_record);                               
                        }  
                        $entityRel->setDateCre($DateImport);
                        $entityRel->setDateMaj($DateImport); 
                        $em->persist($entityRel);
                    }
                } 
             }    
                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }    

    /**
    *  importCSVDataMotuFile($fichier)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template import.motu-assigne
    */ 
    public function importCSVDataMotuFile($fichier,\Bbees\E3sBundle\Entity\Motu $motu)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvDataMotu = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataMotu); // Recupération des champs du CSv sous la forme d'un tableau / Table
        //var_dump($columnByTable); exit;
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"code_seq_ass", "code_seq_ass")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template motu </b>");             
            exit;
        }  
    
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        //var_dump($csvData);
        $entity = $motu;  
        
        # Enregistrement des données de motu
        $entity = $motu;   
        foreach($csvDataMotu as $l2 => $data2){ // 1- Traitement des données ligne à ligne ($l)
                $flagSeq = 0 ;
                $flagSeqExt = 0 ;
                $record_entity_sqc_ass = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"])); 
                if($record_entity_sqc_ass !== NULL){ 
                    $flagSeq = 1 ;
                    $entityRel = new \Bbees\E3sBundle\Entity\Assigne();
                    $method = "setMotuFk";
                    $entityRel->$method($entity);    
                    $method = "setSequenceAssembleeFk";
                    $entityRel->$method($record_entity_sqc_ass);
                    $method = "setNumMotu";
                    $entityRel->$method($data2["num_motu"]);
                    $foreign_record = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
                    if($foreign_record === NULL) $message .= "ERROR : Voc, code  <b>[" . $data2["code_methode_motu"]. ']</b> INCONNU <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                    $method = "setMethodeMotuVocFk";
                    $entityRel->$method($foreign_record);
                }
                $record_entity_sqc_ass_ext = $em->getRepository("BbeesE3sBundle:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"])); 
                if($record_entity_sqc_ass_ext !== NULL){ 
                    $flagSeqExt = 1 ;
                    $entityRel = new \Bbees\E3sBundle\Entity\Assigne();
                    $method = "setMotuFk";
                    $entityRel->$method($entity); 
                    $method = "setSequenceAssembleeExtFk";
                    $entityRel->$method($record_entity_sqc_ass_ext);
                    $method = "setNumMotu";
                    $entityRel->$method($data2["num_motu"]);
                    $foreign_record = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu")); 
                    if($foreign_record === NULL) $message .= "ERROR : Voc, code  <b>[" . $data2["code_methode_motu"]. ']</b> INCONNU <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                    $method = "setMethodeMotuVocFk";
                    $entityRel->$method($foreign_record);
                }  
                //var_dump($l2); var_dump($flagSeqExt); var_dump($flagSeq);  var_dump($data2);
                $entityRel->setDateCre($DateImport);
                $entityRel->setDateMaj($DateImport); 
                $em->persist($entityRel);
                if (!$flagSeq && !$flagSeqExt ) $message .= "ERROR : le code sequence assemblee <b>".$data2["code_seq_ass"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                if ($flagSeq && $flagSeqExt) $message .= "ERROR : le code sequence assemblee existe en interne et externe !? <b>".$data2["code_seq_ass"].'</b> n existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
            }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataMotu). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataMotu($fichier, $fichier_motu)
    *    $fichier : le path vers le fichiers csv de metadata  downloader
    *  $fichier : le path vers le fichiers csv des  données  downloader
     * importation des données csv : template motu
    */ 
    public function importCSVDataMotu($fichier, $fichier_motu)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $csvDataMotu = $importFileCsvService->readCSV($fichier_motu);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"motu", "motu.nom_fichier_csv")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template motu </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine  
        // traitement ligne par ligne du fichier csv          
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        //var_dump($csvData);
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de motu
            $entity = new \Bbees\E3sBundle\Entity\Motu();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["motu"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // on memorise le nom du fichier pour le traiter ensuite 
                    if ($ColCsv == 'motu.nom_fichier_csv' ) {$nom_fichier_csv = $dataColCsv ;}
                    // on adapte  le format date de la colonne  motu.date_motu
                    if ($ColCsv == 'motu.date_motu' ) {
                        if ($dataColCsv != ''){
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                    if (!is_null($dataColCsv)) {
                        // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b> INCONNU <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                            $entity->$method($foreign_record);
                        }
                    }
                }  
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du MotuEstGenerePar                     
            foreach($columnByTable["motu_est_genere_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\MotuEstGenerePar();
                       $method = "setMotuFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }   
            
            # Traitement du fichier csv des motus
            if (array_key_exists("code_seq_ass", $csvDataMotu[0]) && array_key_exists("num_motu", $csvDataMotu[0]) && array_key_exists("code_methode_motu", $csvDataMotu[0])) {
                foreach($csvDataMotu as $l2 => $data2){ // 1- Traitement des données ligne à ligne ($l)
                    $flagSeq = 0 ;
                    $flagSeqExt = 0 ;
                    $record_entity_sqc_ass = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"])); 
                    if($record_entity_sqc_ass !== NULL){ 
                        $flagSeq = 1 ;
                        $entityRel = new \Bbees\E3sBundle\Entity\Assigne();
                        $method = "setMotuFk";
                        $entityRel->$method($entity);    
                        $method = "setSequenceAssembleeFk";
                        $entityRel->$method($record_entity_sqc_ass);
                        $method = "setNumMotu";
                        $entityRel->$method($data2["num_motu"]);
                        $foreign_record = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
                        if($foreign_record === NULL) $message .= "ERROR : Voc, code  <b>[" . $data2["code_methode_motu"]. ']</b> INCONNU <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                        $method = "setMethodeMotuVocFk";
                        $entityRel->$method($foreign_record);
                    }
                    $record_entity_sqc_ass_ext = $em->getRepository("BbeesE3sBundle:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"])); 
                    if($record_entity_sqc_ass_ext !== NULL){ 
                        $flagSeqExt = 1 ;
                        $entityRel = new \Bbees\E3sBundle\Entity\Assigne();
                        $method = "setMotuFk";
                        $entityRel->$method($entity); 
                        $method = "setSequenceAssembleeExtFk";
                        $entityRel->$method($record_entity_sqc_ass_ext);
                        $method = "setNumMotu";
                        $entityRel->$method($data2["num_motu"]);
                        $foreign_record = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu")); 
                        if($foreign_record === NULL) $message .= "ERROR : Voc, code  <b>[" . $data2["code_methode_motu"]. ']</b> INCONNU <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                        $method = "setMethodeMotuVocFk";
                        $entityRel->$method($foreign_record);
                    }  
                    //var_dump($l2); var_dump($flagSeqExt); var_dump($flagSeq);  var_dump($data2);
                    $entityRel->setDateCre($DateImport);
                    $entityRel->setDateMaj($DateImport); 
                    $em->persist($entityRel);
                    if (!$flagSeq && !$flagSeqExt ) $message .= "ERROR : le code sequence assemblee <b>".$data2["code_seq_ass"].'</b> n existe pas dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                    if ($flagSeq && $flagSeqExt) $message .= "ERROR : le code sequence assemblee existe en interne et externe !? <b>".$data2["code_seq_ass"].'</b> n existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                }
            } else {
               return("ERROR : <b> le fichier des données de motu ne contient pas les trois collonnes : {code_seq_ass, num_motu, code_methode_motu} </b>");
               exit;                
            }
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvDataMotu). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    /**
    *  importCSVDataEtablissement($fichier)
    * $fichier : le path vers le fichiers csv downloader
    * importation des données csv : template etablissement
    */ 
    public function importCSVDataEtablissement($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"etablissement", "etablissement.nom_etablissement")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template etablissement </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine     
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;         
            # Enregistrement des données de lame
            $entity = new \Bbees\E3sBundle\Entity\Etablissement();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            if (array_key_exists("etablissement" ,$columnByTable)) {
                foreach($columnByTable["etablissement"] as $ColCsv){  
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                    $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                    if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                        $varfield = explode(".", $field)[1];
                        if($ColCsv  == 'etablissement.nom_etablissement') { // On teste pour savoir si l'etablissement n'a pas déja été créé 
                            $record_entity = $em->getRepository("BbeesE3sBundle:Etablissement")->findOneBy(array("nomEtablissement" => $dataColCsv)); 
                            if($record_entity !== NULL){ 
                               $message .= "ERROR : le nom de l'etablissement <b>".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                            }
                        }
                        // on adapte les formats
                        // on enregistre la valeurs du champ
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entity->$method($dataColCsv);                     
                    }               
                }
                $entity->setDateCre($DateImport);
                $entity->setDateMaj($DateImport);
                $em->persist($entity);
            } else {
               return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
               exit;
            }
        }  
        # FLUSH si il n'y a pas de message d'erreur
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
        
    
    /**
    *  importCSVDataPays(array $csvData)
    * importation des données csv : template
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataPays($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"pays", "pays.code_pays")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template pays </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine     
         $compt = 0;
         $message = '';
         $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
         $list_new_commune = array();            
         foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
             $compt++;
             $entity = new \Bbees\E3sBundle\Entity\Pays();
             if (array_key_exists("pays" ,$columnByTable)) {
                foreach($columnByTable["pays"] as $ColCsv){  
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                    $varfield = explode(".", $field)[1];
                    if($field == 'pays.codePays') { // On teste pour savoir si le code_pays a déja été créé. 
                        $record_pays = $em->getRepository("BbeesE3sBundle:Pays")->findOneBy(array("codePays" => $dataColCsv)); 
                        if($record_pays !== NULL){ 
                           $message .= "ERROR : le code Pays <b>".$dataColCsv.'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+1).": ".join(';', $data)."<br>"; 
                        }
                    }
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);   // on enregistre la valeurs du champ                   
                }
                $entity->setDateCre($DateImport);
                $entity->setDateMaj($DateImport);
                $em->persist($entity);  
            } else {
               return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
               exit;
            }
        }        
        if ($message ==''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données réussi ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
   /**
    *  importCSVDataSequenceAssemblee($fichier)
    * importation des données csv : template
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataSequenceAssembleeExt($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"sequence_assemblee_ext", "sequence_assemblee_ext.code_sqc_ass_ext")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template sequence_assemblee_ext </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\SequenceAssembleeExt();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["sequence_assemblee_ext"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // ex. station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'sequence_assemblee_ext.code_sqc_ass_ext') { // On teste pour savoir si le code_sqc_ass a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_sqc_ass_ext <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    if ($ColCsv == 'sequence_assemblee_ext.date_creation_sqc_ass_ext' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du seq_ass_ext_est_realise_par                    
            foreach($columnByTable["sqc_ext_est_realise_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\SqcExtEstRealisePar;
                       $method = "setSequenceAssembleeExtFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }  
    
            # Enregistrement de SqcExtEstReferenceDans                    
            foreach($columnByTable["sqc_ext_est_reference_dans"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\SqcExtEstReferenceDans();
                       $method = "setSequenceAssembleeExtFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }   
            
            # Enregistrement du EspeceIdentifiee 
            $entityRel = new \Bbees\E3sBundle\Entity\EspeceIdentifiee();
            $entityEspeceIdentifie = $entityRel;
            $method = "setSequenceAssembleeExtFk";
            $entityRel->$method($entity);
             foreach($columnByTable["espece_identifiee"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // on adapte les formats
                    if ($ColCsv == 'espece_identifiee.date_identification' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : espece identifiee :le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                    $val_foreign_field = trim($dataColCsv);
                    // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                            default:
                               $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entityRel->$method($foreign_record);                               
                    }  
                }    
             }
             $entityRel->setDateCre($DateImport);
             $entityRel->setDateMaj($DateImport); 
             $em->persist($entityRel);
            
            # Enregistrement du EstIdentifiePar                     
            foreach($columnByTable["est_identifie_par"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }                           
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

   /**
    *  importCSVDataLotMaterielExt($fichier)
    * importation des données csv : template
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataLotMaterielExt($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"lot_materiel_ext", "lot_materiel_ext.code_lot_materiel_ext")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template lot_materiel_ext </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\LotMaterielExt();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["lot_materiel_ext"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // ex. station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'lot_materiel_ext.code_lot_materiel_ext') { // On teste pour savoir si le code_lot_materiel_ext a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:LotMaterielExt")->findOneBy(array("codeLotMaterielExt" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_lot_materiel_ext <b>".$data[$ColCsv]."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    if ($ColCsv == 'lot_materiel_ext.date_creation_lot_materiel_ext' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du lot_materiel_ext_est_realise_par                    
            foreach($columnByTable["lot_materiel_ext_est_realise_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\LotMaterielExtEstRealisePar;
                       $method = "setLotMaterielExtFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }  
    
            # Enregistrement de LotMaterielExtEstReferenceDans                    
            foreach($columnByTable["lot_materiel_ext_est_reference_dans"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\LotMaterielExtEstReferenceDans();
                       $method = "setLotMaterielExtFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       } 
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }   
            
            # Enregistrement du EspeceIdentifiee 
            $entityRel = new \Bbees\E3sBundle\Entity\EspeceIdentifiee();
            $entityEspeceIdentifie = $entityRel;
            $method = "setLotMaterielExtFk";
            $entityRel->$method($entity);
             foreach($columnByTable["espece_identifiee"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // on adapte les formats
                    if ($ColCsv == 'espece_identifiee.date_identification' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : espece identifiee :le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                    $val_foreign_field = trim($dataColCsv);
                    // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                            default:
                               $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entityRel->$method($foreign_record);                               
                    }  
                }    
             }
             $entityRel->setDateCre($DateImport);
             $entityRel->setDateMaj($DateImport); 
             $em->persist($entityRel);
            
            # Enregistrement du EstIdentifiePar                     
            foreach($columnByTable["est_identifie_par"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }                           
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    
   /**
    *  importCSVDataStation($fichier)
    *  importation des données csv : template station
    *  $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataStation($fichier)
    { 
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"station", "station.code_station")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template station </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine   
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            $entity = new \Bbees\E3sBundle\Entity\Station();
            foreach($columnByTable["station"] as $ColCsv){  
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                   $varfield = explode(".", $field)[1];
                   // var_dump($ColCsv); var_dump($field); exit;
                   if($field == 'station.codeStation') { // On teste pour savoir si le code_station a déja été créé. 
                       $record_station = $em->getRepository("BbeesE3sBundle:Station")->findOneBy(array("codeStation" => $dataColCsv)); 
                       if($record_station !== NULL){ 
                          $message .= "ERROR : le code Station <b>".$dataColCsv.'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+1).": ".join(';', $data)."<br>"; 
                       }
                   }
                   // on adapte les format des long et lat
                   if ($field == 'station.latDegDec' || $field == 'station.longDegDec') {$dataColCsv = ($dataColCsv != '') ?  floatval(str_replace(",", ".", $dataColCsv)): null;}
                   if ($field == 'station.altitudeM') {$dataColCsv = ($dataColCsv != '') ?  intval(str_replace(",", ".", $dataColCsv)) : null; }
                   // on enregistre la valeurs du champ
                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                   $entity->$method($dataColCsv);                     
               }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) ex. : station.commune(commune.nom_commune)
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   //  ->>> A AJOUTER ICI les cas avec plusieurs champs à prendre en compte pour la table liées (ex. referentiel_taxon.ordre|familia|genus|species)
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                           case 'Commune':
                               if ($dataColCsv != '') {
                                   $CodeCommune = $dataColCsv ;
                                   if (array_key_exists($CodeCommune,$list_new_commune)) {  // on teste pour voir si la commune n'a pas déjà été créée
                                       $commune = $list_new_commune[$CodeCommune];
                                   } else {
                                       $commune = new \Bbees\E3sBundle\Entity\Commune();      
                                       $commune->setCodeCommune($CodeCommune);
                                       // analyse du code et set des autres champs : nom_commune, nom_region, pays
                                       $list_field_commune = explode("|", $dataColCsv);
                                       $commune->setNomCommune(str_replace("_"," ",$list_field_commune[0]));
                                       $commune->setNomRegion(str_replace("_"," ",$list_field_commune[1]));
                                       $pays_fk = $em->getRepository("BbeesE3sBundle:Pays")->findOneBy(array("codePays" => $list_field_commune[2])); 
                                       if($pays_fk === NULL){ 
                                            $message .= "ERROR : le code_pays <b>" .$list_field_commune[2]. '</b> n existe pas dans la table Pays <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                       }
                                       $commune->setPaysFk($pays_fk);
                                       $em->persist($commune);                                       
                                       $list_new_commune[$CodeCommune] = $commune; // on conserve en mémoire les communes créée
                                   }
                                   $foreign_fieldName = $foreign_table."Fk";
                                   $method =  $importFileCsvService->TransformNameForSymfony($foreign_fieldName,'set');
                                   $entity->$method($commune); 
                                   //var_dump($CodeCommune); var_dump($list_new_commune); exit;
                               }
                               break;
                        case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                        default:
                              $message .= "ERROR : ".$foreign_table."-".$foreign_field ." <b>" . $dataColCsv. '</b> <br> ligne '. (string)($l+1) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                       if ($foreign_table == 'Pays') { // on memorise les informations sur le pays
                           $code_pays = $foreign_record->getCodePays();
                           $pays_record = $foreign_record;
                       }
                   }
               }  
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);                
       }      
       // A FAIRE : ajouter les champ commune.nom_commune +commune.nom_region

       if ($message ==''){
           try {
               $flush = $em->flush();
               return "Import de ". count($csvData). " données réussi ! </br>".$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
               return 'probleme de FLUSH : </br>'.strval($e);
           }          
       } else {
           return $info.'</br>'.$message;
       }
    }
    
    /**
    *  importCSVDataSequenceAssemblee($fichier)
    * importation des données csv : template 
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataSequenceAssemblee($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"sequence_assemblee", "sequence_assemblee.code_sqc_ass")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template sequence_assemblee </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine       
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            # Enregistrement des données de collecte
            $entity = new \Bbees\E3sBundle\Entity\SequenceAssemblee();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            foreach($columnByTable["sequence_assemblee"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // ex. station.codeStation  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'sequence_assemblee.code_sqc_ass') { // On teste pour savoir si le code_sqc_ass a déja été créé. 
                        $record_entity = $em->getRepository("BbeesE3sBundle:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= "ERROR : le code code_sqc_ass <b>".$dataColCsv."</b> existe déjà dans la bdd. <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // on adapte les formats
                    if ($ColCsv == 'sequence_assemblee.date_creation_sqc_ass' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                   // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $em->persist($entity);
            
            # Enregistrement du sequence_assemblee_est_realise_par                     
            foreach($columnByTable["sequence_assemblee_est_realise_par"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\SequenceAssembleeEstRealisePar();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       } 
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }  
    
            # Enregistrement de SqcEstPublieDans                    
            foreach($columnByTable["sqc_est_publie_dans"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\SqcEstPublieDans();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }  
            
            # Enregistrement de EstAligneEtTraite    (liaison aux chromatogramme)                
            foreach($columnByTable["est_aligne_et_traite"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\EstAligneEtTraite();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }  
            
            # Enregistrement du EspeceIdentifiee 
            $entityRel = new \Bbees\E3sBundle\Entity\EspeceIdentifiee();
            $entityEspeceIdentifie = $entityRel;
            $method = "setSequenceAssembleeFk";
            $entityRel->$method($entity);
             foreach($columnByTable["espece_identifiee"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
                if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                    $varfield = explode(".", $field)[1];
                    // on adapte les formats
                    if ($ColCsv == 'espece_identifiee.date_identification' ) {
                        // ajuste le date incomplete du type m/Y ou Y en 01/m/Y ou 01/01/Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= "ERROR : espece identifiee :le format de date n est pas valide  <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= "ERROR : le format de date n est pas correcte <b>".$data[$ColCsv]."-".$dataColCsv."</b> du type d/m/Y: <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // on enregistre la valeurs du champ
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
                    $val_foreign_field = trim($dataColCsv);
                    // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                               $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entityRel->$method($foreign_record);                               
                    }  
                }    
             }
             $entityRel->setDateCre($DateImport);
             $entityRel->setDateMaj($DateImport); 
             $em->persist($entityRel);
            
            # Enregistrement du EstIdentifiePar                     
            foreach($columnByTable["est_identifie_par"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  // {personne,nom_personne}
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Personne
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomPersonne
               $tab_foreign_field = explode("$",$dataColCsv); // On  transforme le contenu du champ dans un tableau
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \Bbees\E3sBundle\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= "ERROR : ".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                           $entityRel->$method($foreign_record);                               
                       }  
                       $entityRel->setDateCre($DateImport);
                       $entityRel->setDateMaj($DateImport); 
                       $em->persist($entityRel);
                   }
               } 
            }    
                         
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataReferentielTaxon($fichier)
    * importation des données csv : template 
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataReferentielTaxon($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"referentiel_taxon", "referentiel_taxon.taxname")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template referentiel_taxon </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine    
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            $entity = new \Bbees\E3sBundle\Entity\ReferentielTaxon();
            if (array_key_exists("referentiel_taxon" ,$columnByTable)) {
               foreach($columnByTable["referentiel_taxon"] as $ColCsv){  
                  $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $varfield = explode(".", $field)[1];
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                   if($ColCsv == 'referentiel_taxon.taxname') { // On teste pour savoir si le taxname a déja été créé. 
                       $record_entity = $em->getRepository("BbeesE3sBundle:ReferentielTaxon")->findOneBy(array("taxname" => $dataColCsv)); 
                       if($record_entity !== NULL){ 
                          $message .= "ERROR : le Tax Name <b>".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                       }
                    }
                    if ($ColCsv == 'referentiel_taxon.validity') { 
                        if ($dataColCsv != '') {
                            if ($dataColCsv == 'YES' || $dataColCsv == 'NO') {
                                $dataColCsv = ($dataColCsv == 'YES') ? 1 : 0; 
                            } else {
                                $message .= "ERROR : le contenu de ".$ColCsv." n est pas valide  <b>".$data[$ColCsv]."</b> # YES/NO : <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";    
                            }
                        }
                    }
                    if ($dataColCsv === '') $dataColCsv = NULL; // si il n'y a pas de valeur on initialise la valeur a NULL
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);   // on enregistre la valeurs du champ   
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $em->persist($entity);     
            } else {
                return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
                exit;
            }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return "Import de ". count($csvData). " données  ! </br>".$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
               return 'probleme de FLUSH : </br>'.strval($e);
           }          
       } else {
           return $info.'</br>'.$message;
       }
    }
     
    /**
    *  importCSVDataVoc($fichier)
    * importation des données csv : template voc
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataVoc($fichier)
    {
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"voc", "voc.code")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template voc </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine    
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;
            $entity = new \Bbees\E3sBundle\Entity\Voc();
            if (array_key_exists("voc" ,$columnByTable)) {
               foreach($columnByTable["voc"] as $ColCsv){  
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation                
                   $varfield = explode(".", $field)[1];
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                  // On teste pour savoir si le code_voc n existe pas deja pour ce parent 
                  if ($ColCsv == 'voc.parent') {
                      $record_voc = $em->getRepository("BbeesE3sBundle:Voc")->findOneBy(array("parent" => $dataColCsv, "code" => $code));  
                      if($record_voc !== NULL){ 
                          $message .= "ERROR : le Vocabulaire : <b>".$code." / ".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                      }
                   } 
                  $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                  $entity->$method($dataColCsv);   // on enregistre la valeurs du champ   
                  if ($ColCsv == 'voc.code' ) {$code = $dataColCsv;}
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $em->persist($entity);     
            } else {
                return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
                exit;
            }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return "Import de ". count($csvData). " données  ! </br>".$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
               return 'probleme de FLUSH : </br>'.strval($e);
           }          
       } else {
           return $info.'</br>'.$message;
       }
    }
  
    /**
    *  importCSVDataPersonne($fichier)
    * importation des données csv : template personne
    * $fichier : le path vers le fichiers csv downloader
    */ 
    public function importCSVDataPersonne($fichier)
    {     
        $importFileCsvService = $this->importFileCsv; // récuperation du service ImportFileCsv
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Recupération des champs du CSv sous la forme d'un tableau / Table
        if(!$importFileCsvService->testNameColumnCSV($columnByTable,"personne", "personne.nom_personne")) { 
            return("ERROR : <b> le fichier downloader ne contient pas les bonnes collonnes du template personne </b>");             
            exit;
        }
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine     
        $compt = 0;
        $message = '';
        $info = 'Date d import : '.$DateImport->format('Y-m-d H:i:s');  
        foreach($csvData as $l => $data){ // 1- Traitement des données ligne à ligne ($l)
            $compt++;         
            # Enregistrement des données de Personne
            $entity = new \Bbees\E3sBundle\Entity\Personne();    
            // on boucle sur l'ensemble des colonnes dont le nom commence par collecte.
            if (array_key_exists("personne" ,$columnByTable)) {
                foreach($columnByTable["personne"] as $ColCsv){  
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); // station.codeStation  
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .= "ERROR : Des caractères spéciaux sont à supprimer dans <b>" .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                    $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag pour savoir si 1) il s'agit d'une clés étrangère 
                    if (!$flag_foreign) { // cas ou il n'esiste pas de parentheses = pas de table liée
                        $varfield = explode(".", $field)[1];
                        if($ColCsv  == 'personne.nom_personne') { // On teste pour savoir si le code_programme a déja été créé. 
                            $record_entity = $em->getRepository("BbeesE3sBundle:Personne")->findOneBy(array("nomPersonne" => $dataColCsv)); 
                            if($record_entity !== NULL){ 
                               $message .= "ERROR : le nom de Personne <b>".$data[$ColCsv].'</b> existe déjà dans la bdd. <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                            }
                        }
                        // on adapte les formats
                        // on enregistre la valeurs du champ
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entity->$method($dataColCsv);                     
                    }
                    if($flag_foreign){ // cas d'une foreign key (cas ou il existe des parenthèses dans le nom de champ) 
                        $varfield = explode(".", strstr($field, '(', true))[1];
                        $linker = explode('.', trim($foreign_content[0],"()"));  // {commune,nom_commune}
                        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); // Commune
                        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); // nomCommune
                        if (!is_null($dataColCsv)) { // on accept les valeur NULL ou '' et on ne traite que les valuer NON NULL
                            // On teste pour savoir si il s'agit d'une foreign key de la table Voc de la forme:  parentVocFk ou parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                            } else {
                               $foreign_record = $em->getRepository("BbeesE3sBundle:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case 'etablissement' :
                                        break;
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= "ERROR : ".$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= "ERROR FIELD  ".$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b> INCONNU <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                }
                             } else {
                                $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                                $entity->$method($foreign_record);                               
                            }  
                        }
                    }                
                }
                $entity->setDateCre($DateImport);
                $entity->setDateMaj($DateImport);
                $em->persist($entity);
            } else {
               return("ERROR : <b> le fichier ne contient pas les bonnes collonnes  </b>");
               exit;
            }
        }  
        # FLUSH si il n'y a pas de message d'erreur
        if ($message ==''){
            try {
                $flush = $em->flush();
                return "Import de ". count($csvData). " données  ! </br>".$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                return 'probleme de FLUSH : </br>'.strval($e);
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

}
