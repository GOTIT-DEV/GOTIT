<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211002131752 extends AbstractMigration {
  public function getDescription(): string {
    return 'Remove duplicates in intermediary tables';
  }

  private function deduplicate($table, $field1, $field2) {
    $this->addSql("DELETE FROM {$table}
		WHERE id NOT IN
		(
				SELECT MAX(id)
				FROM {$table} e
				GROUP BY (e.{$field1}, e.{$field2})
		);");
  }

  public function up(Schema $schema): void {
    $this->deduplicate('external_biological_material_is_processed_by', 'external_biological_material_fk', 'person_fk');
    $this->deduplicate('chromatogram_is_processed_to', 'internal_sequence_fk', 'chromatogram_fk');
    $this->deduplicate('sampling_is_performed_by', 'sampling_fk', 'person_fk');
  }

  public function down(Schema $schema): void {
    // There is NO reason whatsoever to keep any duplicates in these tables
  }
}
