<?php

namespace App\Services\Core;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Service ImportFileCsv
 * @author Philippe Grison  <philippe.grison@mnhn.fr>
 */
class ImportFileCsv {

  private $entityManager;

  public function __construct(EntityManagerInterface $manager, String $root_dir) {
    $this->entityManager = $manager;
    // $this->assetsManager = $assetsManager;
    $this->root_dir = $root_dir;
    // $this->assetsManager = new Package(new JsonManifestVersionStrategy(
    //   $root_dir . '/public//build/manifest.json'
    // ));
  }

  /**
   * getCsvPath
   * returns the path of CSV file given its type
   */
  public function getCsvPath(string $type_csv) {
    return $this->root_dir . '/assets/imports/' . $type_csv . '.csv';
    // return $this->assetsManager->getUrl('build/imports/' . $type_csv . '.csv');
  }

  /**
   *  readCSV($path)
   * read CSV file with path $path
   * return array : {array / line}  : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...)
   */
  public function readCSV($path) {
    $result = array();
    $name = array();
    if (file_exists($path)) {
      $handle = fopen($path, "r");
      if (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
        $row = 0;
        $name = $data;
        $flag_record = 0;
        while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
          // return one array containing the all {column => value} by record(s) or line
          if ($num == count($data)) {
            $flag_record = 1;
            for ($c = 0; $c < $num; $c++) {
              $result[$row][$name[$c]] = $data[$c];
            }
            $row++;
          }
        }
        if (!$flag_record) {
          // return the column name to NULL if there is no record
          for ($c = 0; $c < $num; $c++) {
            $result[0][$name[$c]] = $data[$c];
          }
        }
      }
      fclose($handle);
    }
    return ($result);
  }

  /**
   *  explodeCSV($path, $maxLine)
   * read CSV file with path $path
   * return array : {array / line} : ex. array([NomCol1] => ValCol1L1, [NomCol2] => ValCol2L1...)
   */
  public function explodeCSV($path, $index, $maxLine = 100) {
    $result = array();
    $name = array();
    $handle = fopen($path, "r");
    if (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
      $num = count($data); // nombre de colonne
      $row = 0;
      $name = $data;
      $nbline = 0;
      $tab = array();
      while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $nbline++;
        if ($num == count($data)) {
          for ($c = 0; $c < $num; $c++) {
            $tab[$row][$name[$c]] = $data[$c];
          }
          $row++;
        }
        if ($nbline == $maxLine) {
          $nbline = 0;
          $result[] = $tab;
          $tab = array();
        }
      }
      $result[] = $tab;
    }
    fclose($handle);
    return ($result);
  }

  /**
   *  readColumnByTableSV($csvData)
   *  return an array [nomTable1 => array(nomTable1.NomField1, nomTable1.NomField2, ...), nomTable2 => array(nomTable2.NomField1, nomTable2.NomField2, ...), ...]
   */
  public function readColumnByTableSV($csvData) {
    # Recovery of the names of columns to be named of the form: TableName.FieldName
    $column_name = [];
    reset($csvData);
    foreach (current($csvData) as $k => $v) {
      $column_name[] = $k;
    }
    # Recovering column names for each table
    $columnByTable = [];
    foreach ($column_name as $v) {
      $ent = explode('.', $v)[0];
      if (isset($columnByTable[$ent])) { // add the name of the column "TableName.FieldName ..." to the table
        $columnByTable[$ent][] = $v;
      } else { // add a table with the title of the column "TableName.FieldName ..."
        $columnByTable[$ent] = [$v];
      }
    }
    return ($columnByTable);
  }

  /**
   *  testNomColumnCSV($columnByTable)
   *  test the name of CSV field like : NameOfDatabaseTable.FieldName
   */
  public function testNameColumnCSV($columnByTable, $nameTable, $nameField = NULL) {

    # Test
    if (isset($columnByTable[$nameTable])) {
      $output = 1;
      if ($nameField !== NULL) {
        if (!in_array($nameField, $columnByTable[$nameTable])) {
          $output = 0;
        }

      }
    } else {
      $output = 0;
    }
    return ($output);
  }

  /**
   *  checkNameCSVfile2Template($pathToTemplate, $pathToFileImport)
   * $pathToTemplate : path to template file
   * $pathToFileImport : path To CSV File Imported (tmp)
   *  compare the name of CSV field between the file imported and the template
   *  return text : ERROR message if exist OR '' if not
   *
   */
  public function checkNameCSVfile2Template($pathToTemplate, $pathToFileImport) {
    $output = '';
    $arrayTemplateCsv = $this->readCSV($pathToTemplate);
    $arrayFileImport = $this->readCSV($pathToFileImport);

    # Recovery of the names of columns to be named of the form: TableName.FieldName
    if ($arrayTemplateCsv !== array()) {
      $column_name_template = [];
      reset($arrayTemplateCsv);
      foreach (current($arrayTemplateCsv) as $k => $v) {
        $column_name_template[] = $k;
      }
    } else {
      $output = 'importfileService.ERROR Unknown template file';
    }

    # Recovery of the names of columns to be named of the form: TableName.FieldName
    if ($arrayFileImport !== array()) {
      $column_name = [];
      reset($arrayFileImport);
      foreach (current($arrayFileImport) as $k => $v) {
        $column_name[] = $k;
      }
    } else {
      $output = 'importfileService.ERROR Unknown file imported';
    }

    # Compare both file name columns
    $nbColumnTemplate = count($arrayTemplateCsv[0]);
    $nbColumnFileImport = count($arrayFileImport[0]);
    if ($nbColumnFileImport > 0 && $nbColumnTemplate == $nbColumnFileImport) {
      for ($c = 0; $c < $nbColumnTemplate; $c++) {
        if ($column_name_template[$c] != $column_name[$c]) {
          $output = 'importfileService.ERROR bad column in CSV file';
        }
      }
    } else {
      $output = 'importfileService.ERROR bad number of columns in CSV file';
    }

    return ($output);
  }

  /**
   *  TransformNameForSymfony($field_name_csv, $type='field')
   * function that transforms db_field_name into db_field_name used by symfony
   * return for field_name_csv = fieldNameCsv (cas $type=field)
   * return for  field_name_csv = FieldNameCsv (cas $type=entity)
   * return for  field_name_csv = setFieldNameCsv (cas $type=set)
   */
  public function TransformNameForSymfony($field_name_csv, $type = 'field', $db_version = 1) {
    $array_fieldnameV2_fielnameV1 = [
      'access_point_voc_fk' => 'access_point_voc_fk',
      'box_code' => 'code',
      'box_comments' => 'comment',
      'box_title' => 'label',
      'box_type_voc_fk' => 'storage_type_voc_fk',
      'chromato_primer_voc_fk' => 'primer_chromato_voc_fk',
      'chromato_quality_voc_fk' => 'quality_voc_fk',
      'chromatogram_code' => 'code_chromato',
      'chromatogram_comments' => 'comment',
      'chromatogram_fk' => 'chromatogram_fk',
      'chromatogram_number' => 'yas_number',
      'clade' => 'clade',
      'collection_code_voc_fk' => 'collection_code_voc_fk',
      'collection_slide_code' => 'code',
      'collection_type_voc_fk' => 'collection_type_voc_fk',
      'coordinate_precision_voc_fk' => 'coordinates_precision_voc_fk',
      'coordinator_names' => 'coordinators',
      'country_code' => 'code_pays',
      'country_fk' => 'country_fk',
      'country_name' => 'nom_pays',
      'creation_user_name' => 'meta_creation_user',
      'csv_file_name' => 'filename',
      'date_of_update' => 'meta_update_date',
      'date_precision_voc_fk' => 'date_precision_voc_fk',
      'delimitation_method_voc_fk' => 'method_voc_fk',
      'dna_code' => 'code',
      'dna_comments' => 'comment',
      'dna_concentration' => 'concentration_ng_microlitre',
      'dna_extraction_date' => 'date',
      'dna_extraction_method_voc_fk' => 'extraction_method_voc_fk',
      'dna_fk' => 'dna_fk',
      'dna_fk' => 'dna_fk',
      'dna_quality_voc_fk' => 'quality',
      'donation_voc_fk' => 'donation',
      'elevation' => 'altitude_m',
      'email' => 'email',
      'ending_year' => 'end_year',
      'external_biological_material_code' => 'code_external_lot',
      'external_biological_material_comments' => 'comment',
      'external_biological_material_creation_date' => 'date_creation_external_lot',
      'external_biological_material_fk' => 'external_lot_fk',
      'external_sequence_accession_number' => 'accession_number',
      'external_sequence_alignment_code' => 'alignment_code',
      'external_sequence_code' => 'code',
      'external_sequence_comments' => 'comment',
      'external_sequence_creation_date' => 'date_creation',
      'external_sequence_fk' => 'external_sequence_fk',
      'external_sequence_origin_voc_fk' => 'origin_voc_fk',
      'external_sequence_primary_taxon' => 'primary_taxon',
      'external_sequence_specimen_number' => 'specimen_molecular_number',
      'external_sequence_status_voc_fk' => 'status',
      'eyes_voc_fk' => 'eyes_voc_fk',
      'family' => 'family',
      'fixative_voc_fk' => 'fixative_voc_fk',
      'forward_primer_voc_fk' => 'primer_start_voc_fk',
      'funding_agency' => 'funding_agency',
      'gene_voc_fk' => 'gene_voc_fk',
      'genus' => 'genus',
      'habitat_type_voc_fk' => 'habitat_type_voc_fk',
      'id' => 'id',
      'identification_criterion_voc_fk' => 'identification_criterion_voc_fk',
      'identification_date' => 'identification_date',
      'identified_species_comments' => 'comment',
      'identified_species_fk' => 'taxon_identification_fk',
      'institution_comments' => 'comment',
      'institution_fk' => 'institution_fk',
      'institution_name' => 'name',
      'institution' => 'institution',
      'internal_biological_material_code' => 'code',
      'internal_biological_material_comments' => 'comment',
      'internal_biological_material_composition_comments' => 'comment',
      'internal_biological_material_date' => 'date',
      'internal_biological_material_fk' => 'lot_materiel_fk',
      'internal_biological_material_status' => 'status',
      'internal_sequence_accession_number' => 'accession_number',
      'internal_sequence_alignment_code' => 'alignment_code',
      'internal_sequence_code' => 'code',
      'internal_sequence_comments' => 'comment',
      'internal_sequence_creation_date' => 'creation_date',
      'internal_sequence_fk' => 'internal_sequence_fk',
      'internal_sequence_status_voc_fk' => 'status',
      'is_active' => 'is_active',
      'latitude' => 'lat_deg_dec',
      'location_info' => 'location_info',
      'longitude' => 'long_deg_dec',
      'motu_comments' => 'comment',
      'motu_date' => 'date',
      'motu_dataset_fk' => 'motu_dataset_fk',
      'motu_dataset_fk' => 'motu_dataset_fk',
      'motu_number' => 'motu_number',
      'motu_title' => 'title',
      'municipality_code' => 'code',
      'municipality_fk' => 'municipality_fk',
      'municipality_name' => 'name',
      'name' => 'name',
      'specimen_count_comments' => 'specimen_quantity_comment',
      'specimen_count_voc_fk' => 'specimen_quantity_voc_fk',
      'specimen_count' => 'specimen_count',
      'parent' => 'parent',
      'password' => 'password',
      'pcr_code' => 'code',
      'pcr_comments' => 'comment',
      'pcr_date' => 'date',
      'pcr_details' => 'details',
      'pcr_fk' => 'pcr_fk',
      'pcr_number' => 'number',
      'pcr_quality_voc_fk' => 'quality_voc_fk',
      'pcr_specificity_voc_fk' => 'specificity_voc_fk',
      'person_comments' => 'comment',
      'person_fk' => 'person_fk',
      'person_full_name' => 'full_name',
      'person_name_bis' => 'nom_person_ref',
      'person_name' => 'name',
      'PG_FM_English database field name (db_gotit2)' => 'Database field name',
      'photo_folder_name' => 'picture_folder',
      'pigmentation_voc_fk' => 'pigmentation_voc_fk',
      'program_code' => 'code',
      'program_comments' => 'if ($db_version == 2) {
        comment',
      'program_fk' => 'program_fk',
      'program_name' => 'name',
      'region_name' => 'region',
      'reverse_primer_voc_fk' => 'primer_end_voc_fk',
      'role' => 'role',
      'salt' => 'salt',
      'sample_code' => 'code',
      'sample_status' => 'status',
      'sampling_comments' => 'comment',
      'sampling_date' => 'date',
      'sampling_duration' => 'duration_mn',
      'sampling_fk' => 'sampling_fk',
      'sampling_method_voc_fk' => 'sampling_method_voc_fk',
      'sequencing_advice' => 'sequencing_advice',
      'site_code' => 'code',
      'site_comments' => 'comment',
      'site_description' => 'description',
      'site_fk' => 'site_fk',
      'site_name' => 'name',
      'slide_comments' => 'comment',
      'slide_date' => 'date',
      'slide_title' => 'label',
      'source_code' => 'code',
      'source_comments' => 'comment',
      'source_fk' => 'source_fk',
      'source_title' => 'title',
      'source_year' => 'year',
      'species' => 'species',
      'specific_conductance' => 'conductance_micro_sie_cm',
      'specimen_comments' => 'comment',
      'specimen_fk' => 'specimen_fk',
      'specimen_molecular_code' => 'molecular_code',
      'specimen_molecular_number' => 'molecular_number',
      'specimen_morphological_code' => 'code_ind_tri_morpho',
      'specimen_slide_fk' => 'slide_fk',
      'specimen_type_voc_fk' => 'specimen_type_voc_fk',
      'starting_year' => 'start_year',
      'storage_box_fk' => 'store_fk',
      'subclass' => 'subclass',
      'subspecies' => 'subspecies',
      'taxon_code' => 'code',
      'taxon_comments' => 'comment',
      'taxon_fk' => 'taxon_fk',
      'taxon_name' => 'taxname',
      'taxon_order' => 'ordre',
      'taxon_rank' => 'rank',
      'taxon_synonym' => 'alias',
      'taxon_validity' => 'validity',
      'temperature' => 'temperature_c',
      'tube_code' => 'tube_code',
      'update_user_name' => 'user_maj',
      'user_comments' => 'comment',
      'username' => 'username',
      'voc_comments' => 'comment',
      'vocabulary_code' => 'code',
      'vocabulary_title' => 'label',
    ];
    // test to know if the version of the database is 2

    if ($db_version == 2) {
      $field_name_csv = $array_fieldnameV2_fielnameV1[$field_name_csv];

    }

    // the type can be 'field' or 'table'
    $field_name_csv_in_array = explode('_', $field_name_csv);
    $field_name_symfony = '';
    $compt = 0;
    foreach ($field_name_csv_in_array as $v) {
      if (!$compt && $type == 'field') {
        $field_name_symfony = $v;
      } else {
        $field_name_symfony = $field_name_symfony . ucfirst($v);
      }
      $compt++;
    }
    if ($type == 'set') {
      $field_name_symfony = 'set' . $field_name_symfony;
    }
    if ($type == 'get') {
      $field_name_symfony = 'get' . $field_name_symfony;
    }

    return $field_name_symfony;
  }

  /**
   *  suppCharSpeciaux($field, $type='')
   * deleting special characters
   */
  public function suppCharSpeciaux($data, $type = 'all') {
    // the type can be 'field' or 'table'
    switch ($type) {
    case " ":
      // space
      $data_corrected = str_replace(" ", "", $data);
      break;
    case "t":
      // tabulation
      $data_corrected = str_replace("\t", "", $data);
      break;
    case "n":
      // line break
      $data_corrected = str_replace("\n", "", $data);
      break;
    case "r":
      // carriage return
      $data_corrected = str_replace("\r", "", $data);
      break;
    case "O":
      // NULL
      $data_corrected = str_replace("\0", "", $data);
      break;
    case "x":
      // vertical tabulation
      $data_corrected = str_replace("\x0B", "", $data);
      break;
    case "tnrOx":
      $data_corrected = str_replace("\t", "", $data);
      $data_corrected = str_replace("\n", "", $data_corrected);
      $data_corrected = str_replace("\r", "", $data_corrected);
      $data_corrected = str_replace("\0", "", $data_corrected);
      $data_corrected = str_replace("\x0B", "", $data_corrected);
      break;
    case "all":
      $data_corrected = str_replace(" ", "", $data);
      $data_corrected = str_replace("\t", "", $data_corrected);
      $data_corrected = str_replace("\n", "", $data_corrected);
      $data_corrected = str_replace("\r", "", $data_corrected);
      $data_corrected = str_replace("\0", "", $data_corrected);
      $data_corrected = str_replace("\x0B", "", $data_corrected);
      break;
    default:
      $data_corrected = $data;
      echo "</br> le type de correction de la fonction suppCharSpeciaux n existe pas : type = " . $type;
      exit;
    }
    return $data_corrected;
  }

  /**
   *  GetCurrentTimestamp()
   * return the objet timestamp current
   */
  public function GetCurrentTimestamp() {
    // get the TIMESTAMP of import
    $dateImport = date("Y-m-d H:i:s");
    $DateImport = new \DateTime($dateImport);
    return $DateImport;
  }
}
