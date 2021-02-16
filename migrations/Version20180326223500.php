<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Convert audit_log objectId to be a varchar instead of an integer
 */
final class Version20180326223500 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE audit_log CHANGE objectId objectId VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE audit_log CHANGE objectId objectId INT NOT NULL');
    }
}
