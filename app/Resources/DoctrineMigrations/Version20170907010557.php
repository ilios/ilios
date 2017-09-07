<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170907010557 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE mesh_concept MODIFY mesh_concept_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_concept_x_term CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(12) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE mesh_concept MODIFY mesh_concept_uid VARCHAR(9) NOT NULL');
        $this->addSql('ALTER TABLE mesh_concept_x_term CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_descriptor_x_concept CHANGE mesh_concept_uid mesh_concept_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
    }
}
