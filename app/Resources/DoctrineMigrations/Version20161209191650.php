<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Swaps out the <code>mesh_tree_x_descriptor</code> table with the <code>mesh_tree</code> table.
 */
class Version20161209191650 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE mesh_tree (mesh_tree_id INT AUTO_INCREMENT NOT NULL, mesh_descriptor_uid VARCHAR(9) DEFAULT NULL, tree_number VARCHAR(31) NOT NULL, INDEX IDX_C63042D9CDB3C93B (mesh_descriptor_uid), PRIMARY KEY(mesh_tree_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesh_tree ADD CONSTRAINT FK_C63042D9CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('INSERT INTO mesh_tree (mesh_tree_id, mesh_descriptor_uid, tree_number) (SELECT mesh_tree_id, mesh_descriptor_uid, tree_number FROM mesh_tree_x_descriptor)');
        $this->addSql('DROP TABLE mesh_tree_x_descriptor');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE mesh_tree_x_descriptor (mesh_tree_id INT AUTO_INCREMENT NOT NULL, mesh_descriptor_uid VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci, tree_number VARCHAR(31) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_A7706041CDB3C93B (mesh_descriptor_uid), PRIMARY KEY(mesh_tree_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor ADD CONSTRAINT FK_A7706041CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('INSERT INTO mesh_tree_x_descriptor (mesh_tree_id, mesh_descriptor_uid, tree_number) (SELECT mesh_tree_id, mesh_descriptor_uid, tree_number FROM mesh_tree_descriptor)');
        $this->addSql('DROP TABLE mesh_tree');
    }
}
