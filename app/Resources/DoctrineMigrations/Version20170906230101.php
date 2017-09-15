<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase the column size on various MeSH tables.
 */
class Version20170906230101 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // mesh descriptor uid
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36FCDB3C93B');
        $this->addSql('ALTER TABLE course_x_mesh DROP FOREIGN KEY FK_82E35212CDB3C93B');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh DROP FOREIGN KEY FK_EC36AECFCDB3C93B');
        $this->addSql('ALTER TABLE session_x_mesh DROP FOREIGN KEY FK_43B09906CDB3C93B');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D6674CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier DROP FOREIGN KEY FK_FC5A6AD7CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept DROP FOREIGN KEY FK_1AF85275CDB3C93B');
        $this->addSql('ALTER TABLE mesh_previous_indexing DROP FOREIGN KEY FK_32B6E2F4CDB3C93B');
        $this->addSql('ALTER TABLE mesh_tree DROP FOREIGN KEY FK_C63042D9CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE objective_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE course_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_previous_indexing CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_tree CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE session_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36FCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_x_mesh ADD CONSTRAINT FK_82E35212CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh ADD CONSTRAINT FK_EC36AECFCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_x_mesh ADD CONSTRAINT FK_43B09906CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D6674CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier ADD CONSTRAINT FK_FC5A6AD7CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept ADD CONSTRAINT FK_1AF85275CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_previous_indexing ADD CONSTRAINT FK_32B6E2F4CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_tree ADD CONSTRAINT FK_C63042D9CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');

        // mesh qualifier uid
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier DROP FOREIGN KEY FK_FC5A6AD763490620');
        $this->addSql('ALTER TABLE mesh_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier ADD CONSTRAINT FK_FC5A6AD763490620 FOREIGN KEY (mesh_qualifier_uid) REFERENCES mesh_qualifier (mesh_qualifier_uid)');

        // mesh concept casn1
        $this->addSql('ALTER TABLE mesh_concept CHANGE casn_1_name casn_1_name VARCHAR(512) DEFAULT NULL');

        // mesh concept uid
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

        // mesh term uid
        $this->addSql('ALTER TABLE mesh_term CHANGE mesh_term_uid mesh_term_uid VARCHAR(12) NOT NULL');

        // mesh tree tree-number
        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(50) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // mesh tree tree-number
        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(31) NOT NULL COLLATE utf8_unicode_ci');

        // mesh term uid
        $this->addSql('ALTER TABLE mesh_term CHANGE mesh_term_uid mesh_term_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');

        // mesh concept uid
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

        // mesh concept casn1
        $this->addSql('ALTER TABLE mesh_concept CHANGE casn_1_name casn_1_name VARCHAR(127) DEFAULT NULL');

        // mesh qualifier uid
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier DROP FOREIGN KEY FK_FC5A6AD763490620');
        $this->addSql('ALTER TABLE mesh_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(9) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(9) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier ADD CONSTRAINT FK_FC5A6AD763490620 FOREIGN KEY (mesh_qualifier_uid) REFERENCES mesh_qualifier (mesh_qualifier_uid)');

        // mesh descriptor uid
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36FCDB3C93B');
        $this->addSql('ALTER TABLE course_x_mesh DROP FOREIGN KEY FK_82E35212CDB3C93B');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh DROP FOREIGN KEY FK_EC36AECFCDB3C93B');
        $this->addSql('ALTER TABLE session_x_mesh DROP FOREIGN KEY FK_43B09906CDB3C93B');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D6674CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier DROP FOREIGN KEY FK_FC5A6AD7CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept DROP FOREIGN KEY FK_1AF85275CDB3C93B');
        $this->addSql('ALTER TABLE mesh_previous_indexing DROP FOREIGN KEY FK_32B6E2F4CDB3C93B');
        $this->addSql('ALTER TABLE mesh_tree DROP FOREIGN KEY FK_C63042D9CDB3C93B');
        $this->addSql('ALTER TABLE mesh_descriptor CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE course_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_previous_indexing CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_tree CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE objective_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE session_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36FCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_x_mesh ADD CONSTRAINT FK_82E35212CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh ADD CONSTRAINT FK_EC36AECFCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_x_mesh ADD CONSTRAINT FK_43B09906CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D6674CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier ADD CONSTRAINT FK_FC5A6AD7CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept ADD CONSTRAINT FK_1AF85275CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_previous_indexing ADD CONSTRAINT FK_32B6E2F4CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE mesh_tree ADD CONSTRAINT FK_C63042D9CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
    }
}
