<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Activate all competencies, vocabularies and vocabulary terms.
 */
class Version20180309225012 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('UPDATE vocabulary set active=true');
        $this->addSql('UPDATE competency set active=true');
        $this->addSql('UPDATE term set active=true');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->throwIrreversibleMigrationException();
    }
}
