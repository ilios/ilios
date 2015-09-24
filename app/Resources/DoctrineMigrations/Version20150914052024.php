<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Move all eppn entries to the username on authentication and drop the eppn column
 */
class Version20150914052024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('UPDATE authentication SET username=eppn WHERE eppn IS NOT NULL');
        $this->addSql('DROP INDEX UNIQ_FEB4C9FDFC7885D4 ON authentication');
        $this->addSql('ALTER TABLE authentication DROP eppn');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE authentication ADD eppn VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEB4C9FDFC7885D4 ON authentication (eppn)');
        $this->addSql('UPDATE authentication SET eppn=username');
        $this->addSql('UPDATE authentication SET username=null');
    }
}
