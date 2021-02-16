<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drops superfluous index from table.
 */
final class Version20180102185950 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP INDEX UNIQ_FEB4C9FD217BBB47 ON authentication');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEB4C9FD217BBB47 ON authentication (person_id)');
    }
}
