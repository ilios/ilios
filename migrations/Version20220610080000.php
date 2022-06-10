<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20220610080000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Drop tracking config from DB as it has been removed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DELETE FROM application_config WHERE name="enable_tracking"');
        $this->addSql('DELETE FROM application_config WHERE name="tracking_code"');
    }

    public function down(Schema $schema): void
    {
    }
}
