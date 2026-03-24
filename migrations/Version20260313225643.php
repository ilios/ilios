<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20260313225643 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Adds accessibility attribute columns to learning materials.';
    }

    public function up(Schema $schema): void
    {
        // add SchoolConfig attributes
        $sql = 'SELECT school_id FROM school';
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();
        if (count($rows)) {
            $insertSql = 'INSERT INTO school_config (school_id, name, value) VALUES ';
            $inserts = [];
            foreach ($rows as $arr) {
                $schoolId = $arr['school_id'];
                $inserts[] = "({$schoolId}, 'learningMaterialAccessibilityRequired', 'false')";
                $inserts[] = "({$schoolId}, 'learningMaterialAccessibilityRequiredMessage', '')";
            }
            $insertSql .= implode(',', $inserts);
            unset($rows);
            unset($inserts);
        }

        if (isset($insertSql)) {
            $this->addSql($insertSql);
        }

        // add LearningMaterial attribute
        $this->addSql('ALTER TABLE learning_material ADD marked_accessible TINYINT DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE learning_material DROP marked_accessible');
    }
}
