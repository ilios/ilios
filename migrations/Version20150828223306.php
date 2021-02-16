<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add a real primary key to mesh_tree
 */
final class Version20150828223306 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor ADD mesh_tree_id INT PRIMARY KEY AUTO_INCREMENT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor MODIFY mesh_tree_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor DROP mesh_tree_id');
        $this->addSql('ALTER TABLE mesh_tree_x_descriptor ADD PRIMARY KEY (tree_number)');
    }
}
