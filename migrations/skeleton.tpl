<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version<version> extends MysqlMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
<up>
    }

    public function down(Schema $schema) : void
    {
<down>
    }
}