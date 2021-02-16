<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase the column size on mesh_tree::tree_number.
 */
final class Version20180307174249 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(80) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
    }
}
