<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Get rid of publish event and add published flag instead for courses, session, program, and program years.
 * Remove publish_event from offering
 *
 * NOTICE!  This migration cannot be reversed!  It would do bad things if we let you.
 */
class Version20160111222451 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE course ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE session ADD published TINYINT(1) NOT NULL');

        $this->addSql('UPDATE course SET published=1 WHERE publish_event_id IS NOT NULL');
        $this->addSql('UPDATE program_year SET published=1 WHERE publish_event_id IS NOT NULL');
        $this->addSql('UPDATE program SET published=1 WHERE publish_event_id IS NOT NULL');
        $this->addSql('UPDATE session SET published=1 WHERE publish_event_id IS NOT NULL');

        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB956C92BE0');
        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB156C92BE0');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED778456C92BE0');
        $this->addSql('ALTER TABLE program_year DROP FOREIGN KEY FK_B664263056C92BE0');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D456C92BE0');
        $this->addSql('DROP TABLE publish_event');
        $this->addSql('DROP INDEX IDX_A5682AB156C92BE0 ON offering');
        $this->addSql('ALTER TABLE offering DROP publish_event_id');

        $this->addSql('DROP INDEX IDX_169E6FB956C92BE0 ON course');
        $this->addSql('ALTER TABLE course DROP publish_event_id');
        $this->addSql('DROP INDEX IDX_B664263056C92BE0 ON program_year');
        $this->addSql('ALTER TABLE program_year DROP publish_event_id');
        $this->addSql('DROP INDEX IDX_92ED778456C92BE0 ON program');
        $this->addSql('ALTER TABLE program DROP publish_event_id');
        $this->addSql('DROP INDEX IDX_D044D5D456C92BE0 ON session');
        $this->addSql('ALTER TABLE session DROP publish_event_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException('This migration cannot be reversed, it would unpublish everything.');
    }
}
