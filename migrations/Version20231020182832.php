<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20231020182832 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Links service tokens to change alerts.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE alert_service_token_instigators (alert_id INT NOT NULL, service_token_id INT NOT NULL, INDEX IDX_57F4E18693035F72 (alert_id), INDEX IDX_57F4E186B8A8BC84 (service_token_id), PRIMARY KEY(alert_id, service_token_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert_service_token_instigators ADD CONSTRAINT FK_57F4E18693035F72 FOREIGN KEY (alert_id) REFERENCES alert (alert_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE alert_service_token_instigators ADD CONSTRAINT FK_57F4E186B8A8BC84 FOREIGN KEY (service_token_id) REFERENCES service_token (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE alert_service_token_instigators DROP FOREIGN KEY FK_57F4E18693035F72');
        $this->addSql('ALTER TABLE alert_service_token_instigators DROP FOREIGN KEY FK_57F4E186B8A8BC84');
        $this->addSql('DROP TABLE alert_service_token_instigators');
    }
}
