<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Update elastic search config names to vendor neutral search
 */
final class Version20230314220624 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Update elastic search config names to vendor neutral search.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE application_config SET name='search_upload_limit' WHERE name='elasticsearch_upload_limit'");
        $this->addSql("UPDATE application_config SET name='search_hosts' WHERE name='elasticsearch_hosts'");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("UPDATE application_config SET name='elasticsearch_upload_limit' WHERE name='search_upload_limit'");
        $this->addSql("UPDATE application_config SET name='elasticsearch_hosts' WHERE name='search_hosts'");
    }
}
