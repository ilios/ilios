<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Re-generate foreign keys to allow for cascading deletes of sessions.
 */
final class Version20160106190703 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE offering DROP FOREIGN KEY FK_A5682AB1613FECDF');
        $this->addSql('ALTER TABLE offering ADD CONSTRAINT FK_A5682AB1613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE session_description DROP FOREIGN KEY FK_91BD5E51613FECDF');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8D613FECDF');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8D613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
    }
}
