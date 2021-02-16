<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200114000000 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Change name of password field so it can be used by any algorithm';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE authentication CHANGE password_bcrypt password_hash VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE authentication CHANGE password_hash password_bcrypt VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
    }
}
