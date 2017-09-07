<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase the max width on qualifier uid columns.
 */
class Version20170906233903 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(12) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_descriptor_x_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE mesh_qualifier CHANGE mesh_qualifier_uid mesh_qualifier_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci');
    }
}
