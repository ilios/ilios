<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200730215836 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Remove the classic sha256 password field';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE authentication DROP password_sha256');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE authentication ADD password_sha256 VARCHAR(64) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
