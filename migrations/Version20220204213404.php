<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Changes curriculum inventory sequence block table to track starting- and ending-academic-level instead
 * of just academic-level.
 */
final class Version20220204213404 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Changes CI sequence block table to track starting/ending-academic-level instead just academic-level.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6806081C3B0');
        $this->addSql('DROP INDEX IDX_22E6B6806081C3B0 ON curriculum_inventory_sequence_block');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD ending_academic_level_id INT NOT NULL, CHANGE academic_level_id starting_academic_level_id INT NOT NULL');
        $this->addSql('UPDATE curriculum_inventory_sequence_block SET ending_academic_level_id = starting_academic_level_id');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B680145CDCE1 FOREIGN KEY (starting_academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B68062B4C1B6 FOREIGN KEY (ending_academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_22E6B680145CDCE1 ON curriculum_inventory_sequence_block (starting_academic_level_id)');
        $this->addSql('CREATE INDEX IDX_22E6B68062B4C1B6 ON curriculum_inventory_sequence_block (ending_academic_level_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B680145CDCE1');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B68062B4C1B6');
        $this->addSql('DROP INDEX IDX_22E6B680145CDCE1 ON curriculum_inventory_sequence_block');
        $this->addSql('DROP INDEX IDX_22E6B68062B4C1B6 ON curriculum_inventory_sequence_block');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block CHANGE starting_academic_level_id academic_level_id INT NOT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP ending_academic_level_id');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6806081C3B0 FOREIGN KEY (academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_22E6B6806081C3B0 ON curriculum_inventory_sequence_block (academic_level_id)');
    }
}
