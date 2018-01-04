<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the obsolete instruction-hours and recurring-event tables from the schema.
 */
class Version20150813184053 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE instruction_hours');
        $this->addSql('DROP TABLE offering_x_recurring_event');
        $this->addSql('DROP TABLE recurring_event');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE recurring_event (recurring_event_id INT AUTO_INCREMENT NOT NULL, next_recurring_event_id INT DEFAULT NULL, previous_recurring_event_id INT DEFAULT NULL, on_sunday TINYINT(1) NOT NULL, on_monday TINYINT(1) NOT NULL, on_tuesday TINYINT(1) NOT NULL, on_wednesday TINYINT(1) NOT NULL, on_thursday TINYINT(1) NOT NULL, on_friday TINYINT(1) NOT NULL, on_saturday TINYINT(1) NOT NULL, end_date DATETIME NOT NULL, repetition_count SMALLINT DEFAULT NULL, INDEX IDX_51B1C7F8B494B099 (previous_recurring_event_id), INDEX IDX_51B1C7F882312E9D (next_recurring_event_id), PRIMARY KEY(recurring_event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offering_x_recurring_event (offering_id INT NOT NULL, recurring_event_id INT NOT NULL, INDEX IDX_D6FB967CE54B259A (recurring_event_id), INDEX IDX_D6FB967C8EDF74F0 (offering_id), PRIMARY KEY(offering_id, recurring_event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instruction_hours (instruction_hours_id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, user_id INT NOT NULL, generation_time_stamp DATETIME NOT NULL, hours_accrued INT NOT NULL, modified TINYINT(1) NOT NULL, modification_time_stamp DATETIME NOT NULL, INDEX IDX_E52A7DDBA76ED395 (user_id), INDEX IDX_E52A7DDB613FECDF (session_id), PRIMARY KEY(instruction_hours_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE instruction_hours ADD CONSTRAINT FK_E52A7DDB613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE instruction_hours ADD CONSTRAINT FK_E52A7DDBA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE offering_x_recurring_event ADD CONSTRAINT FK_D6FB967CE54B259A FOREIGN KEY (recurring_event_id) REFERENCES recurring_event (recurring_event_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_recurring_event ADD CONSTRAINT FK_D6FB967C8EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recurring_event ADD CONSTRAINT fkey_recurring_event_next_recurring_event_id FOREIGN KEY (next_recurring_event_id) REFERENCES recurring_event (recurring_event_id)');
        $this->addSql('ALTER TABLE recurring_event ADD CONSTRAINT fkey_recurring_event_previous_recurring_event_id FOREIGN KEY (previous_recurring_event_id) REFERENCES recurring_event (recurring_event_id)');
    }
}
