<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds active column to AAMC method table.
 */
final class Version20190612212532 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE aamc_method ADD active TINYINT(1) NOT NULL');

        // flag all methods as active
        $this->addSql("UPDATE aamc_method SET active = true");
        // adds new method
        $this->addSql("INSERT IGNORE INTO aamc_method (method_id, description, active) VALUES ('AM019', 'Exam – Institutionally Developed, Laboratory Practical (Lab)', true)");
        // flags "Practical (Lab)" method as inactive.
        $this->addSql("UPDATE aamc_method SET active = FALSE where method_id = 'AM015'");
        // re-map session types to the new method
        $this->addSql("UPDATE session_type_x_aamc_method SET method_id = 'AM019' WHERE method_id = 'AM015'");
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE aamc_method DROP active');
        // re-map session types to the old method
        $this->addSql("UPDATE session_type_x_aamc_method SET method_id = 'AM015' WHERE method_id = 'AM019'");
        // remove new method
        $this->addSql("DELETE FROM aamc_method WHERE method_id = 'AM019'");
    }
}
