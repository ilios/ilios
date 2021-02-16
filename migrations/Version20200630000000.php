<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20200630000000 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Add URL to offering';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE offering ADD url VARCHAR(2000) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE offering DROP url');
    }
}
