<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20240910000000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Increase the size of middle name to match other name fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE middle_name middle_name VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE middle_name middle_name VARCHAR(20) DEFAULT NULL');
    }
}
