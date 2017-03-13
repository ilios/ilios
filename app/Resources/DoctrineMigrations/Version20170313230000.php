<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Default initial school configuration sets:
 * showSessionSupplemental: true
 * showSessionSpecialAttireRequired: true
 * showSessionSpecialEquipmentRequired: true
 * showSessionAttendanceRequired: false
 *
 * Which mirrors what the setup looked like before these were configurable options
 */
class Version20170313230000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $sql = 'SELECT school_id FROM school WHERE school_id NOT IN (SELECT school_id from school_config)';
        $rows = $this->connection->executeQuery($sql)->fetchAll();
        if (count($rows)) {
            $insertSql = 'INSERT INTO school_config (school_id, name, value) VALUES ';
            $inserts = [];
            foreach ($rows as $arr) {
                $schoolId = $arr['school_id'];
                $inserts[] = "({$schoolId}, 'showSessionSupplemental', 'true')";
                $inserts[] = "({$schoolId}, 'showSessionSpecialAttireRequired', 'true')";
                $inserts[] = "({$schoolId}, 'showSessionSpecialEquipmentRequired', 'true')";
                $inserts[] = "({$schoolId}, 'showSessionAttendanceRequired', 'false')";
            }
            $insertSql .= implode(',', $inserts);
            unset($rows);
            unset($inserts);
        }

        if (isset($insertSql)) {
            $this->addSql($insertSql);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
