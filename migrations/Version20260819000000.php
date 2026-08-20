<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add User Preferences Table
 */
final class Version20260819000000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Add User Preferences Table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_preference (json JSON NOT NULL, user_id INT NOT NULL, PRIMARY KEY (user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_preference ADD CONSTRAINT FK_FA0E76BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_preference DROP FOREIGN KEY FK_FA0E76BFA76ED395');
        $this->addSql('DROP TABLE user_preference');
    }
}
