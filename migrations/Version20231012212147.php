<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20231012212147 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Adds service-token table and links it to audit-logs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE service_token (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, expiresAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_log ADD token_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_F6E1C0F541DEE7B9 FOREIGN KEY (token_id) REFERENCES service_token (id)');
        $this->addSql('CREATE INDEX IDX_F6E1C0F541DEE7B9 ON audit_log (token_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE audit_log DROP FOREIGN KEY FK_F6E1C0F541DEE7B9');
        $this->addSql('DROP TABLE service_token');
        $this->addSql('DROP INDEX IDX_F6E1C0F541DEE7B9 ON audit_log');
        $this->addSql('ALTER TABLE audit_log DROP token_id');
    }
}
