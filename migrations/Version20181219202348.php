<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Increases the size of the "name" column on the "mesh_term" table.
 */
final class Version20181219202348 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_term CHANGE name name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_term CHANGE name name VARCHAR(192) NOT NULL COLLATE utf8_unicode_ci');
    }
}
