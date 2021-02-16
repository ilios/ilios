<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Increases the size of the offering::room column.
 */
final class Version20160419233601 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE offering CHANGE room room VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE offering CHANGE room room VARCHAR(60) NOT NULL COLLATE utf8_unicode_ci');
    }
}
