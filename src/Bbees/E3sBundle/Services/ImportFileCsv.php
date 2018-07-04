<?php

namespace Bbees\E3sBundle\Services;

use Doctrine\ORM\EntityManager;


/**
* Service ImportFileCsv
*/
class ImportFileCsv 
{
    private $entityManager;
    
     public function __construct(EntityManager $manager) {
        $this->entityManager = $manager ;
     }
       
    /**
    *  readCSV($path)
     * lecture du fichier CSV renseigné par son chemin $path
     * retourne un tableau / ligne  : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...) 
    */ 
    public function readCSV($path){
        $result = array();
        $name = array();		
        $handle = fopen($path, "r");				
        if(($data = fgetcsv($handle, 0, ";")) !== FALSE){
            $num = count($data);
            $row = 0;
            $name = $data;			
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE){
                if($num == count($data)){
                        for ($c=0; $c < $num; $c++) {
                                $result[$row][$name[$c]] = $data[$c];
                        }					
                        $row++;
                }
            }
        }
        fclose($handle);
        return($result);              
    }   
 
    /**
    *  explodeCSV($path, $maxLine)
     * lecture du fichier CSV renseigné par son chemin $path 
     * retourne un tableau de tableau = tableau / ligne  : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...) 
    */ 
    public function explodeCSV($path, $index, $maxLine=100){
        $result = array();
        $name = array();		
        $handle = fopen($path, "r");				
        if(($data = fgetcsv($handle, 0, ";")) !== FALSE){
            $num = count($data); // nombre de colonne
            $row = 0;
            $name = $data;	
            $nbline = 0;
            $tab = array();
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE){
                $nbline++;
                if($num == count($data)){
                        for ($c=0; $c < $num; $c++) {
                                $tab[$row][$name[$c]] = $data[$c];
                        }					
                        $row++;
                }
                if($nbline == $maxLine){
                    $nbline =0;
                    $result[] = $tab;
                    $tab = array();
                }
            }
        $result[] = $tab;  
        }
        fclose($handle);
        return($result);              
    }   
    
    /**
    *  readColumnByTableSV($csvData)
    * retourne un tableau [nomTable1 => array(nomTable1.NomField1, nomTable1.NomField2, ...), nomTable2 => array(nomTable2.NomField1, nomTable2.NomField2, ...), ...]
    */ 
    public function readColumnByTableSV($csvData){
         # Recuperation des noms de colonnes qui doivent etre nommées de la forme : NomTable.NomField
         $column_name = [];
         reset($csvData);
         foreach(current($csvData) as $k=>$v){
             $column_name[] = $k;            
         }
         # Recuperation des noms de colonnes pour chaque table
         $columnByTable = [];
         foreach($column_name as $v){
             $ent = explode('.', $v)[0]; // on recupère le NomTable 
             if(array_key_exists($ent, $columnByTable)){ // on ajoute au tableau l'intitulé de la colonne "NomTable.NomField..."
                 $columnByTable[$ent][] = $v;               
             } else { // on ajoute un tableau avec l'intitulé de la colonne "NomTable.NomField..." 
                 $columnByTable[$ent] = [$v]; 
             }
         }
        return($columnByTable);              
    }   
    
    /**
    *  testNomColumnCSV($columnByTable)
    *  test si le fichier commence bien par 
    */ 
    public function testNameColumnCSV($columnByTable,$nameTable, $nameField = NULL){
        
         # Test
         if (array_key_exists($nameTable ,$columnByTable)) {
            $output = 1;
            if ($nameField !== NULL){     
                if (! in_array($nameField ,$columnByTable[$nameTable])) $output = 0;    
            }
         } else {
            $output = 0;   
         }
        return($output);              
    } 
    
    /**
    *  TransformNameForSymfony($field_name_csv, $type='field')
     * fonction qui transforme les nom_champ_bdd en nomChampBdd utilsé par symfony
     * retourne pour field_name_csv = fieldNameCsv (cas $type=field)
     * retourne pour field_name_csv = FieldNameCsv (cas $type=entity)
     * retourne pour field_name_csv = setFieldNameCsv (cas $type=set)   
    */ 
    public function TransformNameForSymfony($field_name_csv, $type='field'){
        // le type peut être 'field' ou 'table'
       $field_name_csv_in_array = explode('_', $field_name_csv);
       $field_name_symfony = '';
       $compt = 0;
       foreach($field_name_csv_in_array as $v){
           if (!$compt && $type == 'field') {
               $field_name_symfony = $v;
           } else {
               $field_name_symfony = $field_name_symfony.ucfirst($v);
           }
           if ($type == 'set' ) {$field_name_symfony = 'set'.$field_name_symfony;}
           if ($type == 'get' ) {$field_name_symfony = 'get'.$field_name_symfony;}
           $compt++;
       }      
       return $field_name_symfony;
    }
    
    /**
    *  suppCharSpeciaux($field, $type='')
     * suppression des charactère spéciaux  
    */ 
    public function suppCharSpeciaux($data, $type='all'){
        // le type peut être 'field' ou 'table'
        //$data = mb_convert_encoding($data, "UTF-8","ASCII");
        switch ($type) {
            case " ":
                // espace
                $data_corrected = str_replace(" ","", $data);
                //if ($data_corrected !== $data ) echo "</br> suppression d un ou plusieurts espaces dans : ".$data;
                break;
            case "t":
                // tabulation
                $data_corrected = str_replace("\t","",$data);
                //if ($data_corrected !== $data ) echo "</br> suppression d une tabulation dans : ".$data;
                break;
            case "n":
                $data_corrected = str_replace("\n","",$data);
                //if ($data_corrected !== $data ) echo "</br> suppression d une ligne dans : ".$data;
                break;
            case "r":
                $data_corrected = str_replace("\r","",$data);
                //if ($data_corrected !== $data ) echo "</br> suppression d un retour chariot dans : ".$data;
                break;
            case "O":
                $data_corrected = str_replace("\0","",$data);
                //if ($data_corrected !== $data ) echo "</br> suppression d une caractère NULL dans : ".$data;
                break;
            case "x":
                $data_corrected = str_replace("\x0B","",$data);
                //if ($data_corrected !== $data ) echo "</br> suppression d une tabulation verticale dans : ".$data;
                break;
            case "tnrOx":
                $data_corrected = str_replace("\t","",$data);
                $data_corrected = str_replace("\n","",$data_corrected);
                $data_corrected = str_replace("\r","",$data_corrected);
                $data_corrected = str_replace("\0","",$data_corrected);
                $data_corrected = str_replace("\x0B","",$data_corrected);
                //if ($data_corrected !== $data ) {echo "</br> ! suppression de caracteres speciaux  dans : ".$data;}
                break;
            case "all":
                $data_corrected = str_replace(" ","", $data);
                $data_corrected = str_replace("\t","",$data_corrected);
                $data_corrected = str_replace("\n","",$data_corrected);
                $data_corrected = str_replace("\r","",$data_corrected);
                $data_corrected = str_replace("\0","",$data_corrected);
                $data_corrected = str_replace("\x0B","",$data_corrected);
                //if ($data_corrected !== $data ) {echo "</br> ! suppression de caracteres speciaux  dans : ".$data;}
                break;
            default:
                $data_corrected = $data;
                echo "</br> le type de correction de la fonction suppCharSpeciaux n existe pas : type = ".$type; 
                exit;
        }
        return $data_corrected;
    }

    /**
    *  GetCurrentTimestamp()
     * fonction qui retourne l'objet timestamp current
     * retourne $DateImport 
    */ 
    public function GetCurrentTimestamp(){
       // on récupére le TIMESTAMP du traitement : renseignera le champ date_import  
       $dateImport = date("Y-m-d H:i:s"); 
       $DateImport = new \DateTime($dateImport);
       return $DateImport;
    }
    
}
