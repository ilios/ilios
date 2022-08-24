<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20220714000000 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Add session learning material status table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_session_material_status (id BIGINT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, session_learning_material_id INT NOT NULL, status INT NOT NULL, last_updated_at DATETIME NOT NULL, INDEX IDX_6CC10903A76ED395 (user_id), INDEX IDX_6CC10903E8376E0A (session_learning_material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_session_material_status ADD CONSTRAINT FK_6CC10903A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_session_material_status ADD CONSTRAINT FK_6CC10903E8376E0A FOREIGN KEY (session_learning_material_id) REFERENCES session_learning_material (session_learning_material_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_session_material_status');
    }
}
