<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds a <code>deleted</code> column to the <code>mesh_descriptor</code> table.
 */
final class Version20170905234026 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_descriptor ADD deleted TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_descriptor DROP deleted');
    }
}
