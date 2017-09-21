<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drop MeSH semantic type tables.
 */
class Version20170915185745 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D789316AE7127');
        $this->addSql('DROP TABLE mesh_concept_x_semantic_type');
        $this->addSql('DROP TABLE mesh_semantic_type');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mesh_concept_x_semantic_type (mesh_concept_uid VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, mesh_semantic_type_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_CD3D7893E34D9FF5 (mesh_concept_uid), INDEX IDX_CD3D789316AE7127 (mesh_semantic_type_uid), PRIMARY KEY(mesh_concept_uid, mesh_semantic_type_uid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mesh_semantic_type (mesh_semantic_type_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(192) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(mesh_semantic_type_uid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D789316AE7127 FOREIGN KEY (mesh_semantic_type_uid) REFERENCES mesh_semantic_type (mesh_semantic_type_uid)');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D7893E34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
    }
}
