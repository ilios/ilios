<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Recreate foreign keys to allow for cascading deletes from curriculum inventory reports.
 */
class Version20160607171112 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6804BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6806081C3B0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B680DEB52F47');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6804BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6806081C3B0 FOREIGN KEY (academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B680DEB52F47 FOREIGN KEY (parent_sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session DROP FOREIGN KEY FK_CF8E4F1261D1D223');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F1261D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence DROP FOREIGN KEY FK_B8AE58F54BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence ADD CONSTRAINT FK_B8AE58F54BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_academic_level DROP FOREIGN KEY FK_B4D3296D4BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_academic_level ADD CONSTRAINT FK_B4D3296D4BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE curriculum_inventory_academic_level DROP FOREIGN KEY FK_B4D3296D4BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_academic_level ADD CONSTRAINT FK_B4D3296D4BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence DROP FOREIGN KEY FK_B8AE58F54BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence ADD CONSTRAINT FK_B8AE58F54BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6806081C3B0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B680DEB52F47');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6804BD2A4C0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6806081C3B0 FOREIGN KEY (academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B680DEB52F47 FOREIGN KEY (parent_sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6804BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session DROP FOREIGN KEY FK_CF8E4F1261D1D223');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F1261D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id)');
    }
}
