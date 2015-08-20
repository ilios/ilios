<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the ci_sessions table.
 *
 * @link https://github.com/ilios/ilios/issues/965
 */
class Version20150820020125 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ci_sessions');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ci_sessions (session_id VARCHAR(40) NOT NULL COLLATE utf8_general_ci, ip_address VARCHAR(45) NOT NULL COLLATE utf8_general_ci, user_agent VARCHAR(120) NOT NULL COLLATE utf8_general_ci, last_activity INT NOT NULL, user_data TEXT NOT NULL COLLATE utf8_general_ci, INDEX last_activity_idx (last_activity), PRIMARY KEY(session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}
