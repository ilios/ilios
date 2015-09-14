<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add the pending_user_update table
 */
class Version20150914210711 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pending_user_update (exception_id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(32) NOT NULL, property VARCHAR(32) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_A6D3A181A76ED395 (user_id), PRIMARY KEY(exception_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pending_user_update ADD CONSTRAINT FK_A6D3A181A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('DROP TABLE user_sync_exception');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_sync_exception (exception_id INT AUTO_INCREMENT NOT NULL, process_id INT NOT NULL, process_name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, user_id INT DEFAULT NULL, exception_code INT NOT NULL, mismatched_property_name VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, mismatched_property_value VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX user_id_fkey (user_id), PRIMARY KEY(exception_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE pending_user_update');
    }
}
