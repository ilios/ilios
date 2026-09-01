<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Update offerings to be at least one minute apart
 */
final class Version20260831000000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Fix offerings with no duration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE offering SET end_date = DATE_ADD(start_date, INTERVAL 1 MINUTE) WHERE TIMESTAMPDIFF(SECOND, start_date, end_date) < 60');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
