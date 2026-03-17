<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260313225643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds accessibility_permission column to learning materials.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE learning_material ADD accessibility_permission TINYINT DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE learning_material DROP accessibility_permission');
    }
}
