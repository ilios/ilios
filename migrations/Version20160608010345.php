<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes unique constraint from curriculum inventory reports table.
 */
final class Version20160608010345 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP INDEX program_id_year ON curriculum_inventory_report');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX program_id_year ON curriculum_inventory_report (program_id, year)');
    }
}
