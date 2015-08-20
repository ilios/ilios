<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds a auto-incrementing pseudo-id column to the ingestion_exception table.
 *
 * @link  https://github.com/ilios/ilios/issues/963
 */
class Version20150819234126 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ingestion_exception DROP FOREIGN KEY FK_65713AFFA76ED395');
        $this->addSql('ALTER TABLE ingestion_exception DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ingestion_exception ADD ingestion_exception_id INT AUTO_INCREMENT PRIMARY KEY NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65713AFFA76ED395 ON ingestion_exception (user_id)');
        $this->addSql('ALTER TABLE ingestion_exception ADD CONSTRAINT FK_65713AFFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (user_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ingestion_exception MODIFY ingestion_exception_id INT NOT NULL');
        $this->addSql('ALTER TABLE ingestion_exception DROP FOREIGN KEY FK_65713AFFA76ED395');
        $this->addSql('DROP INDEX UNIQ_65713AFFA76ED395 ON ingestion_exception');
        $this->addSql('ALTER TABLE ingestion_exception DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ingestion_exception DROP ingestion_exception_id');
        $this->addSql('ALTER TABLE ingestion_exception ADD PRIMARY KEY (user_id)');
        $this->addSql('ALTER TABLE ingestion_exception ADD CONSTRAINT FK_65713AFFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (user_id) ON DELETE CASCADE');
    }
}
