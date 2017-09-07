<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase the max width on concept uid columns.
 */
class Version20170907010557 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP FOREIGN KEY FK_100AC50FE34D9FF5');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D7893E34D9FF5');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept DROP FOREIGN KEY FK_1AF85275E34D9FF5');

        $this->addSql('ALTER TABLE mesh_concept MODIFY mesh_concept_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_concept_x_term CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE mesh_concept_x_term ADD CONSTRAINT FK_100AC50FE34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D7893E34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept ADD CONSTRAINT FK_1AF85275E34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP FOREIGN KEY FK_100AC50FE34D9FF5');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D7893E34D9FF5');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept DROP FOREIGN KEY FK_1AF85275E34D9FF5');

        $this->addSql('ALTER TABLE mesh_concept MODIFY mesh_concept_uid VARCHAR(9) NOT NULL');
        $this->addSql('ALTER TABLE mesh_concept_x_term CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE mesh_concept_x_term ADD CONSTRAINT FK_100AC50FE34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D7893E34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept ADD CONSTRAINT FK_1AF85275E34D9FF5 FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)');
    }
}
