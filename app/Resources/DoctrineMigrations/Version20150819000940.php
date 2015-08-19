<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the mesh_user_selection table from the db schema.
 *
 * @link https://github.com/ilios/ilios/issues/935
 */
class Version20150819000940 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE mesh_user_selection');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mesh_user_selection (mesh_user_selection_id INT AUTO_INCREMENT NOT NULL, mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, search_phrase VARCHAR(127) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_650D32BFCDB3C93B (mesh_descriptor_uid), PRIMARY KEY(mesh_user_selection_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesh_user_selection ADD CONSTRAINT FK_650D32BFCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
    }
}
