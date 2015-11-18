<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Cascade deletes for cohorts and program years
 */
class Version20151118233516 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cohort DROP FOREIGN KEY FK_D3B8C16BCB2B0673');
        $this->addSql('ALTER TABLE cohort ADD CONSTRAINT FK_D3B8C16BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cohort DROP FOREIGN KEY FK_D3B8C16BCB2B0673');
        $this->addSql('ALTER TABLE cohort ADD CONSTRAINT FK_D3B8C16BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id)');
    }
}
