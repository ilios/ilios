<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150819200000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(16) NOT NULL, createdAt DATETIME NOT NULL, objectId INT NOT NULL, valuesChanged TEXT NOT NULL, objectClass VARCHAR(255) NOT NULL, INDEX IDX_F6E1C0F5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('DROP TABLE IF EXISTS audit_atom');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE audit_atom (audit_atom_id INT AUTO_INCREMENT NOT NULL, table_row_id INT NOT NULL, table_column VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, table_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, event_type TINYINT(1) NOT NULL, created_by INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX fkey_audit_atom_created_by (created_by), INDEX idx_audit_atom_created_at (created_at), PRIMARY KEY(audit_atom_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE audit_log');
    }
}
