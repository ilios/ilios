<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Back-fills the application config with a "academic year crosses calendar year boundaries" flag.
 */
final class Version20210204234902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Back-fills the application config with a "academic year crosses calendar year boundaries" flag.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO application_config (name, value) VALUES ('academic_year_crosses_calendar_year_boundaries', 'true')");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DELETE FROM application_config WHERE name = 'academic_year_crosses_calendar_year_boundaries'");
    }
}
