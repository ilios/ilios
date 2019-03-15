<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Drops published attributes from programs and program-years.
 */
final class Version20190315193539 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE program DROP published_as_tbd, DROP published');
        $this->addSql('ALTER TABLE program_year DROP published_as_tbd, DROP published');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE program ADD published_as_tbd TINYINT(1) NOT NULL, ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year ADD published_as_tbd TINYINT(1) NOT NULL, ADD published TINYINT(1) NOT NULL');
    }
}
