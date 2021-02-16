<?php

declare(strict_types=1);

namespace App\Classes;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

abstract class MysqlMigration extends AbstractMigration
{
    public function preUp(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            "Migration can only be executed safely on mysql."
        );
    }

    public function preDown(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            "Migration can only be executed safely on mysql."
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
