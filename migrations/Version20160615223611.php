<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds a token column to the curriculum inventory reports table and fills it with generated data.
 */
final class Version20160615223611 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE curriculum_inventory_report ADD token VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX idx_ci_report_token_unique ON curriculum_inventory_report (token)');

        $sql = 'SELECT report_id FROM `curriculum_inventory_report`';
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();
        if (count($rows)) {
            $updates = array_map(
                function ($row) {
                    $random = random_bytes(128);

                    // prepend id to avoid a conflict
                    // and current time to prevent a conflict with regeneration
                    $key = $row['report_id'] . microtime() . $random;

                    // hash the string to give consistent length and URL safe characters
                    $token = hash('sha256', $key);

                    return "WHEN {$row['report_id']} THEN '{$token}'";
                },
                $rows
            );

            //bulk update all the records to avoide a mess in the output
            $sql = 'UPDATE `curriculum_inventory_report` SET `token` = (CASE report_id ';
            $sql .= implode(' ', $updates);
            $sql .= 'END)';

            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX idx_ci_report_token_unique ON curriculum_inventory_report');
        $this->addSql('ALTER TABLE curriculum_inventory_report DROP token');
    }
}
