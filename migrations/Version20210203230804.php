<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds academic year start/end date data points.
 */
final class Version20210203230804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds academic year start/end date data points.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE school ADD academic_year_start_day SMALLINT NOT NULL, ADD academic_year_start_month SMALLINT NOT NULL, ADD academic_year_end_day SMALLINT NOT NULL, ADD academic_year_end_month SMALLINT NOT NULL');
        $this->addSQL('UPDATE school SET academic_year_start_day = 1, academic_year_start_month = 7, academic_year_end_day = 30, academic_year_end_month = 6');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE school DROP academic_year_start_day, DROP academic_year_start_month, DROP academic_year_end_day, DROP academic_year_end_month');
    }
}
