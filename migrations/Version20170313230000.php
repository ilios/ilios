<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
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
final class Version20170313230000 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $sql = 'SELECT school_id FROM school WHERE school_id NOT IN (SELECT school_id from school_config)';
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();
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
    public function down(Schema $schema) : void
    {
    }
}
