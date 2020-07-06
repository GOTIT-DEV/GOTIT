<?php

/*
 * This file is part of the E3sBundle.
 *
 * Authors : see information concerning authors of GOTIT project in file AUTHORS.md
 *
 * E3sBundle is free software : you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * E3sBundle is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with E3sBundle.  If not, see <https://www.gnu.org/licenses/>
 * 
 */

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;
use App\Services\Core\ImportFileCsv;
use App\Entity\Motu;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;


/**
* Service ImportFileE3s
* @author Philippe Grison  <philippe.grison@mnhn.fr>
*/
class ImportFileE3s 
{
    private $entityManager;
    private $importFileCsv;
    private $translator;
    
    /**
    *  __construct(EntityManager $manager,ImportFileCsv $importFileCsv )
    * $manager : service manager service of Doctrine ( @doctrine.orm.entity_manager )
    * $importFileCsv : CSV file import service 
    */ 
    public function __construct(EntityManagerInterface $manager,ImportFileCsv $importFileCsv, Translator $translator) {
       $this->entityManager = $manager ;
       $this->importFileCsv = $importFileCsv ;
       $this->translator =   $translator;
    }

    /**
    *  importCSVDataAdnDeplace($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is DNA_move
    */ 
    public function importCSVDataAdnDeplace($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataAdnRange = $importFileCsvService->readCSV($fichier);      
        //$columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Retrieve CSV fields as a table    
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataAdnRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_adn = $em->getRepository("App:Adn")->createQueryBuilder('dna')
            ->where('dna.codeAdn  LIKE :dna_code')
            ->setParameter('dna_code', $data["dna_code"])
            ->getQuery()
            ->getResult();
            $flagAdn = count($query_adn);
            if ($flagAdn == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["dna_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagAdn && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataAdnRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message; 
            }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataAdnRange($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is DNA_store
    */ 
    public function importCSVDataAdnRange($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataAdnRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataAdnRange); // Retrieve CSV fields as a table     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataAdnRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_adn = $em->getRepository("App:Adn")->createQueryBuilder('dna')
            ->where('dna.codeAdn  LIKE :dna_code')
            ->setParameter('dna_code', $data["dna_code"])
            ->getQuery()
            ->getResult();
            $flagAdn = count($query_adn);
            if ($flagAdn == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["dna_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= $this->translator->trans("importfileService.ERROR no box code").'<b> : '.$data["collection_slide_code"]." </b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagAdn && $flagBoite && $flagBoiteAffecte) { 
                if ($query_adn[0]->getBoiteFk() != null) {
                     $message .= $this->translator->trans('importfileService.ERROR dna already store').'<b> : '.$data["dna_code"].'</b> / '.$query_adn[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataAdnRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

  
    
    /**
    *  importCSVDataIndividuLameDeplace($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is slide_move
    */ 
    public function importCSVDataIndividuLameDeplace($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Retrieve CSV fields as a table     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataIndividuLamelRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_lame = $em->getRepository("App:IndividuLame")->createQueryBuilder('lame')
            ->where('lame.codeLameColl  LIKE :collection_slide_code')
            ->setParameter('collection_slide_code', $data["collection_slide_code"])
            ->getQuery()
            ->getResult();
            $flagLame = count($query_lame);
            if ($flagLame == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["collection_slide_code"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLame && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataIndividuLamelRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataIndividuLameRange($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is slide_store
    */ 
    public function importCSVDataIndividuLameRange($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataIndividuLamelRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataIndividuLamelRange); // Retrieve CSV fields as a table     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataIndividuLamelRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_lame = $em->getRepository("App:IndividuLame")->createQueryBuilder('lame')
            ->where('lame.codeLameColl  LIKE :collection_slide_code')
            ->setParameter('collection_slide_code', $data["collection_slide_code"])
            ->getQuery()
            ->getResult();
            $flagLame = count($query_lame);
            if ($flagLame == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["collection_slide_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= $this->translator->trans("importfileService.ERROR no box code").'<b> : '.$data["collection_slide_code"]." </b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLame && $flagBoite && $flagBoiteAffecte) { 
                if ($query_lame[0]->getBoiteFk() != null) {
                     $message .= $this->translator->trans('importfileService.ERROR slide already store').'<b> : '.$data["collection_slide_code"].'</b> / '.$query_lame[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataIndividuLamelRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

  
    
    /**
    *  importCSVDataLotMaterielDeplace($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is biological_material_move
    */ 
    public function importCSVDataLotMaterielDeplace($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Retrieve CSV fields as a table     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataLotMaterielRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_lot = $em->getRepository("App:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :internal_biological_material_code')
            ->setParameter('internal_biological_material_code', $data["internal_biological_material_code"])
            ->getQuery()
            ->getResult();
            $flagLot = count($query_lot);
            if ($flagLot == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["internal_biological_material_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }               
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLot && $flagBoite) { 
                if ( $flagBoiteAffecte ) { 
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataLotMaterielRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataLotMaterielRange($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is biological_material_store
    */ 
    public function importCSVDataLotMaterielRange($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataLotMaterielRange = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielRange); // Retrieve CSV fields as a table    
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataLotMaterielRange as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_lot = $em->getRepository("App:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :internal_biological_material_code')
            ->setParameter('internal_biological_material_code', $data["internal_biological_material_code"])
            ->getQuery()
            ->getResult();
            $flagLot = count($query_lot);
            if ($flagLot == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["internal_biological_material_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            $flagBoite = 1;
            $flagBoiteAffecte = 0;
            if($data["box_code"] != null || $data["box_code"] != '') {
                $flagBoiteAffecte = 1;
                $query_boite = $em->getRepository("App:Boite")->createQueryBuilder('storage_box')
                ->where('storage_box.codeBoite LIKE :box_code')
                ->setParameter('box_code', $data["box_code"])
                ->getQuery()
                ->getResult(); 
                $flagBoite = count($query_boite);
            }     
            if ($flagBoiteAffecte == 0) $message .= $this->translator->trans("importfileService.ERROR no box code for material").'<b> : '.$data["internal_biological_material_code"]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagBoiteAffecte && $flagBoite == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["box_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            if ($flagLot && $flagBoite && $flagBoiteAffecte) { 
                if ($query_lot[0]->getBoiteFk() != null) {
                     $message .= $this->translator->trans('importfileService.ERROR lot already store').'<b> : '.$data["internal_biological_material_code"].'</b> / '.$query_lot[0]->getBoiteFk()->getCodeBoite().' <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";                                    
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataLotMaterielRange).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataLotMaterielPublie($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is source_attribute_to_lot
    */ 
    public function importCSVDataLotMaterielPublie($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataLotMaterielPublie = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataLotMaterielPublie); // Retrieve CSV fields as a table    
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvDataLotMaterielPublie as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_lot = $em->getRepository("App:LotMateriel")->createQueryBuilder('lot')
            ->where('lot.codeLotMateriel LIKE :internal_biological_material_code')
            ->setParameter('internal_biological_material_code', $data["internal_biological_material_code"])
            ->getQuery()
            ->getResult();
            $query_source = $em->getRepository("App:Source")->createQueryBuilder('source')
            ->where('source.codeSource LIKE :source_code')
            ->setParameter('source_code', $data["source.source_code"])
            ->getQuery()
            ->getResult();                
            if (count($query_lot) == 0 || count($query_source) == 0) {
                if (count($query_lot) == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["internal_biological_material_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                if (count($query_source) == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["source.source_code"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            } else {
                $query_lepd = $em->getRepository("App:LotEstPublieDans")->createQueryBuilder('lepd')
                        ->where('lepd.lotMaterielFk = :id_lot')
                        ->setParameter('id_lot', $query_lot[0]->getId())
                        ->andwhere('source.codeSource = :source_code')
                        ->setParameter('source_code', $data["source.source_code"])
                        ->leftJoin('App:Source', 'source', 'WITH', 'lepd.sourceFk = source.id')
                        ->getQuery()
                        ->getResult();              
                if (count($query_lepd) != 0 ) {
                    $message .= $this->translator->trans('importfileService.ERROR lot already publish').'<b> : '.$data["source.source_code"].' / '.$data["internal_biological_material_code"].' </b><br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataLotMaterielPublie).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataSqcAssembleePublie($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is source_attribute_to_sequence
    */ 
    public function importCSVDataSqcAssembleePublie($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataSqcAssembleePublie = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataSqcAssembleePublie); // Retrieve CSV fields as a table     
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s'); 
        foreach($csvDataSqcAssembleePublie as $l => $data){ // 1- Line-to-line data processing ($ l)
            $query_sa = $em->getRepository("App:SequenceAssemblee")->createQueryBuilder('sa')
            ->where('sa.codeSqcAss LIKE :internal_sequence_code')
            ->setParameter('internal_sequence_code', $data["internal_sequence_code"])
            ->getQuery()
            ->getResult();
            $query_source = $em->getRepository("App:Source")->createQueryBuilder('source')
            ->where('source.codeSource LIKE :source_code')
            ->setParameter('source_code', $data["source.source_code"])
            ->getQuery()
            ->getResult();                
            if (count($query_sa) == 0 || count($query_source) == 0) {
                if (count($query_sa) == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["internal_sequence_code"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                if (count($query_source) == 0) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data["source.source_code"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
            } else {
                $query_lepd = $em->getRepository("App:SqcEstPublieDans")->createQueryBuilder('sepd')
                        ->where('sepd.sequenceAssembleeFk = :id_sa')
                        ->setParameter('id_sa', $query_sa[0]->getId())
                        ->getQuery()
                        ->getResult();              
                if (count($query_lepd) != 0 ||  $query_sa[0]->getAccessionNumber() != '') {
                    if (count($query_lepd) != 0) $message .= $this->translator->trans('importfileService.ERROR sqc already publish').'<b> : '.$data["source.source_code"].' / '.$data["internal_sequence_code"].' </b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                    if ( $query_sa[0]->getAccessionNumber() != '')  $message .= $this->translator->trans('importfileService.ERROR assession number already assign').'<b> : '.$query_sa[0]->getAccessionNumber().' / '.$data["internal_sequence_code"].' </b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>";    
                } else {
                    $method = "setAccessionNumber";
                    $query_sa[0]->$method($data["internal_sequence_accession_number"]);
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
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataSqcAssembleePublie).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataSource($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is source
    */ 
    public function importCSVDataSource($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');       
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Source();     
            foreach($columnByTable["source"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    //V2 varfield = explode(".", $ColCsv)[1];
                    if($ColCsv == 'source.source_code') {  
                        $record_entity = $em->getRepository("App:Source")->findOneBy(array("codeSource" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    
                    // control and standardization of field formats
                    if ($ColCsv == 'source.source_year' && !is_null($dataColCsv)) {$dataColCsv = intval(str_replace(",", ".", $dataColCsv));}
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                     //V2 $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set',2);
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    //V2 $varfield = explode(".", strstr($ColCsv, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    //V2 $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table',2);
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    //V2 $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field', 2); 
                    if (!is_null($dataColCsv)) {
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." [" . $data[$ColCsv]. ']</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break;   
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b>  <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
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
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);

            $em->persist($entity);
            
            # Record of  SourceAEteIntegrePar                    
             foreach($columnByTable["source_is_entered_by"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\SourceAEteIntegrePar();
                        $method = "setSourceFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataPcrChromato($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : ! the template of csv file to import is NOT YET SUPPORTED in V1.1
    */ 
    public function importCSVDataPcrChromato($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_pcr = array();        
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;   
            $flag_new_pcr = 1;
            # Records of pcr data
            $entity = new \App\Entity\Pcr();    
            // 
            foreach($columnByTable["pcr"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'pcr.pcr_code') { 
                        $record_entity = $em->getRepository("App:Pcr")->findOneBy(array("codePcr" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } 
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'pcr.pcr_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y 
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }
                              break;
                            default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            // management of the pcr which gave rise to several chromato (n lines)
            if (array_key_exists($data['pcr.pcr_code'], $list_new_pcr)) {
               $flag_new_pcr = 0;
               $entity = $list_new_pcr[$data['pcr.pcr_code']];
            } else {
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $entity->setUserCre($userId);
               $entity->setUserMaj($userId);
               $em->persist($entity);
               $list_new_pcr[$data['pcr.pcr_code']] = $entity ;
            }

            # Record of PcrEstRealisePar 
            if ($flag_new_pcr) {
                foreach($columnByTable["pcr_is_done_by"] as $ColCsv){   
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                   if($flag_foreign && trim($dataColCsv) != ''){ 
                       foreach($tab_foreign_field as $val_foreign_field){ 
                           $val_foreign_field = trim($val_foreign_field);
                           $entityRel = new \App\Entity\PcrEstRealisePar();
                           $method = "setPcrFk";
                           $entityRel->$method($entity);
                           //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                           $varfield_parent = strstr($varfield, 'Voc', true);
                           if (!$varfield_parent) {
                             $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                           } else {
                              $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                           }  
                           if($foreign_record === NULL){  
                              switch ($foreign_table) {
                                 case "Voc":
                                    $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                 break;
                                   default:
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                               }
                            } else {
                               $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            $entityRel = new \App\Entity\Chromatogramme();
            $method = "setPcrFk";
            $entityRel->$method($entity);             
            foreach($columnByTable["chromatogram"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    if($ColCsv == 'chromatogram.chromatogram_code') { // On teste pour savoir si le chromatogram.chromatogram_code a déja été créé. 
                        $record_entity = $em->getRepository("App:Chromatogramme")->findOneBy(array("codeChromato" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign && $ColCsv != 'chromatogram.pcr_fk(pcr.pcr_code)'){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }                              break;
                            default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

 
    /**
    *  importCSVDataPcr($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import PCR
    */ 
    public function importCSVDataPcr($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');       
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;   
            # Record PCR data
            $entity = new \App\Entity\Pcr();    
            foreach($columnByTable["pcr"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    if($ColCsv == 'pcr.pcr_code') { 
                        $record_entity = $em->getRepository("App:Pcr")->findOneBy(array("codePcr" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } 
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'pcr.pcr_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }
                              break;
                            default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["pcr_is_done_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\PcrEstRealisePar();
                       $method = "setPcrFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                             case "Voc":
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                             break;
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataChromato(array $csvData)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is chromatogram
    */ 
    public function importCSVDataChromato($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');           
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;   
            # Record of the chromatogram   
            $entity = new \App\Entity\Chromatogramme();            
            foreach($columnByTable["chromatogram"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'chromatogram.chromatogram_code') {  
                        $record_entity = $em->getRepository("App:Chromatogramme")->findOneBy(array("codeChromato" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                              if ($data[$ColCsv] == '')  {
                                  $foreign_record = NULL;
                              }  else {
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                              }                             
                              break;
                            default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataAdn($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is DNA
    */ 
    public function importCSVDataAdn($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            #
            $entity = new \App\Entity\Adn();    
            // 
            foreach($columnByTable["dna"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'dna.dna_code') { 
                        $record_entity = $em->getRepository("App:Adn")->findOneBy(array("codeAdn" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    
                    // control and standardization of field formats
                    if ($ColCsv == 'dna.dna_concentration' && !is_null($dataColCsv)) {$dataColCsv = floatval(str_replace(",", ".", $dataColCsv));}
                    // test of the date format
                    if ($ColCsv == 'dna.dna_extraction_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if (!is_null($dataColCsv)){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    if (!is_null($dataColCsv)) {
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break;   
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b>  <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
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
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of AdnEstRealisePar                     
             foreach($columnByTable["dna_is_extracted_by"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\AdnEstRealisePar();
                        $method = "setAdnFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataProgramme($fichier, $userId = null)
    * $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is program
    */ 
    public function importCSVDataProgramme($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();            
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Programme();
            if (array_key_exists("program" ,$columnByTable)) {
               foreach($columnByTable["program"] as $ColCsv){ 
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }              
                   $varfield = explode(".", $field)[1];
                   if($field == 'program.codeProgramme') { 
                       $record_entity = $em->getRepository("App:Programme")->findOneBy(array("codeProgramme" => $dataColCsv)); 
                       if($record_entity !== NULL){ 
                          $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b><br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                       }
                   }
                   if ($dataColCsv === '') $dataColCsv = NULL; // if there is no value, initialize the value to NULL
                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                   $entity->$method($dataColCsv);   // save the values ​​of the field                   
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $entity->setUserCre($userId);
               $entity->setUserMaj($userId);
               $em->persist($entity);  
           } else {
              return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
              exit;
           }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;            }          
       } else {
           return $info.'</br>'.$message;
       }
    }
      
    /**
    *  importCSVDataCollecte($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is sampling
    */ 
    public function importCSVDataCollecte($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // appel du manager de Doctrine
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array();      
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Collecte();   
            foreach($columnByTable["sampling"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($field == 'sampling.codeCollecte') { 
                        $record_entity = $em->getRepository("App:Collecte")->findOneBy(array("codeCollecte" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$dataColCsv."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats 
                    if ($ColCsv == 'sampling.specific_conductance' || $ColCsv == 'sampling.temperature') {
                        if ($dataColCsv != '') {
                            $dataColCsv = floatval(str_replace(",", ".", $dataColCsv));
                            if ($dataColCsv == '') $message .= $this->translator->trans('importfileService.ERROR bad float format').'<b> : '.$data[$ColCsv]."</b>  <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'sampling.sampling_duration' ) { 
                         if ($dataColCsv != '') {
                            $dataColCsv = intval(str_replace(",", ".", $dataColCsv)); 
                         } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'sampling.sample_status') { 
                        if ($dataColCsv != '') {
                            if ($dataColCsv == 'OUI' || $dataColCsv == 'YES' || $dataColCsv == '1') {$dataColCsv = 1 ;}
                            if ($dataColCsv == 'NON' || $dataColCsv == 'NO' || $dataColCsv == '0') {$dataColCsv = 0 ;}
                            if ($dataColCsv !== 1 && $dataColCsv !== 0) {
                                $message .= $this->translator->trans('importfileService.ERROR bad data OUI-NON').'<b> : '.$ColCsv."/ ".$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";    
                            }
                        } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    if ($ColCsv == 'sampling.date_collecte' ) {
                        if ($dataColCsv != ''){
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) ex. : site.municipality(municipality.municipality_name)
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   // var_dump($varfield); var_dump($varfield_parent); var_dump($field);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));  
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                           default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').$field."-".$foreign_table.".".$foreign_field." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["has_targeted_taxa"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\ACibler();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["sample_is_fixed_with"] as $ColCsv){
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\APourFixateur();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["sampling_is_done_with_method"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\APourSamplingMethod();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["sampling_is_performed_by"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\EstEffectuePar();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["sampling_is_funded_by"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\EstFinancePar();
                        $method = "setCollecteFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field."-".$varfield."-".$varfield_parent."-".$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataLame($fichier, $userId = null)
    * $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is specimen_slide
    */ 
    public function importCSVDataLame($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;         
            # Enregistrement des données de lame
            $entity = new \App\Entity\IndividuLame();    
            // 
            foreach($columnByTable["specimen_slide"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($field); var_dump($ColCsv); 
                    if($ColCsv == 'specimen_slide.collection_slide_code') { 
                        $record_entity = $em->getRepository("App:IndividuLame")->findOneBy(array("codeLameColl" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'specimen_slide.slide_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    if (!is_null($dataColCsv)) { 
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of IndividuLameEstRealisePar                     
            foreach($columnByTable["slide_is_mounted_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\IndividuLameEstRealisePar();
                       $method = "setIndividuLameFk";
                       $entityRel->$method($entity);
                       if (!is_null($val_foreign_field) && $val_foreign_field != '') { 
                            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                            } else {
                               $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                }
                             } else {
                                $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;            }          
        } else {
            return $info.'</br>'.$message;
        }
    }
   
   /**
    *  importCSVDataIndividu($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is specimen
    */ 
    public function importCSVDataIndividu($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager     
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;           
            # Enregistrement des données de Individu
            $entity = new \App\Entity\Individu();    
            // 
            foreach($columnByTable["specimen"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'specimen.specimen_molecular_code' && !is_null($dataColCsv)) { 
                        $record_entity = $em->getRepository("App:Individu")->findOneBy(array("codeIndBiomol" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    if($ColCsv == 'specimen_morphological_code' && !is_null($dataColCsv)) { 
                        $record_entity = $em->getRepository("App:Individu")->findOneBy(array("codeIndTriMorpho" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   if (!is_null($dataColCsv)) { 
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of EspeceIdentifiee 
            $key_taxname = array_keys($columnByTable["identified_species"], "identified_species.taxon_fk(taxon.taxon_name)")[0];
            // var_dump($data[$columnByTable["identified_species"][$key_taxname]]);
            $entityEspeceIdentifie = NULL;
            if ($data[$columnByTable["identified_species"][$key_taxname]] != '') { 
                $entityRel = new \App\Entity\EspeceIdentifiee();
                $entityEspeceIdentifie = $entityRel;
                $method = "setIndividuFk";
                $entityRel->$method($entity);
                foreach($columnByTable["identified_species"] as $ColCsv){ 
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   if ($dataColCsv === '') $dataColCsv = NULL;
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                   if (!$flag_foreign) { 
                       $varfield = explode(".", $field)[1];
                       // control and standardization of field formats
                       if ($ColCsv == 'identified_species.identification_date' ) {
                           // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                           if (!is_null($dataColCsv)){
                               if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                               if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                               $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                               if (!$eventDate) {
                                   $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                   $dataColCsv = NULL; 
                               } else {
                                   $tabdate = explode("/",$dataColCsv);
                                   if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                       $dataColCsv = date_format($eventDate, 'Y-m-d');
                                       $dataColCsv = new \DateTime($dataColCsv);
                                   } else {
                                       $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                       $dataColCsv = NULL; 
                                   }
                               }
                               //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                           } 
                       }
                       // save the values ​​of the field
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entityRel->$method($dataColCsv);                     
                   }
                   if($flag_foreign){ 
                       $varfield = explode(".", strstr($field, '(', true))[1];
                       $linker = explode('.', trim($foreign_content[0],"()"));  
                       $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                       $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                       if (!is_null($dataColCsv)) { 
                            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                            } else {
                               $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
                $entityRel->setUserCre($userId);
                $entityRel->setUserMaj($userId);
                $em->persist($entityRel);
            }
            
            # Record of EstIdentifiePar    
            if (!is_null($entityEspeceIdentifie)) { 
                foreach($columnByTable["species_is_identified_by"] as $ColCsv){  
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   }
                   if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                   if($flag_foreign && !is_null($dataColCsv)){ 
                       foreach($tab_foreign_field as $val_foreign_field){
                           $val_foreign_field = trim($val_foreign_field);
                           $entityRel = new \App\Entity\EstIdentifiePar();
                           $method = "setEspeceIdentifieeFk";
                           $entityRel->$method($entityEspeceIdentifie);
                           if (!is_null($val_foreign_field) && $val_foreign_field != '') { 
                               //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                               $varfield_parent = strstr($varfield, 'Voc', true);
                               if (!$varfield_parent) {
                                 $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                               } else {
                                  $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                               }  
                               if($foreign_record === NULL){  
                                  switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                    break; 
                                       default:
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                   }
                                } else {
                                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataBoite($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is box
    */ 
    public function importCSVDataBoite($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager    
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');          
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Boite();
            if (array_key_exists("storage_box" ,$columnByTable)) {
               foreach($columnByTable["storage_box"] as $ColCsv){  
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                   $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key  
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                   if (!$flag_foreign) { 
                      $varfield = explode(".", $field)[1];
                      if($field == 'storage_box.codeBoite') { 
                          $record_entity = $em->getRepository("App:Boite")->findOneBy(array("codeBoite" => $dataColCsv)); 
                          if($record_entity !== NULL){ 
                             $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                          }
                      }
                      if ($dataColCsv === '') $dataColCsv = NULL; // if there is no value, initialize the value to NULL
                      $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                      $entity->$method($dataColCsv);   // save the values ​​of the field                        
                   }
                   if($flag_foreign){ 
                       $varfield = explode(".", strstr($field, '(', true))[1];
                       $linker = explode('.', trim($foreign_content[0],"()"));  
                       $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                       $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field." parent=".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
              return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
              exit;
           }  
        }        
        if ($message ==''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }   
    
    /**
    *  importCSVDataLotMateriel($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is internal_biological_material
    */ 
    public function importCSVDataLotMateriel($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager        
        // traitement ligne par ligne du fichier csv
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            
            #
            $entity = new \App\Entity\LotMateriel();    
            // 
            foreach($columnByTable["internal_biological_material"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'internal_biological_material.internal_biological_material_code') { 
                        $record_entity = $em->getRepository("App:LotMateriel")->findOneBy(array("codeLotMateriel" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'internal_biological_material.internal_biological_material_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    if ($ColCsv == 'internal_biological_material.internal_biological_material_status') { 
                        if ($dataColCsv != '') {
                            if ($dataColCsv == 'OUI' || $dataColCsv == 'YES' || $dataColCsv == '1') {$dataColCsv = 1 ;}
                            if ($dataColCsv == 'NON' || $dataColCsv == 'NO' || $dataColCsv == '0') {$dataColCsv = 0 ;}
                            if ($dataColCsv !== 1 && $dataColCsv !== 0) {
                                $message .= $this->translator->trans('importfileService.ERROR bad data OUI-NON').'<b> : '.$ColCsv."/ ".$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";    
                            }
                        } else {
                            $dataColCsv = NULL; 
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Boite":
                                // la valeur NULL est permise
                                break;
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["Internal_biological_material_is_treated_by"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\LotMaterielEstRealisePar();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["internal_biological_material_is_published_in"] as $ColCsv){   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\LotEstPublieDans();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["composition_of_internal_biological_material"] as $ColCsv){  
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }              
                if ($ColCsv == 'composition_of_internal_biological_material.internal_biological_material_composition_comments' ) {
                    $commentaireCompoLotMateriel = $dataColCsv;
                }
                
                if ($ColCsv == 'composition_of_internal_biological_material.number_of_specimens+specimen_type_voc_fk(vocabulary.code)' ) {
                    $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\CompositionLotMateriel();
                        $method = "setLotMaterielFk";
                        $entityRel->$method($entity);
                        $entityRel->setCommentaireCompoLotMateriel($commentaireCompoLotMateriel);
                        // We split the information into two variable $number_of_specimens & $type_individu
                        $nb_individu = (int) preg_replace('/[^0-9]/','',$val_foreign_field); 
                        $type_individu =  preg_replace('/[0-9]/','',$val_foreign_field); 
                        $type_individu = trim($type_individu);
                        if ($nb_individu == 0)  $nb_individu = NULL;
                        $entityRel->setNbIndividus($nb_individu);
                        $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $type_individu, "parent" => 'typeIndividu'));  
                        if($foreign_record === NULL){  
                           switch ("Voc") {
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '. $type_individu. '</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
             foreach($columnByTable["identified_species"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // control and standardization of field formats
                    if ($ColCsv == 'identified_species.identification_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    $val_foreign_field = trim($dataColCsv);
                    //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                               $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["species_is_identified_by"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                $varfield = explode(".", strstr($field, '(', true))[1];
                $linker = explode('.', trim($foreign_content[0],"()"));  
                $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
                if($flag_foreign && trim($dataColCsv) != ''){ 
                    foreach($tab_foreign_field as $val_foreign_field){ 
                        $val_foreign_field = trim($val_foreign_field);
                        $entityRel = new \App\Entity\EstIdentifiePar();
                        $method = "setEspeceIdentifieeFk";
                        $entityRel->$method($entityEspeceIdentifie);
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                            }
                         } else {
                            $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }    

    /**
    *  importCSVDataMotuFile($fichier, ,\App\Entity\Motu $motu, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is MOTU
    */ 
    public function importCSVDataMotuFile($fichier,\App\Entity\Motu $motu, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvDataMotu = $importFileCsvService->readCSV($fichier);      
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvDataMotu); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        //var_dump($csvData);
        $entity = $motu;            
        foreach($csvDataMotu as $l2 => $data2){ // 1- Line-to-line data processing ($ l)
                $flagSeq = 0 ;
                $flagSeqExt = 0 ;
                $record_entity_sqc_ass = $em->getRepository("App:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"])); 
                if($record_entity_sqc_ass !== NULL){ 
                    $flagSeq = 1 ;
                    $entityRel = new \App\Entity\Assigne();
                    $method = "setMotuFk";
                    $entityRel->$method($entity);    
                    $method = "setSequenceAssembleeFk";
                    $entityRel->$method($record_entity_sqc_ass);
                    $method = "setNumMotu";
                    $entityRel->$method($data2["motu_number"]);
                    $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
                    if($foreign_record === NULL) $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '. $data2["code_methode_motu"]. '</b>  <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                    $method = "setMethodeMotuVocFk";
                    $entityRel->$method($foreign_record);
                }
                $record_entity_sqc_ass_ext = $em->getRepository("App:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"])); 
                if($record_entity_sqc_ass_ext !== NULL){ 
                    $flagSeqExt = 1 ;
                    $entityRel = new \App\Entity\Assigne();
                    $method = "setMotuFk";
                    $entityRel->$method($entity); 
                    $method = "setSequenceAssembleeExtFk";
                    $entityRel->$method($record_entity_sqc_ass_ext);
                    $method = "setNumMotu";
                    $entityRel->$method($data2["motu_number"]);
                    $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu")); 
                    if($foreign_record === NULL) $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '. $data2["code_methode_motu"]. '</b>  <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                    $method = "setMethodeMotuVocFk";
                    $entityRel->$method($foreign_record);
                }  
                //var_dump($l2); var_dump($flagSeqExt); var_dump($flagSeq);  var_dump($data2); 
                $entityRel->setDateCre($DateImport);
                $entityRel->setDateMaj($DateImport);
                $entityRel->setUserCre($userId);
                $entityRel->setUserMaj($userId);
                $em->persist($entityRel);
                if (!$flagSeq && !$flagSeqExt ) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data2["code_seq_ass"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                if ($flagSeq && $flagSeqExt) $message .= $this->translator->trans('importfileService.ERROR duplicate code sqc sqcext').'<b> : '.$data2["code_seq_ass"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
            }
    
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataMotu).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
    /**
    *  importCSVDataMotu($fichier, $fichier_motu)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import IS NOT YET SUPPORTED in V1.1
    */ 
    public function importCSVDataMotu($fichier, $fichier_motu)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $csvDataMotu = $importFileCsvService->readCSV($fichier_motu);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager 
        // line by line processing of the csv file        
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        //var_dump($csvData);
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            # Enregistrement des données de motu
            $entity = new \App\Entity\Motu();    
            // 
            foreach($columnByTable["motu"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                if ($dataColCsv === '') $dataColCsv = NULL;
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // we memorize the name of the file to treat it later
                    if ($ColCsv == 'motu.csv_file_name' ) {$csv_file_name = $dataColCsv ;}
                    // we adapt the date format of the column motu.motu_date
                    if ($ColCsv == 'motu.motu_date' ) {
                        if ($dataColCsv != ''){
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    if (!is_null($dataColCsv)) {
                        //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                        $varfield_parent = strstr($varfield, 'Voc', true);
                        if (!$varfield_parent) {
                        $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                        } else {
                           $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                        }  
                        if($foreign_record === NULL){  
                           switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                                default:
                                   $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. "]</b>  <br> ligne ". (string)($l+2) . ": " . join(';', $data). "<br>";
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
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of MotuEstGenerePar                     
            foreach($columnByTable["motu_is_generated_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\MotuEstGenerePar();
                       $method = "setMotuFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            if (array_key_exists("code_seq_ass", $csvDataMotu[0]) && array_key_exists("motu_number", $csvDataMotu[0]) && array_key_exists("code_methode_motu", $csvDataMotu[0])) {
                foreach($csvDataMotu as $l2 => $data2){ // 1- Line-to-line data processing ($ l)
                    $flagSeq = 0 ;
                    $flagSeqExt = 0 ;
                    $record_entity_sqc_ass = $em->getRepository("App:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $data2["code_seq_ass"])); 
                    if($record_entity_sqc_ass !== NULL){ 
                        $flagSeq = 1 ;
                        $entityRel = new \App\Entity\Assigne();
                        $method = "setMotuFk";
                        $entityRel->$method($entity);    
                        $method = "setSequenceAssembleeFk";
                        $entityRel->$method($record_entity_sqc_ass);
                        $method = "setNumMotu";
                        $entityRel->$method($data2["motu_number"]);
                        $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu"));
                        if($foreign_record === NULL) $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$data2["code_methode_motu"]. '</b>  <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                        $method = "setMethodeMotuVocFk";
                        $entityRel->$method($foreign_record);
                    }
                    $record_entity_sqc_ass_ext = $em->getRepository("App:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $data2["code_seq_ass"])); 
                    if($record_entity_sqc_ass_ext !== NULL){ 
                        $flagSeqExt = 1 ;
                        $entityRel = new \App\Entity\Assigne();
                        $method = "setMotuFk";
                        $entityRel->$method($entity); 
                        $method = "setSequenceAssembleeExtFk";
                        $entityRel->$method($record_entity_sqc_ass_ext);
                        $method = "setNumMotu";
                        $entityRel->$method($data2["motu_number"]);
                        $foreign_record = $em->getRepository("App:Voc")->findOneBy(array("code" => $data2["code_methode_motu"], "parent" => "methodeMotu")); 
                        if($foreign_record === NULL) $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$data2["code_methode_motu"]. '</b> INCONNU <br> ligne '. (string)($l2+2) . ": " . join(';', $data2). "<br>";
                        $method = "setMethodeMotuVocFk";
                        $entityRel->$method($foreign_record);
                    }  
                    //var_dump($l2); var_dump($flagSeqExt); var_dump($flagSeq);  var_dump($data2);
                    $entityRel->setDateCre($DateImport);
                    $entityRel->setDateMaj($DateImport);
                    $entityRel->setUserCre($userId);
                    $entityRel->setUserMaj($userId);
                    $em->persist($entityRel);
                    if (!$flagSeq && !$flagSeqExt ) $message .= $this->translator->trans('importfileService.ERROR bad code').'<b> : '.$data2["code_seq_ass"].'</b> <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                    if ($flagSeq && $flagSeqExt) $message .= $this->translator->trans('importfileService.ERROR duplicate code sqc sqcext').'<b> : '.$data2["code_seq_ass"].'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data2)."<br>"; 
                }
            } else {
               return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
               exit;                
            }
        }      
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvDataMotu).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    /**
    *  importCSVDataEtablissement($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is institution
    */ 
    public function importCSVDataEtablissement($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager    
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;         
            $entity = new \App\Entity\Etablissement();    
            // 
            if (array_key_exists("institution" ,$columnByTable)) {
                foreach($columnByTable["institution"] as $ColCsv){  
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                    $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                    if (!$flag_foreign) { 
                        $varfield = explode(".", $field)[1];
                        if($ColCsv  == 'institution.institution_name') { 
                            $record_entity = $em->getRepository("App:Etablissement")->findOneBy(array("nomEtablissement" => $dataColCsv)); 
                            if($record_entity !== NULL){ 
                               $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                            }
                        }
                        // control and standardization of field formats
                        // save the values ​​of the field
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entity->$method($dataColCsv);                     
                    }               
                }
                $entity->setDateCre($DateImport);
                $entity->setDateMaj($DateImport);
                $entity->setUserCre($userId);
                $entity->setUserMaj($userId);
                $em->persist($entity);
            } else {
               return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
               exit;
            }
        }  
        # FLUSH 
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
        
    
    /**
    *  importCSVDataPays(array $csvData)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is country
    */ 
    public function importCSVDataPays($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager    
         $compt = 0;
         $message = '';
         $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
         $list_new_commune = array();            
         foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
             $compt++;
             $entity = new \App\Entity\Pays();
             if (array_key_exists("country" ,$columnByTable)) {
                foreach($columnByTable["country"] as $ColCsv){  
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                    $varfield = explode(".", $field)[1];
                    if($field == 'country.countryCode') { 
                        $record_pays = $em->getRepository("App:Pays")->findOneBy(array("codePays" => $dataColCsv)); 
                        if($record_pays !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+1).": ".join(';', $data)."<br>"; 
                        }
                    }
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);   // save the values ​​of the field                   
                }
                $entity->setDateCre($DateImport);
                $entity->setDateMaj($DateImport);
                $entity->setUserCre($userId);
                $entity->setUserMaj($userId);
                $em->persist($entity);  
            } else {
               return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
               exit;
            }
        }        
        if ($message ==''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

    
   /**
    *  importCSVDataSequenceAssembleeExt($fichier, $userId = null)
    * $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is external_sequence
    */ 
    public function importCSVDataSequenceAssembleeExt($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            #
            $entity = new \App\Entity\SequenceAssembleeExt();    
            // 
            foreach($columnByTable["external_sequence"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field'); 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'external_sequence.external_sequence_code') { 
                        $record_entity = $em->getRepository("App:SequenceAssembleeExt")->findOneBy(array("codeSqcAssExt" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'external_sequence.external_sequence_creation_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["external_sequence_is_entered_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\SqcExtEstRealisePar;
                       $method = "setSequenceAssembleeExtFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["external_sequence_is_published_in"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\SqcExtEstReferenceDans();
                       $method = "setSequenceAssembleeExtFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["identified_species"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // control and standardization of field formats
                    if ($ColCsv == 'identified_species.identification_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    $val_foreign_field = trim($dataColCsv);
                    //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                            default:
                               $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["species_is_identified_by"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

   /**
    *  importCSVDataLotMaterielExt($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is external_biological_external
    */ 
    public function importCSVDataLotMaterielExt($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            #
            $entity = new \App\Entity\LotMaterielExt();    
            // 
            foreach($columnByTable["external_biological_material"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'external_biological_material.external_biological_material_code') {  
                        $record_entity = $em->getRepository("App:LotMaterielExt")->findOneBy(array("codeLotMaterielExt" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'external_biological_material.external_biological_material_creation_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of external_biological_material_is_processed_by                    
            foreach($columnByTable["external_biological_material_is_processed_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\LotMaterielExtEstRealisePar;
                       $method = "setLotMaterielExtFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["external_biological_material_is_published_in"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\LotMaterielExtEstReferenceDans();
                       $method = "setLotMaterielExtFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["identified_species"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // control and standardization of field formats
                    if ($ColCsv == 'identified_species.identification_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    $val_foreign_field = trim($dataColCsv);
                    //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                            default:
                               $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["species_is_identified_by"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
   
    
   /**
    *  importCSVDataStation($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is site
    */ 
    public function importCSVDataStation($fichier, $userId = null)
    { 
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager  
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Station();
            foreach($columnByTable["site"] as $ColCsv){  
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               if (!$flag_foreign) { 
                   $varfield = explode(".", $field)[1];
                   // var_dump($ColCsv); var_dump($field); exit;
                   if($field == 'site.codeStation') { // On teste pour savoir si le site_code a déja été créé. 
                       $record_station = $em->getRepository("App:Station")->findOneBy(array("codeStation" => $dataColCsv)); 
                       if($record_station !== NULL){ 
                          $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+1).": ".join(';', $data)."<br>"; 
                       }
                   }
                   // we adapt the format of long and lat
                   if ($field == 'site.latDegDec' || $field == 'site.longDegDec') {$dataColCsv = ($dataColCsv != '') ?  floatval(str_replace(",", ".", $dataColCsv)): null;}
                   if ($field == 'site.altitudeM') {$dataColCsv = ($dataColCsv != '') ?  intval(str_replace(",", ".", $dataColCsv)) : null; }
                   // save the values ​​of the field
                   $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                   $entity->$method($dataColCsv);                     
               }
                if($flag_foreign){ 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                           case 'Commune':
                               if ($dataColCsv != '') {
                                   $CodeCommune = $dataColCsv ;
                                   if (array_key_exists($CodeCommune,$list_new_commune)) {  
                                       $municipality = $list_new_commune[$CodeCommune];
                                   } else { // if CodeCommune is null create a new municipality with a codeCommune as Nom_Commune|Nom_Region|Nom_Pays and field site_name = "Nom Commune" and municipality_name = "Nom Region"
                                       $municipality = new \App\Entity\Commune();      
                                       $municipality->setCodeCommune($CodeCommune);
                                       $list_field_commune = explode("|", $dataColCsv);
                                       $municipality->setNomCommune(str_replace("_"," ",$list_field_commune[0]));
                                       $municipality->setNomRegion(str_replace("_"," ",$list_field_commune[1]));
                                       $municipality->setDateCre($DateImport);
                                       $municipality->setDateMaj($DateImport);
                                       $municipality->setUserCre($userId);
                                       $municipality->setUserMaj($userId);
                                       $country_fk = $em->getRepository("App:Pays")->findOneBy(array("codePays" => $list_field_commune[2])); 
                                       if($country_fk === NULL){ 
                                            $message .= $this->translator->trans('importfileService.ERROR bad code').' : '.$list_field_commune[2]. '</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                       }
                                       $municipality->setPaysFk($country_fk);
                                       $em->persist($municipality);                                       
                                       $list_new_commune[$CodeCommune] = $municipality; // we keep in memory the communes created
                                   }
                                   $foreign_fieldName = $foreign_table."Fk";
                                   $method =  $importFileCsvService->TransformNameForSymfony($foreign_fieldName,'set');
                                   $entity->$method($municipality); 
                               }
                               break;
                        case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                        default:
                              $message .= $this->translator->trans('importfileService.ERROR unknown record').$foreign_table."-".$foreign_field ." <b>" . $dataColCsv. '</b> <br> ligne '. (string)($l+1) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
       // A FAIRE : ajouter les champ municipality.municipality_name +municipality.region_name

       if ($message ==''){
           try {
               $flush = $em->flush();
               return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;            }          
       } else {
           return $info.'</br>'.$message;
       }
    }
    
    /**
    *  importCSVDataSequenceAssemblee($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is internal_sequence
    */ 
    public function importCSVDataSequenceAssemblee($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager      
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_personne = array(); 
        $commentaireCompoLotMateriel = "";
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            #
            $entity = new \App\Entity\SequenceAssemblee();    
            // 
            foreach($columnByTable["internal_sequence"] as $ColCsv){  
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // var_dump($ColCsv); var_dump($field); exit;
                    if($ColCsv == 'internal_sequence.internal_sequence_code') { 
                        $record_entity = $em->getRepository("App:SequenceAssemblee")->findOneBy(array("codeSqcAss" => $dataColCsv)); 
                        if($record_entity !== NULL){ 
                           $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$dataColCsv."</b> <br>ligne ".(string)($l+2).": ".join(';', $data)."<br>"; 
                        }
                    }
                    // control and standardization of field formats
                    if ($ColCsv == 'internal_sequence.internal_sequence_creation_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);                     
                }
                if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                   $varfield = explode(".", strstr($field, '(', true))[1];
                   $linker = explode('.', trim($foreign_content[0],"()"));  
                   $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                   $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                   //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                   $varfield_parent = strstr($varfield, 'Voc', true);
                   if (!$varfield_parent) {
                     $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                   } else {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                   }  
                   if($foreign_record === NULL){  
                      switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }
                                break;
                            default:
                                $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                       }
                    } else {
                       $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                       $entity->$method($foreign_record);
                   }
                }                
            }
            $entity->setDateCre($DateImport);
            $entity->setDateMaj($DateImport);
            $entity->setUserCre($userId);
            $entity->setUserMaj($userId);
            $em->persist($entity);
            
            # Record of internal_sequence_is_assembled_by                     
            foreach($columnByTable["internal_sequence_is_assembled_by"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\SequenceAssembleeEstRealisePar();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["internal_sequence_is_published_in"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\SqcEstPublieDans();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            
            # Enregistrement de EstAligneEtTraite    (liaison aux chromatogram)                
            foreach($columnByTable["chromatogram_is_processed_to"] as $ColCsv){   
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\EstAligneEtTraite();
                       $method = "setSequenceAssembleeFk";
                       $entityRel->$method($entity);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
             foreach($columnByTable["identified_species"] as $ColCsv){ 
                $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                if ($dataColCsv !== $data[$ColCsv] ) {
                    $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                }
                $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
                if (!$flag_foreign) { 
                    $varfield = explode(".", $field)[1];
                    // control and standardization of field formats
                    if ($ColCsv == 'identified_species.identification_date' ) {
                        // adjusts the incomplete date of type m/Y or Y in 01/m/Y or 01/01/ Y
                        if ($dataColCsv != ''){
                            if (count(explode("/",$dataColCsv))== 2) $dataColCsv = "01/".$dataColCsv;
                            if (count(explode("/",$dataColCsv))== 1) $dataColCsv = "01/01/".$dataColCsv;
                            $eventDate = date_create_from_format('d/m/Y', $dataColCsv);
                            if (!$eventDate) {
                                $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                $dataColCsv = NULL; 
                            } else {
                                $tabdate = explode("/",$dataColCsv);
                                if (checkdate($tabdate[1], $tabdate[0], $tabdate[2])) {
                                    $dataColCsv = date_format($eventDate, 'Y-m-d');
                                    $dataColCsv = new \DateTime($dataColCsv);
                                } else {
                                    $message .= $this->translator->trans('importfileService.ERROR bad date format').'<b> : '.$data[$ColCsv]."-".$dataColCsv."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";
                                    $dataColCsv = NULL; 
                                }
                            }
                            //var_dump($ColCsv); var_dump($eventDate); var_dump($dataColCsv);
                        } else {
                          $dataColCsv = NULL;  
                        }
                    }
                    // save the values ​​of the field
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entityRel->$method($dataColCsv);                     
                }
                if($flag_foreign){ 
                    $varfield = explode(".", strstr($field, '(', true))[1];
                    $linker = explode('.', trim($foreign_content[0],"()"));  
                    $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                    $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                    $val_foreign_field = trim($dataColCsv);
                    //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                    $varfield_parent = strstr($varfield, 'Voc', true);
                    if (!$varfield_parent) {
                      $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                    } else {
                       $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                    }  
                    if($foreign_record === NULL){  
                       switch ($foreign_table) {
                            case "Voc":
                                if ($data[$ColCsv] == '')  {
                                    $foreign_record = NULL;
                                }  else {
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                }                             
                                break; 
                            default:
                               $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                        }
                     } else {
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
            foreach($columnByTable["species_is_identified_by"] as $ColCsv){ 
               $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
               if ($dataColCsv !== $data[$ColCsv] ) {
                   $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
               }
               $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
               $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key                 
               $varfield = explode(".", strstr($field, '(', true))[1];
               $linker = explode('.', trim($foreign_content[0],"()"));  
               $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
               $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
               $tab_foreign_field = explode("$",$dataColCsv); // We transform the contents of the field into a table
               if($flag_foreign && trim($dataColCsv) != ''){ 
                   foreach($tab_foreign_field as $val_foreign_field){ 
                       $val_foreign_field = trim($val_foreign_field);
                       $entityRel = new \App\Entity\EstIdentifiePar();
                       $method = "setEspeceIdentifieeFk";
                       $entityRel->$method($entityEspeceIdentifie);
                       //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                       $varfield_parent = strstr($varfield, 'Voc', true);
                       if (!$varfield_parent) {
                         $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field));    
                       } else {
                          $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $val_foreign_field, "parent" => $varfield_parent));  
                       }  
                       if($foreign_record === NULL){  
                          switch ($foreign_table) {
                                case "Voc":
                                    if ($data[$ColCsv] == '')  {
                                        $foreign_record = NULL;
                                    }  else {
                                      $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                    }                             
                                    break; 
                               default:
                                  $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field ." <b>[" . $val_foreign_field. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                           }
                        } else {
                           $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
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
        if ($message == ''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataReferentielTaxon($fichier, $userId = null)
    *  $fichier : path to the download csv file
    *  NOTE : the template of csv file to import is taxon
    */ 
    public function importCSVDataReferentielTaxon($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager   
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\ReferentielTaxon();
            if (array_key_exists("taxon" ,$columnByTable)) {
               foreach($columnByTable["taxon"] as $ColCsv){  
                  $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');         
                   $varfield = explode(".", $field)[1];
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                   if($ColCsv == 'taxon.taxon_name') { // On teste pour savoir si le taxon_name a déja été créé. 
                       $record_entity = $em->getRepository("App:ReferentielTaxon")->findOneBy(array("taxon_name" => $dataColCsv)); 
                       if($record_entity !== NULL){ 
                          $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                       }
                    }
                    if ($ColCsv == 'taxon.taxon_validity') { 
                        if ($dataColCsv != '') {
                            if ($dataColCsv == 'YES' || $dataColCsv == 'NO') {
                                $dataColCsv = ($dataColCsv == 'YES') ? 1 : 0; 
                            } else {
                                $message .= $this->translator->trans('importfileService.ERROR bad data YES-NO').'<b> : '.$ColCsv." / ".$data[$ColCsv]."</b>  <br> ligne ".(string)($l+2).": ".join(';', $data)."<br>";    
                            }
                        }
                    }
                    if ($dataColCsv === '') $dataColCsv = NULL; // if there is no value, initialize the value to NULL
                    $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                    $entity->$method($dataColCsv);   // save the values ​​of the field   
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $entity->setUserCre($userId);
               $entity->setUserMaj($userId);
               $em->persist($entity);     
            } else {
                return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
                exit;
            }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;            }          
       } else {
           return $info.'</br>'.$message;
       }
    }
     
    /**
    *  importCSVDataVoc($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is vocabulary
    */ 
    public function importCSVDataVoc($fichier, $userId = null)
    {
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager   
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');
        $list_new_commune = array();      
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;
            $entity = new \App\Entity\Voc();
            if (array_key_exists("vocabulary" ,$columnByTable)) {
               foreach($columnByTable["vocabulary"] as $ColCsv){  
                   $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');                 
                   $varfield = explode(".", $field)[1];
                   $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                   if ($dataColCsv !== $data[$ColCsv] ) {
                       $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                   } 
                  // On teste pour savoir si le code_voc n existe pas deja pour ce parent 
                  if ($ColCsv == 'vocabulary.parent') {
                      $record_voc = $em->getRepository("App:Voc")->findOneBy(array("parent" => $dataColCsv, "code" => $code));  
                      if($record_voc !== NULL){ 
                          $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b>  <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                      }
                   } 
                  $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                  $entity->$method($dataColCsv);   
                  if ($ColCsv == 'vocabulary.code' ) {$code = $dataColCsv;}
               }
               $entity->setDateCre($DateImport);
               $entity->setDateMaj($DateImport);
               $entity->setUserCre($userId);
               $entity->setUserMaj($userId);
               $em->persist($entity);     
            } else {
                return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
                exit;
            }
       }        
       if ($message ==''){
           try {
               $flush = $em->flush();
               return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
               } 
           catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;            }          
       } else {
           return $info.'</br>'.$message;
       }
    }
  
    /**
    *  importCSVDataPersonne($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is person
    */ 
    public function importCSVDataPersonne($fichier, $userId = null)
    {     
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager    
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');  
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;         
            # Enregistrement des données de Personne
            $entity = new \App\Entity\Personne();    
            // 
            if (array_key_exists("person" ,$columnByTable)) {
                foreach($columnByTable["person"] as $ColCsv){  
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                    $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key  
                    if (!$flag_foreign) { 
                        $varfield = explode(".", $field)[1];
                        if($ColCsv  == 'person.person_name') { 
                            $record_entity = $em->getRepository("App:Personne")->findOneBy(array("nomPersonne" => $dataColCsv)); 
                            if($record_entity !== NULL){ 
                               $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                            }
                        }
                        // control and standardization of field formats
                        // save the values ​​of the field
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entity->$method($dataColCsv);                     
                    }
                    if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                        $varfield = explode(".", strstr($field, '(', true))[1];
                        $linker = explode('.', trim($foreign_content[0],"()"));  
                        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                        if (!is_null($dataColCsv)) { 
                            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                            } else {
                               $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case 'etablissement' :
                                        // la valeur NULL est permise
                                        break;
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
                $entity->setUserCre($userId);
                $entity->setUserMaj($userId);
                $em->persist($entity);
            } else {
               return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
               exit;
            }
        }  
        # FLUSH
        if ($message ==''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }
    
    /**
    *  importCSVDataCommune($fichier, $userId = null)
    *  $fichier : path to the download csv file 
    *  NOTE : the template of csv file to import is municipality
    */ 
    public function importCSVDataCommune($fichier, $userId = null)
    {     
        $importFileCsvService = $this->importFileCsv; // retrieve the ImportFileCsv service
        $csvData = $importFileCsvService->readCSV($fichier);
        $columnByTable =  $importFileCsvService->readColumnByTableSV($csvData); // Retrieve CSV fields as a table
        $DateImport= $importFileCsvService->GetCurrentTimestamp();
        $em = $this->entityManager;    // call of Doctrine manager    
        $compt = 0;
        $message = '';
        $info = $this->translator->trans('importfileService.Date of data set import').' : '.$DateImport->format('Y-m-d H:i:s');  
        foreach($csvData as $l => $data){ // 1- Line-to-line data processing ($ l)
            $compt++;         
            # Enregistrement des données de Personne
            $entity = new \App\Entity\Commune();    
            // 
            if (array_key_exists("municipality" ,$columnByTable)) {
                foreach($columnByTable["municipality"] as $ColCsv){  
                    $field = $importFileCsvService->TransformNameForSymfony($ColCsv,'field');   
                    $dataColCsv = $importFileCsvService->suppCharSpeciaux($data[$ColCsv],'tnrOx');
                    if ($dataColCsv !== $data[$ColCsv] ) {
                        $message .=  $this->translator->trans('importfileService.ERROR bad character').'<b> : ' .$data[$ColCsv]. '</b> <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                    }
                    if ($dataColCsv == '' || trim($dataColCsv) == '') $dataColCsv = NULL;
                    $flag_foreign = preg_match('(\((.*?)\))', $ColCsv, $foreign_content);  // flag to know if 1) it is a foreign key  
                    if (!$flag_foreign) { 
                        $varfield = explode(".", $field)[1];
                        if($ColCsv  == 'municipality.municipality_code') { 
                            $record_entity = $em->getRepository("App:Commune")->findOneBy(array("codeCommune" => $dataColCsv)); 
                            if($record_entity !== NULL){ 
                               $message .= $this->translator->trans('importfileService.ERROR duplicate code').'<b> : '.$data[$ColCsv]." / ".$ColCsv.'</b> <br>ligne '.(string)($l+2).": ".join(';', $data)."<br>"; 
                            }
                        }
                        // control and standardization of field formats
                        // save the values ​​of the field
                        $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                        $entity->$method($dataColCsv);                     
                    }
                    if($flag_foreign){ // case of a foreign key (where there are parentheses in the field name) 
                        $varfield = explode(".", strstr($field, '(', true))[1];
                        $linker = explode('.', trim($foreign_content[0],"()"));  
                        $foreign_table = $importFileCsvService->TransformNameForSymfony($linker[0],'table'); 
                        $foreign_field = $importFileCsvService->TransformNameForSymfony($linker[1],'field'); 
                        if (!is_null($dataColCsv)) { 
                            //  test if it is a foreign key of the Voc table of the form: parentVocFk or parentVocAliasFk
                            $varfield_parent = strstr($varfield, 'Voc', true);
                            if (!$varfield_parent) {
                              $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv));    
                            } else {
                               $foreign_record = $em->getRepository("App:".$foreign_table)->findOneBy(array($foreign_field => $dataColCsv, "parent" => $varfield_parent));  
                            }  
                            if($foreign_record === NULL){  
                               switch ($foreign_table) {
                                    case "Voc":
                                        if ($data[$ColCsv] == '')  {
                                            $foreign_record = NULL;
                                        }  else {
                                          $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$foreign_table.".".$foreign_field.".".$varfield_parent." <b>[" . $data[$ColCsv]. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";                                 
                                        }                             
                                        break; 
                                    default:
                                       $message .= $this->translator->trans('importfileService.ERROR unknown record').' : '.$field.' : '.$foreign_table.".".$foreign_field ." <b>[" . $dataColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
                                }
                            } else {
                                $method =  $importFileCsvService->TransformNameForSymfony($varfield,'set');
                                $entity->$method($foreign_record);                               
                            }  
                        } else {
                            // cas des valeurs NULL 
                            switch ($foreign_table) {
                                    case 'Pays' :
                                        $message .= $this->translator->trans('importfileService.ERROR NULL value').' : '.$field." <b>[" . $ColCsv. ']</b>  <br> ligne '. (string)($l+2) . ": " . join(';', $data). "<br>";
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
               return($this->translator->trans('importfileService.ERROR bad columns in CSV'));
               exit;
            }
        }  
        # FLUSH
        if ($message ==''){
            try {
                $flush = $em->flush();
                return  $this->translator->trans('importfileService.Import OK').' = '.count($csvData).'</br>'.$info;
                } 
            catch(\Doctrine\DBAL\DBALException $e) {
                $exception_message =  str_replace('"', '\"',str_replace("'", "\'", html_entity_decode(strval($e), ENT_QUOTES , 'UTF-8')));
                $message = $this->translator->trans('importfileService.Problem of FLUSH').' : </br>'.explode("\n", $exception_message)[0];
                if(count(explode("\n", $exception_message))>1) $message .= ' : </br>'.explode("\n", $exception_message)[1];
                return $message;             }          
        } else {
            return $info.'</br>'.$message;
        }
    }

}
