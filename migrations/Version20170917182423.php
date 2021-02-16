<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drops the "print" column from the "mesh_term" table.
 */
final class Version20170917182423 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_term DROP print');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_term ADD print TINYINT(1) DEFAULT NULL');
    }
}
