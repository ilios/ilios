<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add active to Terms, Competencies, and Vocabularies
 */
class Version20160715212648 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE term ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE term set active=true');

        $this->addSql('ALTER TABLE competency ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE competency set active=true');

        $this->addSql('ALTER TABLE vocabulary ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE vocabulary set active=true');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE term DROP active');
        $this->addSql('ALTER TABLE competency DROP active');
        $this->addSql('ALTER TABLE vocabulary DROP active');
    }
}
