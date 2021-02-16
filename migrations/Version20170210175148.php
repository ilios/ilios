<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This adds a 'position' column to the course/session learning materials tables.
 */
final class Version20170210175148 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course_learning_material ADD position INT NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material ADD position INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course_learning_material DROP position');
        $this->addSql('ALTER TABLE session_learning_material DROP position');
    }
}
