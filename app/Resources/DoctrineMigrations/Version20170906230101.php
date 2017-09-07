<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase the max width on descriptor uid columns.
 */
class Version20170906230101 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE objective_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE course_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_previous_indexing CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_tree CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE session_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(12) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE course_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE course_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_previous_indexing CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_tree CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE objective_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE session_learning_material_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE session_x_mesh CHANGE mesh_descriptor_uid mesh_descriptor_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
    }
}
