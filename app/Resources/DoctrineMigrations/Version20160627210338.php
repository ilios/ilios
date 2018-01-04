<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Replace AAMC resource type identifiers with the officially provided codes.
 */
class Version20160627210338 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // "virtual reality" has been dropped from the list, remove it.
        $this->addSql('DELETE FROM aamc_resource_type WHERE resource_type_id = 24');
        // fix capitalization and spacing
        $this->addSql("UPDATE aamc_resource_type SET title = 'Patient - Teaching' WHERE resource_type_id = 15");
        $this->addSql("UPDATE aamc_resource_type SET title = 'Patient - Receiving Clinical Care' WHERE resource_type_id = 16");

        // re-map resource type identifiers
        $this->addSql('ALTER TABLE term_x_aamc_resource_type DROP FOREIGN KEY FK_F4C4B9D698EC6B7B');
        $this->addSql('DROP INDEX IDX_F4C4B9D698EC6B7B ON term_x_aamc_resource_type');
        $this->addSql('ALTER TABLE term_x_aamc_resource_type CHANGE resource_type_id resource_type_id VARCHAR(21) NOT NULL');
        $this->addSql('ALTER TABLE aamc_resource_type CHANGE resource_type_id resource_type_id VARCHAR(21) NOT NULL');

        $map = [
            'RE001' => '1',
            'RE002' => '2',
            'RE003' => '3',
            'RE004' => '4',
            'RE005' => '5',
            'RE006' => '6',
            'RE007' => '7',
            'RE008' => '8',
            'RE009' => '9',
            'RE010' => '10',
            'RE011' => '11',
            'RE012' => '12',
            'RE013' => '13',
            'RE014' => '14',
            'RE015' => '16',
            'RE016' => '15',
            'RE017' => '17',
            'RE018' => '27',
            'RE019' => '18',
            'RE020' => '19',
            'RE021' => '20',
            'RE022' => '21',
            'RE023' => '22',
            'RE024' => '23',
            'RE025' => '25',
            'RE026' => '26',
        ];
        foreach ($map as $newId => $oldId) {
            $this->addSql("UPDATE aamc_resource_type SET resource_type_id = '{$newId}' WHERE resource_type_id = '{$oldId}'");
            $this->addSql("UPDATE term_x_aamc_resource_type SET resource_type_id='{$newId}' WHERE resource_type_id = '{$oldId}'");
        }

        $this->addSql('ALTER TABLE term_x_aamc_resource_type ADD CONSTRAINT FK_F4C4B9D698EC6B7B FOREIGN KEY (resource_type_id) REFERENCES aamc_resource_type (resource_type_id)');
        $this->addSql('CREATE INDEX IDX_F4C4B9D698EC6B7B ON term_x_aamc_resource_type (resource_type_id)');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
