<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increase max width of <code>tree_number::tree_number</code> column.
 */
class Version20170908184626 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(50) NOT NULL');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_tree CHANGE tree_number tree_number VARCHAR(31) NOT NULL COLLATE utf8_unicode_ci');
    }
}
