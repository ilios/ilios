<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add cascade to set objective competency ID to null when a competency is deleted
 */
class Version20160301211144 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE objective DROP FOREIGN KEY FK_B996F101FB9F58C');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101FB9F58C FOREIGN KEY (competency_id) REFERENCES competency (competency_id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE objective DROP FOREIGN KEY FK_B996F101FB9F58C');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101FB9F58C FOREIGN KEY (competency_id) REFERENCES competency (competency_id)');
    }
}
