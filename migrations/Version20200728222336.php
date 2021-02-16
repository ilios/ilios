<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200728222336 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Make learning material description nullable';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE learning_material CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE learning_material SET description=NULL WHERE description=""');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('UPDATE learning_material SET description="" WHERE description=NULL');
        $this->addSql('ALTER TABLE learning_material CHANGE description description LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
