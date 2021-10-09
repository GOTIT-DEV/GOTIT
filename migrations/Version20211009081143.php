<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211009081143 extends AbstractMigration {
  public function getDescription(): string {
    return 'Rework entities property types';
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE chromatogram ALTER id TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_primer_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_quality_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER institution_fk TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER institution_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER pcr_fk TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER pcr_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE chromatogram ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE composition_of_internal_biological_material ALTER id TYPE INT');
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER specimen_type_voc_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER specimen_type_voc_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER internal_biological_material_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER creation_user_name TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER creation_user_name DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER update_user_name TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER update_user_name DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER number_of_specimens TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER number_of_specimens DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE country ALTER id TYPE INT');
    $this->addSql('ALTER TABLE country ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE country ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE country ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE country ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE country ALTER country_name TYPE VARCHAR(255)');
    $this->addSql('ALTER TABLE dna ALTER id TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER dna_extraction_method_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER dna_extraction_method_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER specimen_fk TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER dna_quality_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER dna_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER storage_box_fk TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE dna ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER dna_fk TYPE INT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER dna_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER id TYPE INT');
    $this->addSql('ALTER TABLE external_biological_material ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE external_biological_material ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER date_precision_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER date_precision_voc_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER number_of_specimens_voc_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER number_of_specimens_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_biological_material ALTER pigmentation_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER pigmentation_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_biological_material ALTER eyes_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE external_biological_material ALTER eyes_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE external_biological_material ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE external_biological_material ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER person_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER person_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER external_biological_material_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER external_biological_material_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER source_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER source_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER id TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER gene_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER gene_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER external_sequence_origin_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_origin_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER external_sequence_status_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_status_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE external_sequence ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_sequence_is_entered_by ALTER external_sequence_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence_is_entered_by ALTER external_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence_is_entered_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE external_sequence_is_entered_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence_is_published_in ALTER source_fk TYPE INT');
    $this->addSql('ALTER TABLE external_sequence_is_published_in ALTER source_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_sequence_is_published_in ALTER external_sequence_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence_is_published_in ALTER external_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER id TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER identification_criterion_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE identified_species ALTER identification_criterion_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER external_sequence_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER external_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER external_biological_material_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE identified_species ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER internal_biological_material_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE identified_species ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER taxon_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER taxon_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER specimen_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER internal_sequence_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER internal_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER type_material_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE identified_species ALTER type_material_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER identified_species_fk TYPE INT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER identified_species_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE institution ALTER id TYPE INT');
    $this->addSql('ALTER TABLE institution ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE institution ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE institution ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE institution ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER id TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER date_precision_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER date_precision_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_biological_material ALTER pigmentation_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER pigmentation_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_biological_material ALTER eyes_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER eyes_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER storage_box_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER internal_biological_material_status TYPE BOOLEAN
        using CASE WHEN internal_biological_material_status=0 THEN FALSE ELSE TRUE END'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER internal_biological_material_status DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER internal_biological_material_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER person_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER person_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER internal_biological_material_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER source_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER source_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence ALTER id TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_sequence ALTER internal_sequence_status_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE internal_sequence ALTER internal_sequence_status_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_sequence ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_sequence_is_assembled_by ALTER internal_sequence_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence_is_assembled_by ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence_is_assembled_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence_is_assembled_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_sequence_is_published_in ALTER source_fk TYPE INT');
    $this->addSql('ALTER TABLE internal_sequence_is_published_in ALTER source_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_sequence_is_published_in ALTER internal_sequence_fk TYPE INT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence_is_published_in ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE chromatogram_is_processed_to ALTER chromatogram_fk TYPE INT');
    $this->addSql('ALTER TABLE chromatogram_is_processed_to ALTER chromatogram_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram_is_processed_to ALTER internal_sequence_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE chromatogram_is_processed_to ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE motu ALTER id TYPE INT');
    $this->addSql('ALTER TABLE motu ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE motu ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE motu ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER motu_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER motu_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER id TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER external_sequence_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER external_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER delimitation_method_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER delimitation_method_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER internal_sequence_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER internal_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_fk TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_number TYPE INT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_number DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER id TYPE INT');
    $this->addSql('ALTER TABLE municipality ALTER country_fk TYPE INT');
    $this->addSql('ALTER TABLE municipality ALTER country_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE municipality ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE municipality ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER id TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER gene_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER gene_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_quality_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_specificity_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_specificity_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER forward_primer_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER forward_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER reverse_primer_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER reverse_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER dna_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER dna_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE pcr ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER pcr_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER pcr_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER id TYPE INT');
    $this->addSql('ALTER TABLE person ALTER institution_fk TYPE INT');
    $this->addSql('ALTER TABLE person ALTER institution_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE person ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE person ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER id TYPE INT');
    $this->addSql('ALTER TABLE program ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE program ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE program ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER starting_year TYPE SMALLINT');
    $this->addSql('ALTER TABLE program ALTER starting_year DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER ending_year TYPE SMALLINT');
    $this->addSql('ALTER TABLE program ALTER ending_year DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER id TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER donation_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER donation_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER site_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER site_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER sampling_duration TYPE INT');
    $this->addSql('ALTER TABLE sampling ALTER sampling_duration DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER sample_status TYPE BOOLEAN
    using CASE WHEN sample_status=0 THEN FALSE ELSE TRUE END');
    $this->addSql('ALTER TABLE sampling ALTER sample_status DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_done_with_method ALTER sampling_method_voc_fk TYPE INT');
    $this->addSql(
      'ALTER TABLE sampling_is_done_with_method ALTER sampling_method_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE sampling_is_done_with_method ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling_is_done_with_method ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER fixative_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER fixative_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER program_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER program_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER sampling_fk TYPE INT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER taxon_fk TYPE INT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER taxon_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER id TYPE INT');
    $this->addSql('ALTER TABLE site ALTER municipality_fk TYPE INT');
    $this->addSql('ALTER TABLE site ALTER municipality_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER country_fk TYPE INT');
    $this->addSql('ALTER TABLE site ALTER country_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER access_point_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE site ALTER access_point_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER habitat_type_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE site ALTER habitat_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER coordinate_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE site ALTER coordinate_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE site ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE site ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER elevation TYPE INT');
    $this->addSql('ALTER TABLE site ALTER elevation DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER id TYPE INT');
    $this->addSql('ALTER TABLE source ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE source ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE source ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER source_year TYPE SMALLINT');
    $this->addSql('ALTER TABLE source ALTER source_year DROP DEFAULT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER source_fk TYPE INT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER source_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER id TYPE INT');
    $this->addSql('ALTER TABLE specimen ALTER specimen_type_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE specimen ALTER specimen_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER internal_biological_material_fk TYPE INT');
    $this->addSql('ALTER TABLE specimen ALTER internal_biological_material_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE specimen ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE specimen ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER id TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER date_precision_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER storage_box_fk TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER specimen_fk TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE specimen_slide ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER specimen_slide_fk TYPE INT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER specimen_slide_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER person_fk TYPE INT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER id TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_type_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_code_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_code_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER box_type_voc_fk TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER box_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE storage_box ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER id TYPE INT');
    $this->addSql('ALTER TABLE taxon ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE taxon ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE taxon ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER taxon_validity TYPE BOOLEAN
    using CASE WHEN taxon_validity=0 THEN FALSE ELSE TRUE END');
    $this->addSql('ALTER TABLE taxon ALTER taxon_validity DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER id TYPE INT');
    $this->addSql('ALTER TABLE user_db ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE user_db ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE user_db ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER user_is_active TYPE BOOLEAN
    using CASE WHEN user_is_active=0 THEN FALSE ELSE TRUE END');
    $this->addSql('ALTER TABLE user_db ALTER user_is_active DROP DEFAULT');
    $this->addSql('ALTER TABLE vocabulary ALTER id TYPE INT');
    $this->addSql('ALTER TABLE vocabulary ALTER creation_user_name TYPE INT');
    $this->addSql('ALTER TABLE vocabulary ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE vocabulary ALTER update_user_name TYPE INT');
    $this->addSql('ALTER TABLE vocabulary ALTER update_user_name DROP DEFAULT');
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE country ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE country ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE country ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE country ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE country ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE country ALTER country_name TYPE VARCHAR(1024)');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER taxon_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE has_targeted_taxa ALTER taxon_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE external_biological_material ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_biological_material ALTER sampling_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER date_precision_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER date_precision_voc_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER number_of_specimens_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER number_of_specimens_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_biological_material ALTER pigmentation_voc_fk TYPE BIGINT');
    $this->addSql(
      'ALTER TABLE external_biological_material ALTER pigmentation_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_biological_material ALTER eyes_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_biological_material ALTER eyes_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE external_biological_material ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE external_biological_material ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE external_biological_material ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_sequence_is_entered_by ALTER external_sequence_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence_is_entered_by ALTER external_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence_is_entered_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence_is_entered_by ALTER person_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER external_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER person_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_processed_by ALTER person_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE dna ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER dna_extraction_method_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER dna_extraction_method_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER dna_quality_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER dna_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER specimen_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER storage_box_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE dna ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE dna ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE composition_of_internal_biological_material ALTER id TYPE BIGINT');
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER specimen_type_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER specimen_type_voc_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER internal_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER creation_user_name TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER creation_user_name DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER update_user_name TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER update_user_name DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER number_of_specimens TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE composition_of_internal_biological_material ALTER number_of_specimens DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER gene_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER gene_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_origin_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_origin_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER sampling_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_status_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence ALTER external_sequence_status_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE external_sequence ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_primer_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_quality_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER chromato_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER institution_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER institution_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER pcr_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER pcr_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE chromatogram ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE chromatogram_is_processed_to ALTER internal_sequence_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE chromatogram_is_processed_to ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE chromatogram_is_processed_to ALTER chromatogram_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE chromatogram_is_processed_to ALTER chromatogram_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER external_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER source_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_biological_material_is_published_in ALTER source_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence_is_published_in ALTER external_sequence_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE external_sequence_is_published_in ALTER external_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE external_sequence_is_published_in ALTER source_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE external_sequence_is_published_in ALTER source_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER taxon_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER taxon_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER type_material_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER type_material_voc_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE identified_species ALTER identification_criterion_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE identified_species ALTER identification_criterion_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER external_sequence_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER external_sequence_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE identified_species ALTER external_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE identified_species ALTER external_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE identified_species ALTER internal_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE identified_species ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE identified_species ALTER specimen_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER internal_sequence_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER internal_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE identified_species ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE identified_species ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER internal_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER person_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_treated_by ALTER person_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_biological_material ALTER id TYPE BIGINT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER date_precision_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER date_precision_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_biological_material ALTER pigmentation_voc_fk TYPE BIGINT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER pigmentation_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_biological_material ALTER eyes_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER eyes_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER storage_box_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_biological_material ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER internal_biological_material_status TYPE SMALLINT
      using CASE WHEN internal_biological_material_status=FALSE THEN 0 ELSE 1 END'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material ALTER internal_biological_material_status DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_sequence ALTER internal_sequence_status_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence ALTER internal_sequence_status_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE internal_sequence ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence ALTER update_user_name DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER internal_biological_material_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER internal_biological_material_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER source_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_biological_material_is_published_in ALTER source_fk DROP DEFAULT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence_is_assembled_by ALTER internal_sequence_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence_is_assembled_by ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence_is_assembled_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence_is_assembled_by ALTER person_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE internal_sequence_is_published_in ALTER internal_sequence_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE internal_sequence_is_published_in ALTER internal_sequence_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE internal_sequence_is_published_in ALTER source_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE internal_sequence_is_published_in ALTER source_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER external_sequence_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER external_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER delimitation_method_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER delimitation_method_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER internal_sequence_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER internal_sequence_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_number TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_number ALTER motu_number DROP DEFAULT');
    $this->addSql('ALTER TABLE motu ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE motu ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE motu ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE motu ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER motu_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER motu_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE motu_is_generated_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER specimen_slide_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER specimen_slide_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE slide_is_mounted_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER gene_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER gene_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_quality_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_quality_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_specificity_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER pcr_specificity_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER forward_primer_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER forward_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER reverse_primer_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER reverse_primer_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER dna_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER dna_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE program ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE program ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE program ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER starting_year TYPE BIGINT');
    $this->addSql('ALTER TABLE program ALTER starting_year DROP DEFAULT');
    $this->addSql('ALTER TABLE program ALTER ending_year TYPE BIGINT');
    $this->addSql('ALTER TABLE program ALTER ending_year DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER donation_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER donation_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER site_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER site_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER sampling_duration TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling ALTER sampling_duration DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling ALTER sample_status TYPE
    using CASE WHEN sample_status=FALSE THEN 0 ELSE 1 END');
    $this->addSql('ALTER TABLE sampling ALTER sample_status DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling_is_performed_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE person ALTER institution_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE person ALTER institution_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE person ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE person ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE person ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER pcr_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER pcr_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE pcr_is_done_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE municipality ALTER country_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE municipality ALTER country_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE municipality ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE municipality ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE municipality ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_done_with_method ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling_is_done_with_method ALTER sampling_fk DROP DEFAULT');
    $this->addSql(
      'ALTER TABLE sampling_is_done_with_method ALTER sampling_method_voc_fk TYPE BIGINT'
    );
    $this->addSql(
      'ALTER TABLE sampling_is_done_with_method ALTER sampling_method_voc_fk DROP DEFAULT'
    );
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER fixative_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sample_is_fixed_with ALTER fixative_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER municipality_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER municipality_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER country_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER country_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER access_point_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER access_point_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER habitat_type_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER habitat_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER coordinate_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER coordinate_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE site ALTER elevation TYPE BIGINT');
    $this->addSql('ALTER TABLE site ALTER elevation DROP DEFAULT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER source_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER source_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE source_is_entered_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE source ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE source ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE source ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE source ALTER source_year TYPE BIGINT');
    $this->addSql('ALTER TABLE source ALTER source_year DROP DEFAULT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER identified_species_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER identified_species_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE species_is_identified_by ALTER person_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen ALTER specimen_type_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen ALTER specimen_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER internal_biological_material_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen ALTER internal_biological_material_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE taxon ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE taxon ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE taxon ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE taxon ALTER taxon_validity TYPE SMALLINT
    using CASE WHEN taxon_validity=FALSE THEN 0 ELSE 1 END');
    $this->addSql('ALTER TABLE taxon ALTER taxon_validity DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_type_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_code_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER collection_code_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER box_type_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER box_type_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE storage_box ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE storage_box ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE user_db ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE user_db ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE user_db ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE user_db ALTER user_is_active TYPE SMALLINT
    using CASE WHEN user_is_active=FALSE THEN 0 ELSE 1 END');
    $this->addSql('ALTER TABLE user_db ALTER user_is_active DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER sampling_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER sampling_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER program_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE sampling_is_funded_by ALTER program_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE vocabulary ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE vocabulary ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE vocabulary ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE vocabulary ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE vocabulary ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER date_precision_voc_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER date_precision_voc_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER storage_box_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER storage_box_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER specimen_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER specimen_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE specimen_slide ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE specimen_slide ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE institution ALTER id TYPE BIGINT');
    $this->addSql('ALTER TABLE institution ALTER creation_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE institution ALTER creation_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE institution ALTER update_user_name TYPE BIGINT');
    $this->addSql('ALTER TABLE institution ALTER update_user_name DROP DEFAULT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER dna_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER dna_fk DROP DEFAULT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER person_fk TYPE BIGINT');
    $this->addSql('ALTER TABLE dna_is_extracted_by ALTER person_fk DROP DEFAULT');
  }
}
