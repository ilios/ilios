<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds url and needs_accommodation columns to learner group table.
 */
final class Version20200902230650 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds url and needs_accommodation columns to learner group table.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `group` ADD url VARCHAR(2000) DEFAULT NULL, ADD needs_accommodation TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE `group` DROP url, DROP needs_accommodation');
    }
}
