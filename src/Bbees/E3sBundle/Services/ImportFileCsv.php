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
    * read CSV file with path $path
    * return array : {array / line}  : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...) 
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
    * read CSV file with path $path
    * return array : {array / line} : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...) 
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
    *  return an array [nomTable1 => array(nomTable1.NomField1, nomTable1.NomField2, ...), nomTable2 => array(nomTable2.NomField1, nomTable2.NomField2, ...), ...]
    */ 
    public function readColumnByTableSV($csvData){
         # Recovery of the names of columns to be named of the form: TableName.FieldName
         $column_name = [];
         reset($csvData);
         foreach(current($csvData) as $k=>$v){
             $column_name[] = $k;            
         }
         # Recovering column names for each table
         $columnByTable = [];
         foreach($column_name as $v){
             $ent = explode('.', $v)[0]; 
             if(array_key_exists($ent, $columnByTable)){ // add the name of the column "TableName.FieldName ..." to the table
                 $columnByTable[$ent][] = $v;               
             } else { // add a table with the title of the column "TableName.FieldName ..."
                 $columnByTable[$ent] = [$v]; 
             }
         }
        return($columnByTable);              
    }   
    
    /**
    *  testNomColumnCSV($columnByTable)
    *  test the name of CSV field like : NameOfDatabaseTable.FieldName
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
     * function that transforms db_field_name into db_field_name used by symfony
     * return for field_name_csv = fieldNameCsv (cas $type=field)
     * return for  field_name_csv = FieldNameCsv (cas $type=entity)
     * return for  field_name_csv = setFieldNameCsv (cas $type=set)   
    */ 
    public function TransformNameForSymfony($field_name_csv, $type='field'){
        // the type can be 'field' or 'table'
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
     * deleting special characters 
    */ 
    public function suppCharSpeciaux($data, $type='all'){
        // the type can be 'field' or 'table'
        switch ($type) {
            case " ":
                // space
                $data_corrected = str_replace(" ","", $data);
                break;
            case "t":
                // tabulation
                $data_corrected = str_replace("\t","",$data);
                break;
            case "n":
                // line break
                $data_corrected = str_replace("\n","",$data);
                break;
            case "r":
                // carriage return
                $data_corrected = str_replace("\r","",$data);
                break;
            case "O":
                // NULL 
                $data_corrected = str_replace("\0","",$data);
                break;
            case "x":
                // vertical tabulation
                $data_corrected = str_replace("\x0B","",$data);
                break;
            case "tnrOx":
                $data_corrected = str_replace("\t","",$data);
                $data_corrected = str_replace("\n","",$data_corrected);
                $data_corrected = str_replace("\r","",$data_corrected);
                $data_corrected = str_replace("\0","",$data_corrected);
                $data_corrected = str_replace("\x0B","",$data_corrected);
                break;
            case "all":
                $data_corrected = str_replace(" ","", $data);
                $data_corrected = str_replace("\t","",$data_corrected);
                $data_corrected = str_replace("\n","",$data_corrected);
                $data_corrected = str_replace("\r","",$data_corrected);
                $data_corrected = str_replace("\0","",$data_corrected);
                $data_corrected = str_replace("\x0B","",$data_corrected);
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
    * return the objet timestamp current 
    */ 
    public function GetCurrentTimestamp(){
       // on récupére le TIMESTAMP du traitement : renseignera le champ date_import  
       $dateImport = date("Y-m-d H:i:s"); 
       $DateImport = new \DateTime($dateImport);
       return $DateImport;
    }
    
}
