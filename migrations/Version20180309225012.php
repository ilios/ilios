<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Activate all competencies, vocabularies and vocabulary terms.
 */
final class Version20180309225012 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE vocabulary set active=true');
        $this->addSql('UPDATE competency set active=true');
        $this->addSql('UPDATE term set active=true');
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
