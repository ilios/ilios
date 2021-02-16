<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Cascade learner group deletes to subgroups
 */
final class Version20160919224121 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561997596');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561997596 FOREIGN KEY (parent_group_id) REFERENCES `group` (group_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C561997596');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561997596 FOREIGN KEY (parent_group_id) REFERENCES `group` (group_id)');
    }
}
