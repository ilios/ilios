<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Recreates foreign keys to allow for cascading deletes of sessions and associated data points.
 */
class Version20160106235355 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_group DROP FOREIGN KEY FK_4D68848F8EDF74F0');
        $this->addSql('ALTER TABLE offering_x_group ADD CONSTRAINT FK_4D68848F8EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_instructor_group DROP FOREIGN KEY FK_5540AEE18EDF74F0');
        $this->addSql('ALTER TABLE offering_x_instructor_group ADD CONSTRAINT FK_5540AEE18EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_learner DROP FOREIGN KEY FK_991D7DA38EDF74F0');
        $this->addSql('ALTER TABLE offering_x_learner ADD CONSTRAINT FK_991D7DA38EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_instructor DROP FOREIGN KEY FK_171DC5498EDF74F0');
        $this->addSql('ALTER TABLE offering_x_instructor ADD CONSTRAINT FK_171DC5498EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE offering_x_group DROP FOREIGN KEY FK_4D68848F8EDF74F0');
        $this->addSql('ALTER TABLE offering_x_group ADD CONSTRAINT FK_4D68848F8EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id)');
        $this->addSql('ALTER TABLE offering_x_instructor DROP FOREIGN KEY FK_171DC5498EDF74F0');
        $this->addSql('ALTER TABLE offering_x_instructor ADD CONSTRAINT FK_171DC5498EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id)');
        $this->addSql('ALTER TABLE offering_x_instructor_group DROP FOREIGN KEY FK_5540AEE18EDF74F0');
        $this->addSql('ALTER TABLE offering_x_instructor_group ADD CONSTRAINT FK_5540AEE18EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id)');
        $this->addSql('ALTER TABLE offering_x_learner DROP FOREIGN KEY FK_991D7DA38EDF74F0');
        $this->addSql('ALTER TABLE offering_x_learner ADD CONSTRAINT FK_991D7DA38EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id)');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
    }
}
