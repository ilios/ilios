<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drops obsolete <code>user_made_reminder</code> table.
 */
class Version20180323231630 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_made_reminder');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE user_made_reminder (user_made_reminder_id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, note VARCHAR(150) NOT NULL COLLATE utf8_unicode_ci, creation_date DATETIME NOT NULL, due_date DATETIME NOT NULL, closed TINYINT(1) NOT NULL, INDEX due_closed_user_k (due_date, closed, user_id), INDEX IDX_44EF4595A76ED395 (user_id), PRIMARY KEY(user_made_reminder_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_made_reminder ADD CONSTRAINT FK_44EF4595A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
    }
}
