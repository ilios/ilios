<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20260413213609 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Drops the obsolete archived column from the program_year table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_year DROP archived');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE program_year ADD archived TINYINT NOT NULL');
    }
}
