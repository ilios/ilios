<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Re-generate foreign keys to allow for cascading deletes of sessions.
 */
class Version20160106190703 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
    }
}
