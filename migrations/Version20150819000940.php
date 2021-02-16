<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the mesh_user_selection table from the db schema.
 *
 * @link https://github.com/ilios/ilios/issues/935
 */
final class Version20150819000940 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE mesh_user_selection');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE mesh_user_selection (mesh_user_selection_id INT AUTO_INCREMENT NOT NULL, mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, search_phrase VARCHAR(127) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_650D32BFCDB3C93B (mesh_descriptor_uid), PRIMARY KEY(mesh_user_selection_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesh_user_selection ADD CONSTRAINT FK_650D32BFCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
    }
}
