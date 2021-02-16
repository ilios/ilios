<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Increases learning material title column size.
 */
final class Version20191030201837 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Increases learning material title column size.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE learning_material CHANGE title title VARCHAR(120) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE learning_material CHANGE title title VARCHAR(60) NOT NULL');
    }
}
