<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds url and needs_accommodation columns to learner group table.
 */
final class Version20200902230650 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Adds url and needs_accommodation columns to learner group table.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` ADD url VARCHAR(2000) DEFAULT NULL, ADD needs_accommodation TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` DROP url, DROP needs_accommodation');
    }
}
