<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Instead of auto creating the messenger table do it in a migration
 * This is the new pattern in symfony 5.1
 */
final class Version20200612202533 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Creates the messenger storage table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE messenger_messages');
    }
}
