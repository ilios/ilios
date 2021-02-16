<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Reset objective active to true in case it was changed accidentally
 */
final class Version20190307000000 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE objective SET active=1');
    }

    public function down(Schema $schema) : void
    {
    }
}
