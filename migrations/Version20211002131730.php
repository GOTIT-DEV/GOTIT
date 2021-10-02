<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002131730 extends AbstractMigration {
  public function getDescription(): string {
    return 'Initial schema definition';
  }

  public function up(Schema $schema): void {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE country (id BIGSERIAL NOT NULL, country_code VARCHAR(255) NOT NULL, country_name VARCHAR(1024) NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_country__country_code ON country (country_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE has_targeted_taxa (id BIGSERIAL NOT NULL, sampling_fk BIGINT NOT NULL, taxon_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_c0df0ce47b09e3bc ON has_targeted_taxa (taxon_fk)');
    $this->addSql('CREATE INDEX idx_c0df0ce4662d9b98 ON has_targeted_taxa (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_biological_material (id BIGSERIAL NOT NULL, sampling_fk BIGINT NOT NULL, date_precision_voc_fk BIGINT NOT NULL, number_of_specimens_voc_fk BIGINT NOT NULL, pigmentation_voc_fk BIGINT NOT NULL, eyes_voc_fk BIGINT NOT NULL, external_biological_material_code VARCHAR(255) NOT NULL, external_biological_material_creation_date DATE DEFAULT NULL, external_biological_material_comments TEXT DEFAULT NULL, number_of_specimens_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_eefa43f382acdc4 ON external_biological_material (number_of_specimens_voc_fk)');
    $this->addSql('CREATE INDEX idx_eefa43f3b0b56b73 ON external_biological_material (pigmentation_voc_fk)');
    $this->addSql('CREATE INDEX idx_eefa43f3a30c442f ON external_biological_material (date_precision_voc_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_external_biological_material__external_biological_material_c ON external_biological_material (external_biological_material_code)');
    $this->addSql('CREATE INDEX idx_eefa43f3a897cc9e ON external_biological_material (eyes_voc_fk)');
    $this->addSql('CREATE INDEX idx_eefa43f3662d9b98 ON external_biological_material (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_sequence_is_entered_by (external_sequence_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL)');
    $this->addSql('CREATE INDEX idx_dc41e25ab53cd04c ON external_sequence_is_entered_by (person_fk)');
    $this->addSql('CREATE INDEX idx_dc41e25acdd1f756 ON external_sequence_is_entered_by (external_sequence_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_biological_material_is_processed_by (id BIGSERIAL NOT NULL, person_fk BIGINT NOT NULL, external_biological_material_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_7d78636fb53cd04c ON external_biological_material_is_processed_by (person_fk)');
    $this->addSql('CREATE INDEX idx_7d78636f40d80ecd ON external_biological_material_is_processed_by (external_biological_material_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE composition_of_internal_biological_material (id BIGSERIAL NOT NULL, specimen_type_voc_fk BIGINT NOT NULL, internal_biological_material_fk BIGINT NOT NULL, number_of_specimens BIGINT DEFAULT NULL, internal_biological_material_composition_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_10a6974454dbbd4d ON composition_of_internal_biological_material (internal_biological_material_fk)');
    $this->addSql('CREATE INDEX idx_10a697444236d33e ON composition_of_internal_biological_material (specimen_type_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_sequence (id BIGSERIAL NOT NULL, gene_voc_fk BIGINT NOT NULL, date_precision_voc_fk BIGINT NOT NULL, external_sequence_origin_voc_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, external_sequence_status_voc_fk BIGINT NOT NULL, external_sequence_code VARCHAR(1024) NOT NULL, external_sequence_creation_date DATE DEFAULT NULL, external_sequence_accession_number VARCHAR(255) NOT NULL, external_sequence_alignment_code VARCHAR(1024) DEFAULT NULL, external_sequence_specimen_number VARCHAR(255) NOT NULL, external_sequence_primary_taxon VARCHAR(255) DEFAULT NULL, external_sequence_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_external_sequence__external_sequence_alignment_code ON external_sequence (external_sequence_alignment_code)');
    $this->addSql('CREATE INDEX idx_9e9f85cf88085e0f ON external_sequence (external_sequence_status_voc_fk)');
    $this->addSql('CREATE INDEX idx_9e9f85cf662d9b98 ON external_sequence (sampling_fk)');
    $this->addSql('CREATE INDEX idx_9e9f85cf9d3cdb05 ON external_sequence (gene_voc_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_external_sequence__external_sequence_code ON external_sequence (external_sequence_code)');
    $this->addSql('CREATE INDEX idx_9e9f85cf514d78e0 ON external_sequence (external_sequence_origin_voc_fk)');
    $this->addSql('CREATE INDEX idx_9e9f85cfa30c442f ON external_sequence (date_precision_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE dna (id BIGSERIAL NOT NULL, date_precision_voc_fk BIGINT NOT NULL, dna_extraction_method_voc_fk BIGINT NOT NULL, specimen_fk BIGINT NOT NULL, dna_quality_voc_fk BIGINT NOT NULL, storage_box_fk BIGINT DEFAULT NULL, dna_code VARCHAR(255) NOT NULL, dna_extraction_date DATE DEFAULT NULL, dna_concentration DOUBLE PRECISION DEFAULT NULL, dna_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_dna__dna_code ON dna (dna_code)');
    $this->addSql('CREATE INDEX idx_dna__specimen_fk ON dna (specimen_fk)');
    $this->addSql('CREATE INDEX idx_1dcf9af9c53b46b ON dna (dna_quality_voc_fk)');
    $this->addSql('CREATE INDEX idx_dna__date_precision_voc_fk ON dna (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_dna__storage_box_fk ON dna (storage_box_fk)');
    $this->addSql('CREATE INDEX idx_dna__dna_extraction_method_voc_fk ON dna (dna_extraction_method_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE chromatogram (id BIGSERIAL NOT NULL, chromato_primer_voc_fk BIGINT NOT NULL, chromato_quality_voc_fk BIGINT NOT NULL, institution_fk BIGINT NOT NULL, pcr_fk BIGINT NOT NULL, chromatogram_code VARCHAR(255) NOT NULL, chromatogram_number VARCHAR(255) NOT NULL, chromatogram_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_fcb2dab72b63d494 ON chromatogram (pcr_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_chromatogram__chromatogram_code ON chromatogram (chromatogram_code)');
    $this->addSql('CREATE INDEX idx_fcb2dab7206fe5c0 ON chromatogram (chromato_quality_voc_fk)');
    $this->addSql('CREATE INDEX idx_fcb2dab7e8441376 ON chromatogram (institution_fk)');
    $this->addSql('CREATE INDEX idx_fcb2dab7286bbca9 ON chromatogram (chromato_primer_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE chromatogram_is_processed_to (id BIGSERIAL NOT NULL, chromatogram_fk BIGINT NOT NULL, internal_sequence_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_bd45639e5be90e48 ON chromatogram_is_processed_to (internal_sequence_fk)');
    $this->addSql('CREATE INDEX idx_bd45639eefcfd332 ON chromatogram_is_processed_to (chromatogram_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_biological_material_is_published_in (id BIGSERIAL NOT NULL, external_biological_material_fk BIGINT NOT NULL, source_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_d2338bb2821b1d3f ON external_biological_material_is_published_in (source_fk)');
    $this->addSql('CREATE INDEX idx_d2338bb240d80ecd ON external_biological_material_is_published_in (external_biological_material_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE external_sequence_is_published_in (id BIGSERIAL NOT NULL, source_fk BIGINT NOT NULL, external_sequence_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_8d0e8d6acdd1f756 ON external_sequence_is_published_in (external_sequence_fk)');
    $this->addSql('CREATE INDEX idx_8d0e8d6a821b1d3f ON external_sequence_is_published_in (source_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE identified_species (id BIGSERIAL NOT NULL, identification_criterion_voc_fk BIGINT NOT NULL, date_precision_voc_fk BIGINT NOT NULL, external_sequence_fk BIGINT DEFAULT NULL, external_biological_material_fk BIGINT DEFAULT NULL, internal_biological_material_fk BIGINT DEFAULT NULL, taxon_fk BIGINT NOT NULL, specimen_fk BIGINT DEFAULT NULL, internal_sequence_fk BIGINT DEFAULT NULL, type_material_voc_fk BIGINT DEFAULT NULL, identification_date DATE DEFAULT NULL, identified_species_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_801c3911b669f53d ON identified_species (type_material_voc_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8d5f2c6176 ON identified_species (specimen_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8d54dbbd4d ON identified_species (internal_biological_material_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8d40d80ecd ON identified_species (external_biological_material_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8dcdd1f756 ON identified_species (external_sequence_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8da30c442f ON identified_species (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8d5be90e48 ON identified_species (internal_sequence_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8dfb5f790 ON identified_species (identification_criterion_voc_fk)');
    $this->addSql('CREATE INDEX idx_49d19c8d7b09e3bc ON identified_species (taxon_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_biological_material_is_treated_by (id BIGSERIAL NOT NULL, internal_biological_material_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_69c58aff54dbbd4d ON internal_biological_material_is_treated_by (internal_biological_material_fk)');
    $this->addSql('CREATE INDEX idx_69c58affb53cd04c ON internal_biological_material_is_treated_by (person_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_biological_material (id BIGSERIAL NOT NULL, date_precision_voc_fk BIGINT NOT NULL, pigmentation_voc_fk BIGINT NOT NULL, eyes_voc_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, storage_box_fk BIGINT DEFAULT NULL, internal_biological_material_code VARCHAR(255) NOT NULL, internal_biological_material_date DATE DEFAULT NULL, sequencing_advice TEXT DEFAULT NULL, internal_biological_material_comments TEXT DEFAULT NULL, internal_biological_material_status SMALLINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_ba1841a52b644673 ON internal_biological_material (storage_box_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_internal_biological_material__internal_biological_material_c ON internal_biological_material (internal_biological_material_code)');
    $this->addSql('CREATE INDEX idx_ba1841a5b0b56b73 ON internal_biological_material (pigmentation_voc_fk)');
    $this->addSql('CREATE INDEX idx_ba1841a5a897cc9e ON internal_biological_material (eyes_voc_fk)');
    $this->addSql('CREATE INDEX idx_ba1841a5a30c442f ON internal_biological_material (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_ba1841a5662d9b98 ON internal_biological_material (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_sequence (id BIGSERIAL NOT NULL, date_precision_voc_fk BIGINT NOT NULL, internal_sequence_status_voc_fk BIGINT NOT NULL, internal_sequence_code VARCHAR(1024) NOT NULL, internal_sequence_creation_date DATE DEFAULT NULL, internal_sequence_accession_number VARCHAR(255) DEFAULT NULL, internal_sequence_alignment_code VARCHAR(1024) DEFAULT NULL, internal_sequence_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_353cf66988085e0f ON internal_sequence (internal_sequence_status_voc_fk)');
    $this->addSql('CREATE INDEX idx_353cf669a30c442f ON internal_sequence (date_precision_voc_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_internal_sequence__internal_sequence_alignment_code ON internal_sequence (internal_sequence_alignment_code)');
    $this->addSql('CREATE UNIQUE INDEX uk_internal_sequence__internal_sequence_code ON internal_sequence (internal_sequence_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_biological_material_is_published_in (id BIGSERIAL NOT NULL, internal_biological_material_fk BIGINT NOT NULL, source_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_ea07bfa7821b1d3f ON internal_biological_material_is_published_in (source_fk)');
    $this->addSql('CREATE INDEX idx_ea07bfa754dbbd4d ON internal_biological_material_is_published_in (internal_biological_material_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_sequence_is_assembled_by (id BIGSERIAL NOT NULL, internal_sequence_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_f6971ba8b53cd04c ON internal_sequence_is_assembled_by (person_fk)');
    $this->addSql('CREATE INDEX idx_f6971ba85be90e48 ON internal_sequence_is_assembled_by (internal_sequence_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE internal_sequence_is_published_in (id BIGSERIAL NOT NULL, source_fk BIGINT NOT NULL, internal_sequence_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_ba97b9c4821b1d3f ON internal_sequence_is_published_in (source_fk)');
    $this->addSql('CREATE INDEX idx_ba97b9c45be90e48 ON internal_sequence_is_published_in (internal_sequence_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE motu_number (id BIGSERIAL NOT NULL, external_sequence_fk BIGINT DEFAULT NULL, delimitation_method_voc_fk BIGINT NOT NULL, internal_sequence_fk BIGINT DEFAULT NULL, motu_fk BIGINT NOT NULL, motu_number BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_4e79cb8d40e7e0b3 ON motu_number (delimitation_method_voc_fk)');
    $this->addSql('CREATE INDEX idx_4e79cb8d503b4409 ON motu_number (motu_fk)');
    $this->addSql('CREATE INDEX idx_4e79cb8dcdd1f756 ON motu_number (external_sequence_fk)');
    $this->addSql('CREATE INDEX idx_4e79cb8d5be90e48 ON motu_number (internal_sequence_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE motu (id BIGSERIAL NOT NULL, csv_file_name VARCHAR(1024) NOT NULL, motu_date DATE NOT NULL, motu_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, motu_title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE motu_is_generated_by (id BIGSERIAL NOT NULL, motu_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_17a90ea3503b4409 ON motu_is_generated_by (motu_fk)');
    $this->addSql('CREATE INDEX idx_17a90ea3b53cd04c ON motu_is_generated_by (person_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE slide_is_mounted_by (id BIGSERIAL NOT NULL, specimen_slide_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_88295540d9c85992 ON slide_is_mounted_by (specimen_slide_fk)');
    $this->addSql('CREATE INDEX idx_88295540b53cd04c ON slide_is_mounted_by (person_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE program (id BIGSERIAL NOT NULL, program_code VARCHAR(255) NOT NULL, program_name VARCHAR(1024) NOT NULL, coordinator_names TEXT NOT NULL, funding_agency VARCHAR(1024) DEFAULT NULL, starting_year BIGINT DEFAULT NULL, ending_year BIGINT DEFAULT NULL, program_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_program__program_code ON program (program_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE sampling (id BIGSERIAL NOT NULL, date_precision_voc_fk BIGINT NOT NULL, donation_voc_fk BIGINT NOT NULL, site_fk BIGINT NOT NULL, sample_code VARCHAR(255) NOT NULL, sampling_date DATE DEFAULT NULL, sampling_duration BIGINT DEFAULT NULL, temperature DOUBLE PRECISION DEFAULT NULL, specific_conductance DOUBLE PRECISION DEFAULT NULL, sample_status SMALLINT NOT NULL, sampling_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_55ae4a3da30c442f ON sampling (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_55ae4a3d50bb334e ON sampling (donation_voc_fk)');
    $this->addSql('CREATE INDEX idx_55ae4a3d369ab36b ON sampling (site_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_sampling__sample_code ON sampling (sample_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE pcr (id BIGSERIAL NOT NULL, gene_voc_fk BIGINT NOT NULL, pcr_quality_voc_fk BIGINT NOT NULL, pcr_specificity_voc_fk BIGINT NOT NULL, forward_primer_voc_fk BIGINT NOT NULL, reverse_primer_voc_fk BIGINT NOT NULL, date_precision_voc_fk BIGINT NOT NULL, dna_fk BIGINT NOT NULL, pcr_code VARCHAR(255) NOT NULL, pcr_number VARCHAR(255) NOT NULL, pcr_date DATE DEFAULT NULL, pcr_details TEXT DEFAULT NULL, pcr_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_5b6b99366ccc2566 ON pcr (pcr_specificity_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b9936a30c442f ON pcr (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b99362c5b04a7 ON pcr (forward_primer_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b9936f1694267 ON pcr (reverse_primer_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b99368b4a1710 ON pcr (pcr_quality_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b99369d3cdb05 ON pcr (gene_voc_fk)');
    $this->addSql('CREATE INDEX idx_5b6b99364b06319d ON pcr (dna_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_pcr__pcr_code ON pcr (pcr_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE sampling_is_performed_by (id BIGSERIAL NOT NULL, person_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_ee2a88c9b53cd04c ON sampling_is_performed_by (person_fk)');
    $this->addSql('CREATE INDEX idx_ee2a88c9662d9b98 ON sampling_is_performed_by (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE person (id BIGSERIAL NOT NULL, institution_fk BIGINT DEFAULT NULL, person_name VARCHAR(255) NOT NULL, person_full_name VARCHAR(1024) DEFAULT NULL, person_name_bis VARCHAR(255) DEFAULT NULL, person_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_fcec9efe8441376 ON person (institution_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_person__person_name ON person (person_name)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE pcr_is_done_by (id BIGSERIAL NOT NULL, pcr_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_1041853b2b63d494 ON pcr_is_done_by (pcr_fk)');
    $this->addSql('CREATE INDEX idx_1041853bb53cd04c ON pcr_is_done_by (person_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE municipality (id BIGSERIAL NOT NULL, country_fk BIGINT NOT NULL, municipality_code VARCHAR(255) NOT NULL, municipality_name VARCHAR(1024) NOT NULL, region_name VARCHAR(1024) NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_municipality__municipality_code ON municipality (municipality_code)');
    $this->addSql('CREATE INDEX idx_e2e2d1eeb1c3431a ON municipality (country_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE sampling_is_done_with_method (id BIGSERIAL NOT NULL, sampling_method_voc_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_5a6bd88a29b38195 ON sampling_is_done_with_method (sampling_method_voc_fk)');
    $this->addSql('CREATE INDEX idx_5a6bd88a662d9b98 ON sampling_is_done_with_method (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE sample_is_fixed_with (id BIGSERIAL NOT NULL, fixative_voc_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_60129a31662d9b98 ON sample_is_fixed_with (sampling_fk)');
    $this->addSql('CREATE INDEX idx_60129a315fd841ac ON sample_is_fixed_with (fixative_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE site (id BIGSERIAL NOT NULL, municipality_fk BIGINT NOT NULL, country_fk BIGINT NOT NULL, access_point_voc_fk BIGINT NOT NULL, habitat_type_voc_fk BIGINT NOT NULL, coordinate_precision_voc_fk BIGINT NOT NULL, site_code VARCHAR(255) NOT NULL, site_name VARCHAR(1024) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, elevation BIGINT DEFAULT NULL, location_info TEXT DEFAULT NULL, site_description TEXT DEFAULT NULL, site_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_9f39f8b1c23046ae ON site (habitat_type_voc_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_site__site_code ON site (site_code)');
    $this->addSql('CREATE INDEX idx_9f39f8b14d50d031 ON site (access_point_voc_fk)');
    $this->addSql('CREATE INDEX idx_9f39f8b143d4e2c ON site (municipality_fk)');
    $this->addSql('CREATE INDEX idx_9f39f8b1e86dbd90 ON site (coordinate_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_9f39f8b1b1c3431a ON site (country_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE source_is_entered_by (id BIGSERIAL NOT NULL, source_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_16dc6005b53cd04c ON source_is_entered_by (person_fk)');
    $this->addSql('CREATE INDEX idx_16dc6005821b1d3f ON source_is_entered_by (source_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE source (id BIGSERIAL NOT NULL, source_code VARCHAR(255) NOT NULL, source_year BIGINT DEFAULT NULL, source_title VARCHAR(2048) NOT NULL, source_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_source__source_code ON source (source_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE species_is_identified_by (id BIGSERIAL NOT NULL, identified_species_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_f8fccf63b53cd04c ON species_is_identified_by (person_fk)');
    $this->addSql('CREATE INDEX idx_f8fccf63b4ab6ba0 ON species_is_identified_by (identified_species_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE specimen (id BIGSERIAL NOT NULL, specimen_type_voc_fk BIGINT NOT NULL, internal_biological_material_fk BIGINT NOT NULL, specimen_molecular_code VARCHAR(255) DEFAULT NULL, specimen_morphological_code VARCHAR(255) NOT NULL, tube_code VARCHAR(255) NOT NULL, specimen_molecular_number VARCHAR(255) DEFAULT NULL, specimen_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_5ee42fce54dbbd4d ON specimen (internal_biological_material_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_specimen__specimen_molecular_code ON specimen (specimen_molecular_code)');
    $this->addSql('CREATE UNIQUE INDEX uk_specimen__specimen_morphological_code ON specimen (specimen_morphological_code)');
    $this->addSql('CREATE INDEX idx_5ee42fce4236d33e ON specimen (specimen_type_voc_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE taxon (id BIGSERIAL NOT NULL, taxon_name VARCHAR(255) NOT NULL, taxon_rank VARCHAR(255) NOT NULL, subclass VARCHAR(255) DEFAULT NULL, taxon_order VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, genus VARCHAR(255) DEFAULT NULL, species VARCHAR(255) DEFAULT NULL, subspecies VARCHAR(255) DEFAULT NULL, taxon_validity SMALLINT NOT NULL, taxon_code VARCHAR(255) NOT NULL, taxon_comments TEXT DEFAULT NULL, clade VARCHAR(255) DEFAULT NULL, taxon_synonym VARCHAR(255) DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, taxon_full_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_taxon__taxon_name ON taxon (taxon_name)');
    $this->addSql('CREATE UNIQUE INDEX uk_taxon__taxon_code ON taxon (taxon_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE storage_box (id BIGSERIAL NOT NULL, collection_type_voc_fk BIGINT NOT NULL, collection_code_voc_fk BIGINT NOT NULL, box_type_voc_fk BIGINT NOT NULL, box_code VARCHAR(255) NOT NULL, box_title VARCHAR(1024) NOT NULL, box_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_7718edef41a72d48 ON storage_box (collection_code_voc_fk)');
    $this->addSql('CREATE INDEX idx_7718edef9e7b0e1f ON storage_box (collection_type_voc_fk)');
    $this->addSql('CREATE INDEX idx_7718edef57552d30 ON storage_box (box_type_voc_fk)');
    $this->addSql('CREATE UNIQUE INDEX uk_storage_box__box_code ON storage_box (box_code)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE user_db (id BIGSERIAL NOT NULL, user_name VARCHAR(255) NOT NULL, user_password VARCHAR(255) NOT NULL, user_email VARCHAR(255) DEFAULT NULL, user_role VARCHAR(255) NOT NULL, salt VARCHAR(255) DEFAULT NULL, user_full_name VARCHAR(255) NOT NULL, user_institution VARCHAR(255) DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, user_is_active SMALLINT NOT NULL, user_comments TEXT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_user_db__username ON user_db (user_name)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE sampling_is_funded_by (id BIGSERIAL NOT NULL, program_fk BIGINT NOT NULL, sampling_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_18fcbb8f759c7bb0 ON sampling_is_funded_by (program_fk)');
    $this->addSql('CREATE INDEX idx_18fcbb8f662d9b98 ON sampling_is_funded_by (sampling_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE vocabulary (id BIGSERIAL NOT NULL, code VARCHAR(255) NOT NULL, vocabulary_title VARCHAR(1024) NOT NULL, parent VARCHAR(255) NOT NULL, voc_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_vocabulary__parent__code ON vocabulary (code, parent)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE specimen_slide (id BIGSERIAL NOT NULL, date_precision_voc_fk BIGINT NOT NULL, storage_box_fk BIGINT DEFAULT NULL, specimen_fk BIGINT NOT NULL, collection_slide_code VARCHAR(255) NOT NULL, slide_title VARCHAR(1024) NOT NULL, slide_date DATE DEFAULT NULL, photo_folder_name VARCHAR(1024) DEFAULT NULL, slide_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_specimen_slide__collection_slide_code ON specimen_slide (collection_slide_code)');
    $this->addSql('CREATE INDEX idx_8da827e22b644673 ON specimen_slide (storage_box_fk)');
    $this->addSql('CREATE INDEX idx_8da827e2a30c442f ON specimen_slide (date_precision_voc_fk)');
    $this->addSql('CREATE INDEX idx_8da827e25f2c6176 ON specimen_slide (specimen_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE dna_is_extracted_by (id BIGSERIAL NOT NULL, dna_fk BIGINT NOT NULL, person_fk BIGINT NOT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE INDEX idx_b786c521b53cd04c ON dna_is_extracted_by (person_fk)');
    $this->addSql('CREATE INDEX idx_b786c5214b06319d ON dna_is_extracted_by (dna_fk)');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('CREATE TABLE institution (id BIGSERIAL NOT NULL, institution_name VARCHAR(1024) NOT NULL, institution_comments TEXT DEFAULT NULL, date_of_creation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_update TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, creation_user_name BIGINT DEFAULT NULL, update_user_name BIGINT DEFAULT NULL, PRIMARY KEY(id))');
    $this->addSql('CREATE UNIQUE INDEX uk_institution__institution_name ON institution (institution_name)');
  }

  public function down(Schema $schema): void {
    // this down() migration is auto-generated, please modify it to your needs
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE country');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE has_targeted_taxa');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_biological_material');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_sequence_is_entered_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_biological_material_is_processed_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE composition_of_internal_biological_material');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_sequence');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE dna');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE chromatogram');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE chromatogram_is_processed_to');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_biological_material_is_published_in');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE external_sequence_is_published_in');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE identified_species');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_biological_material_is_treated_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_biological_material');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_sequence');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_biological_material_is_published_in');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_sequence_is_assembled_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE internal_sequence_is_published_in');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE motu_number');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE motu');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE motu_is_generated_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE slide_is_mounted_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE program');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE sampling');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE pcr');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE sampling_is_performed_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE person');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE pcr_is_done_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE municipality');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE sampling_is_done_with_method');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE sample_is_fixed_with');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE site');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE source_is_entered_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE source');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE species_is_identified_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE specimen');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE taxon');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE storage_box');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE user_db');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE sampling_is_funded_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE vocabulary');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE specimen_slide');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE dna_is_extracted_by');
    $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

    $this->addSql('DROP TABLE institution');
  }
}
