<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add ancestor self referencing relationship to learner groups
 */
class Version20180320041519 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` ADD ancestor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES `group` (group_id)');
        $this->addSql('CREATE INDEX IDX_6DC044C5C671CEA1 ON `group` (ancestor_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5C671CEA1');
        $this->addSql('DROP INDEX IDX_6DC044C5C671CEA1 ON `group`');
        $this->addSql('ALTER TABLE `group` DROP ancestor_id');
    }
}
