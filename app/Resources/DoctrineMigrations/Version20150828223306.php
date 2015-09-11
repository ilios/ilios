<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add a real primary key to mesh_tree
 */
class Version20150828223306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor ADD mesh_tree_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mesh_tree_x_descriptor MODIFY mesh_tree_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP mesh_tree_id');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor ADD PRIMARY KEY (tree_number)');
    }
}
