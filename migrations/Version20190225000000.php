<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Add active flag to objective table
 */
final class Version20190225000000 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE objective ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE objective SET active=1');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE objective DROP active');
    }
}
