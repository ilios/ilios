<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Expands the size of the first- and last-name columns on the user table.
 */
final class Version20190711180817 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE user CHANGE last_name last_name VARCHAR(50) NOT NULL, CHANGE first_name first_name VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE user CHANGE last_name last_name VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci, CHANGE first_name first_name VARCHAR(20) NOT NULL COLLATE utf8_unicode_ci');
    }
}
