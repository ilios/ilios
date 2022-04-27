<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20220427070439 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Add pronouns field to user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD pronouns VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP pronouns');
    }
}
