<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds start/end-date columns to course/session learning materials tables.
 */
final class Version20170913234324 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course_learning_material ADD start_date DATETIME DEFAULT NULL, ADD end_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE session_learning_material ADD start_date DATETIME DEFAULT NULL, ADD end_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course_learning_material DROP start_date, DROP end_date');
        $this->addSql('ALTER TABLE session_learning_material DROP start_date, DROP end_date');
    }
}
