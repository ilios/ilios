<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds JOIN table for excluding sessions in CI reporting.
 */
final class Version20180514214501 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE curriculum_inventory_sequence_block_x_excluded_session (sequence_block_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_67E306F861D1D223 (sequence_block_id), INDEX IDX_67E306F8613FECDF (session_id), PRIMARY KEY(sequence_block_id, session_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_x_excluded_session ADD CONSTRAINT FK_67E306F861D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_x_excluded_session ADD CONSTRAINT FK_67E306F8613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE curriculum_inventory_sequence_block_x_excluded_session');
    }
}
