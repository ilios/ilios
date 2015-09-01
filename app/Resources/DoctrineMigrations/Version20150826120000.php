<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrate MeSH terms into a usable form
 */
class Version20150826120000 extends AbstractMigration
{
    
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D7893E34D9FF5');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D789316AE7127');

        $this->addSql('DROP INDEX mesh_concept_uid ON mesh_concept_x_semantic_type');
        $this->addSql('DROP INDEX mesh_semantic_type_uid ON mesh_concept_x_semantic_type');
        $this->addSql(
            'ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D7893E34D9FF5 ' .
            'FOREIGN KEY (mesh_concept_uid) REFERENCES mesh_concept (mesh_concept_uid)'
        );
        $this->addSql(
            'ALTER TABLE mesh_concept_x_semantic_type ADD CONSTRAINT FK_CD3D789316AE7127 ' .
            'FOREIGN KEY (mesh_semantic_type_uid) REFERENCES mesh_semantic_type (mesh_semantic_type_uid)'
        );
        $this->addSql('CREATE INDEX IDX_CD3D7893E34D9FF5 ON mesh_concept_x_semantic_type (mesh_concept_uid)');
        $this->addSql('CREATE INDEX IDX_CD3D789316AE7127 ON mesh_concept_x_semantic_type (mesh_semantic_type_uid)');
        $this->addSql('ALTER TABLE mesh_term DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_term ADD mesh_term_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX mesh_term_uid_name ON mesh_term (mesh_term_uid, name)');
        
        $this->addSql('ALTER TABLE `mesh_tree_x_descriptor` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE `mesh_tree_x_descriptor` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $this->addSql('CREATE INDEX IDX_A7706041CDB3C93B ON mesh_tree_x_descriptor (mesh_descriptor_uid)');
        $this->addSql(
            'ALTER TABLE mesh_tree_x_descriptor ' .
            'CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL'
        );
        $this->addSql(
            'ALTER TABLE mesh_tree_x_descriptor ADD CONSTRAINT FK_A7706041CDB3C93B ' .
            'FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)'
        );
        $this->addSql(
            'ALTER TABLE mesh_tree_x_descriptor CHANGE mesh_descriptor_uid ' .
            'mesh_descriptor_uid VARCHAR(9) DEFAULT NULL'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D7893E34D9FF5');
        $this->addSql('ALTER TABLE mesh_concept_x_semantic_type DROP FOREIGN KEY FK_CD3D789316AE7127');
        $this->addSql('DROP INDEX IDX_CD3D7893E34D9FF5 ON mesh_concept_x_semantic_type');
        $this->addSql('CREATE INDEX mesh_concept_uid ON mesh_concept_x_semantic_type (mesh_concept_uid)');
        $this->addSql('DROP INDEX IDX_CD3D789316AE7127 ON mesh_concept_x_semantic_type');
        $this->addSql('CREATE INDEX mesh_semantic_type_uid ON mesh_concept_x_semantic_type (mesh_semantic_type_uid)');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP FOREIGN KEY FK_A7706041CDB3C93B');
        $this->addSql('DROP INDEX IDX_A7706041CDB3C93B ON mesh_tree_x_descriptor');
        $this->addSql(
            'ALTER TABLE mesh_tree_x_descriptor ' .
            'CHANGE mesh_descriptor_uid mesh_descriptor_uid ' .
            'VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci'
        );
        
        
        $this->addSql('ALTER TABLE mesh_term MODIFY mesh_term_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesh_term DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_term DROP mesh_term_id');
        $this->addSql('ALTER TABLE mesh_term ADD PRIMARY KEY (mesh_term_uid, name)');
        $this->addSql('DROP INDEX mesh_term_uid_name ON mesh_term');
    }
}
