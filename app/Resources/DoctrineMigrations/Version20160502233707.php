<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates table for AAMC method types and sets up a JOIN table to vocabulary terms.
 */
class Version20160502233707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE term_x_aamc_resource_type (term_id INT NOT NULL, resource_type_id INT NOT NULL, INDEX IDX_F4C4B9D6E2C35FC (term_id), INDEX IDX_F4C4B9D698EC6B7B (resource_type_id), PRIMARY KEY(term_id, resource_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE aamc_resource_type (resource_type_id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(resource_type_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE term_x_aamc_resource_type ADD CONSTRAINT FK_F4C4B9D6E2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id)');
        $this->addSql('ALTER TABLE term_x_aamc_resource_type ADD CONSTRAINT FK_F4C4B9D698EC6B7B FOREIGN KEY (resource_type_id) REFERENCES aamc_resource_type (resource_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE term_x_aamc_resource_type DROP FOREIGN KEY FK_F4C4B9D698EC6B7B');
        $this->addSql('DROP TABLE term_x_aamc_resource_type');
        $this->addSql('DROP TABLE aamc_resource_type');
    }
}
