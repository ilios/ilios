<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20260313225643 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Adds accessibility attribute column to learning materials.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE learning_material ADD marked_accessible TINYINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE learning_material DROP marked_accessible');
    }
}
