<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drops "umls_uid" column from the "mesh_descriptor" table.
 */
final class Version20170917183718 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_concept DROP umls_uid');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_concept ADD umls_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
    }
}
