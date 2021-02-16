<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove the deprecated instructors column on the `group` table
 */
final class Version20151004075931 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` DROP instructors');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` ADD instructors VARCHAR(120) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
