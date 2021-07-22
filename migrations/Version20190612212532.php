<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds active column to AAMC method table.
 */
final class Version20190612212532 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $recordCount = (int) $this->connection->fetchOne("SELECT COUNT(*) FROM aamc_method");

        $this->addSql('ALTER TABLE aamc_method ADD active TINYINT(1) NOT NULL');

        // flag all methods as active
        $this->addSql("UPDATE aamc_method SET active = true");

        // KLUDGE!
        // only run this if the table is already populated with data.
        // otherwise, this will cause duplicate key issues down the road when attempting to
        // import default data, which is expected to happen as the next step during the
        // installation process.
        // [ST 2021/07/22]
        if ($recordCount) {
            // adds new method
            $this->addSql("INSERT IGNORE INTO aamc_method (method_id, description, active) VALUES ('AM019', 'Exam – Institutionally Developed, Laboratory Practical (Lab)', true)");
        }
        // flags "Practical (Lab)" method as inactive.
        $this->addSql("UPDATE aamc_method SET active = FALSE where method_id = 'AM015'");
        // re-map session types to the new method
        $this->addSql("UPDATE session_type_x_aamc_method SET method_id = 'AM019' WHERE method_id = 'AM015'");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE aamc_method DROP active');
        // re-map session types to the old method
        $this->addSql("UPDATE session_type_x_aamc_method SET method_id = 'AM015' WHERE method_id = 'AM019'");
        // remove new method
        $this->addSql("DELETE FROM aamc_method WHERE method_id = 'AM019'");
    }
}
