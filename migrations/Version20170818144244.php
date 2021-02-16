<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Activate all session types.
 */
final class Version20170818144244 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE session_type SET active = 1');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('UPDATE session_type SET active = 0');
    }
}
