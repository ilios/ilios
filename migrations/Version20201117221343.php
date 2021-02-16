<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20201117221343 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Changes offering room attribute to be nullable.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offering CHANGE room room VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offering CHANGE room room VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
