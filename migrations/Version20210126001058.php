<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Alters foreign key constraint on prerequisite column to set value to NULL on deletion.
 */
final class Version20210126001058 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Alters foreign key constraint on prerequisite column to set value to NULL on deletion.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D42710C13B');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D42710C13B FOREIGN KEY (postrequisite_id) REFERENCES session (session_id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D42710C13B');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D42710C13B FOREIGN KEY (postrequisite_id) REFERENCES session (session_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
