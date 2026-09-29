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
        //var_dump($data);     
        //var_dump($name);         
        if (!$flag_record) {
          // return the column name to NULL if there is no record
          for ($c = 0 ; $c < $num; $c++) {
            $result[0][$name[$c]] = $name[$c];
            //var_dump($result[0][$name[$c]]);
          }
        }
        //exit();
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
      if (array_key_exists($ent, $columnByTable)) { // add the name of the column "TableName.FieldName ..." to the table
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
    if (array_key_exists($nameTable, $columnByTable)) {
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
      'access_point_voc_fk' => 'point_acces_voc_fk',
      'box_code' => 'code_boite',
      'box_comments' => 'commentaire_boite',
      'box_title' => 'libelle_boite',
      'box_type_voc_fk' => 'type_boite_voc_fk',
      'chromato_primer_voc_fk' => 'primer_chromato_voc_fk',
      'chromato_quality_voc_fk' => 'qualite_chromato_voc_fk',
      'chromatogram_code' => 'code_chromato',
      'chromatogram_comments' => 'commentaire_chromato',
      'chromatogram_fk' => 'chromatogramme_fk',
      'chromatogram_number' => 'num_yas',
      'clade' => 'clade',
      'collection_code_voc_fk' => 'code_collection_voc_fk',
      'collection_slide_code' => 'code_lame_coll',
      'collection_type_voc_fk' => 'type_collection_voc_fk',
      'coordinate_precision_voc_fk' => 'precision_lat_long_voc_fk',
      'coordinator_names' => 'noms_responsables',
      'country_code' => 'code_pays',
      'country_fk' => 'pays_fk',
      'country_name' => 'nom_pays',
      'creation_user_name' => 'user_cre',
      'csv_file_name' => 'nom_fichier_csv',
      'date_of_update' => 'date_maj',
      'date_precision_voc_fk' => 'date_precision_voc_fk',
      'delimitation_method_voc_fk' => 'methode_motu_voc_fk',
      'dna_code' => 'code_adn',
      'dna_comments' => 'commentaire_adn',
      'dna_concentration' => 'concentration_ng_microlitre',
      'dna_extraction_date' => 'date_adn',
      'dna_extraction_method_voc_fk' => 'methode_extraction_adn_voc_fk',
      'dna_fk' => 'adn_fk',
      'dna_fk' => 'adn_fk',
      'dna_quality_voc_fk' => 'qualite_adn_voc_fk',
      'donation_voc_fk' => 'leg_voc_fk',
      'elevation' => 'altitude_m',
      'email' => 'email',
      'ending_year' => 'annee_fin',
      'external_biological_material_code' => 'code_lot_materiel_ext',
      'external_biological_material_comments' => 'commentaire_lot_materiel_ext',
      'external_biological_material_creation_date' => 'date_creation_lot_materiel_ext',
      'external_biological_material_fk' => 'lot_materiel_ext_fk',
      'external_sequence_accession_number' => 'accession_number_sqc_ass_ext',
      'external_sequence_alignment_code' => 'code_sqc_ass_ext_alignement',
      'external_sequence_code' => 'code_sqc_ass_ext',
      'external_sequence_comments' => 'commentaire_sqc_ass_ext',
      'external_sequence_creation_date' => 'date_creation_sqc_ass_ext',
      'external_sequence_fk' => 'sequence_assemblee_ext_fk',
      'external_sequence_origin_voc_fk' => 'origine_sqc_ass_ext_voc_fk',
      'external_sequence_primary_taxon' => 'taxon_origine_sqc_ass_ext',
      'external_sequence_specimen_number' => 'num_individu_sqc_ass_ext',
      'external_sequence_status_voc_fk' => 'statut_sqc_ass_voc_fk',
      'eyes_voc_fk' => 'yeux_voc_fk',
      'family' => 'family',
      'fixative_voc_fk' => 'fixateur_voc_fk',
      'forward_primer_voc_fk' => 'primer_pcr_start_voc_fk',
      'funding_agency' => 'type_financeur',
      'gene_voc_fk' => 'gene_voc_fk',
      'genus' => 'genus',
      'habitat_type_voc_fk' => 'habitat_type_voc_fk',
      'id' => 'id',
      'identification_criterion_voc_fk' => 'critere_identification_voc_fk',
      'identification_date' => 'date_identification',
      'identified_species_comments' => 'commentaire_esp_id',
      'identified_species_fk' => 'espece_identifiee_fk',
      'institution_comments' => 'commentaire_etablissement',
      'institution_fk' => 'etablissement_fk',
      'institution_name' => 'nom_etablissement',
      'institution' => 'institution',
      'internal_biological_material_code' => 'code_lot_materiel',
      'internal_biological_material_comments' => 'commentaire_lot_materiel',
      'internal_biological_material_composition_comments' => 'commentaire_compo_lot_materiel',
      'internal_biological_material_date' => 'date_lot_materiel',
      'internal_biological_material_fk' => 'lot_materiel_fk',
      'internal_biological_material_status' => 'a_faire',
      'internal_sequence_accession_number' => 'accession_number',
      'internal_sequence_alignment_code' => 'code_sqc_alignement',
      'internal_sequence_code' => 'code_sqc_ass',
      'internal_sequence_comments' => 'commentaire_sqc_ass',
      'internal_sequence_creation_date' => 'date_creation_sqc_ass',
      'internal_sequence_fk' => 'sequence_assemblee_fk',
      'internal_sequence_status_voc_fk' => 'statut_sqc_ass_voc_fk',
      'is_active' => 'is_active',
      'latitude' => 'lat_deg_dec',
      'location_info' => 'info_localisation',
      'longitude' => 'long_deg_dec',
      'motu_comments' => 'commentaire_motu',
      'motu_date' => 'date_motu',
      'motu_fk' => 'motu_fk',
      'motu_fk' => 'motu_fk',
      'motu_number' => 'num_motu',
      'motu_title' => 'libelle_motu',
      'municipality_code' => 'code_commune',
      'municipality_fk' => 'commune_fk',
      'municipality_name' => 'nom_commune',
      'name' => 'name',
      'number_of_specimens_comments' => 'commentaire_nb_individus',
      'number_of_specimens_voc_fk' => 'nb_individus_voc_fk',
      'number_of_specimens' => 'nb_individus',
      'parent' => 'parent',
      'password' => 'password',
      'pcr_code' => 'code_pcr',
      'pcr_comments' => 'remarque_pcr',
      'pcr_date' => 'date_pcr',
      'pcr_details' => 'detail_pcr',
      'pcr_fk' => 'pcr_fk',
      'pcr_number' => 'num_pcr',
      'pcr_quality_voc_fk' => 'qualite_pcr_voc_fk',
      'pcr_specificity_voc_fk' => 'specificite_voc_fk',
      'person_comments' => 'commentaire_personne',
      'person_fk' => 'personne_fk',
      'person_full_name' => 'nom_complet',
      'person_name_bis' => 'nom_personne_ref',
      'person_name' => 'nom_personne',
      'PG_FM_English database field name (db_gotit2)' => 'Database field name',
      'photo_folder_name' => 'nom_dossier_photos',
      'pigmentation_voc_fk' => 'pigmentation_voc_fk',
      'program_code' => 'code_programme',
      'program_comments' => 'if ($db_version == 2) {
        commentaire_programme',
      'program_fk' => 'programme_fk',
      'program_name' => 'nom_programme',
      'region_name' => 'nom_region',
      'reverse_primer_voc_fk' => 'primer_pcr_end_voc_fk',
      'role' => 'role',
      'salt' => 'salt',
      'sample_code' => 'code_collecte',
      'sample_status' => 'a_faire',
      'sampling_comments' => 'commentaire_collecte',
      'sampling_date' => 'date_collecte',
      'sampling_duration' => 'duree_echantillonnage_mn',
      'sampling_fk' => 'collecte_fk',
      'sampling_method_voc_fk' => 'sampling_method_voc_fk',
      'sequencing_advice' => 'commentaire_conseil_sqc',
      'site_code' => 'code_station',
      'site_comments' => 'commentaire_station',
      'site_description' => 'info_description',
      'site_fk' => 'station_fk',
      'site_name' => 'nom_station',
      'slide_comments' => 'commentaire_lame',
      'slide_date' => 'date_lame',
      'slide_title' => 'libelle_lame',
      'source_code' => 'code_source',
      'source_comments' => 'commentaire_source',
      'source_fk' => 'source_fk',
      'source_title' => 'libelle_source',
      'source_year' => 'annee_source',
      'species' => 'species',
      'specific_conductance' => 'conductivite_micro_sie_cm',
      'specimen_comments' => 'commentaire_ind',
      'specimen_fk' => 'individu_fk',
      'specimen_molecular_code' => 'code_ind_biomol',
      'specimen_molecular_number' => 'num_ind_biomol',
      'specimen_morphological_code' => 'code_ind_tri_morpho',
      'specimen_slide_fk' => 'individu_lame_fk',
      'specimen_type_voc_fk' => 'type_individu_voc_fk',
      'starting_year' => 'annee_debut',
      'storage_box_fk' => 'boite_fk',
      'subclass' => 'subclass',
      'subspecies' => 'subspecies',
      'taxon_code' => 'code_taxon',
      'taxon_comments' => 'commentaire_ref',
      'taxon_fk' => 'referentiel_taxon_fk',
      'taxon_name' => 'taxname',
      'taxon_order' => 'ordre',
      'taxon_rank' => 'rank',
      'taxon_synonym' => 'taxname_ref',
      'taxon_validity' => 'validity',
      'temperature' => 'temperature_c',
      'tube_code' => 'code_tube',
      'update_user_name' => 'user_maj',
      'user_comments' => 'commentaire_user',
      'username' => 'username',
      'voc_comments' => 'commentaire',
      'vocabulary_code' => 'code',
      'vocabulary_title' => 'libelle',
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
    if ($type == 'add') {
      $field_name_symfony = 'add' . $field_name_symfony;
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
