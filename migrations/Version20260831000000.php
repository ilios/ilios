<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20260831000000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Flags all schools to enable MeSH UI in the frontend.';
    }

    public function up(Schema $schema): void
    {
        $sql = "SELECT school_id FROM school WHERE school_id NOT IN (SELECT school_id from school_config WHERE name = 'showMeSH')";
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();
        if (count($rows)) {
            $insertSql = 'INSERT INTO school_config (school_id, name, value) VALUES ';
            $inserts = [];
            foreach ($rows as $arr) {
                $schoolId = $arr['school_id'];
                $inserts[] = "({$schoolId}, 'showMeSH', 'true')";
            }
            $insertSql .= implode(',', $inserts);
            unset($rows);
            unset($inserts);
        }

        if (isset($insertSql)) {
            $this->addSql($insertSql);
        }
    }

    public function down(Schema $schema): void
    {
        // do nothing here.
    }
}
