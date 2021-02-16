<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200330062405 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Re-active all objectives';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE objective SET active=1');
    }

    public function down(Schema $schema) : void
    {
    }
}
