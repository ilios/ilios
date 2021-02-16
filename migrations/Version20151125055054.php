<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove incorrect double index created by doctrine
 */
final class Version20151125055054 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP INDEX UNIQ_32B6E2F4CDB3C93B ON mesh_previous_indexing');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32B6E2F4CDB3C93B ON mesh_previous_indexing (mesh_descriptor_uid)');
    }
}
