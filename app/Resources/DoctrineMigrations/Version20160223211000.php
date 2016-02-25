<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fix mimetype on link and citation learning materials
 */
class Version20160223211000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE learning_material set mime_type = 'link' WHERE web_link IS NOT NULL");
        $this->addSql("UPDATE learning_material set mime_type = 'citation' WHERE citation IS NOT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE learning_material set mime_type = NULL WHERE web_link IS NOT NULL");
        $this->addSql("UPDATE learning_material set mime_type = NULL WHERE citation IS NOT NULL");
    }
}
