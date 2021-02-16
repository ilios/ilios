<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the ci_sessions table.
 *
 * @link https://github.com/ilios/ilios/issues/965
 */
final class Version20150820020125 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE ci_sessions');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE ci_sessions (session_id VARCHAR(40) NOT NULL COLLATE utf8_general_ci, ip_address VARCHAR(45) NOT NULL COLLATE utf8_general_ci, user_agent VARCHAR(120) NOT NULL COLLATE utf8_general_ci, last_activity INT NOT NULL, user_data TEXT NOT NULL COLLATE utf8_general_ci, INDEX last_activity_idx (last_activity), PRIMARY KEY(session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
