<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add administrator relationship to school
 */
class Version20161013205034 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE school_administrator (school_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_74CDAA6FC32A47EE (school_id), INDEX IDX_74CDAA6FA76ED395 (user_id), PRIMARY KEY(school_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school_administrator ADD CONSTRAINT FK_74CDAA6FC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE school_administrator ADD CONSTRAINT FK_74CDAA6FA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE school_administrator');
    }
}
