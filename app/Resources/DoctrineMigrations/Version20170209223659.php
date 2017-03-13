<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Allow Session attributes to be set to null
 */
class Version20170209223659 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session CHANGE attire_required attire_required TINYINT(1) DEFAULT NULL, CHANGE equipment_required equipment_required TINYINT(1) DEFAULT NULL, CHANGE supplemental supplemental TINYINT(1) DEFAULT NULL, CHANGE attendance_required attendance_required TINYINT(1) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE session CHANGE attire_required attire_required TINYINT(1) NOT NULL, CHANGE equipment_required equipment_required TINYINT(1) NOT NULL, CHANGE supplemental supplemental TINYINT(1) NOT NULL, CHANGE attendance_required attendance_required TINYINT(1) NOT NULL');
    }
}
