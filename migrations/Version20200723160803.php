<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds a description column to the session table and copies
 * all existing text from the corresponding session_description::description
 * column over, if applicable.
 */
final class Version20200723160803 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds description column to session table.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE session ADD description LONGTEXT DEFAULT NULL');

        // copy description from session_description::description to session::description
        $this->addSql('UPDATE session s JOIN session_description sd ON s.session_id = sd.session_id SET s.description = sd.description');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE session DROP description');
    }
}
