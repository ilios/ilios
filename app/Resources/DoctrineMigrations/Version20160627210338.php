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
            'RE01' => '1',
            'RE02' => '2',
            'RE03' => '3',
            'RE04' => '4',
            'RE05' => '5',
            'RE06' => '6',
            'RE07' => '7',
            'RE08' => '8',
            'RE09' => '9',
            'RE10' => '10',
            'RE11' => '11',
            'RE12' => '12',
            'RE13' => '13',
            'RE14' => '14',
            'RE15' => '16',
            'RE16' => '15',
            'RE17' => '17',
            'RE18' => '27',
            'RE19' => '18',
            'RE20' => '19',
            'RE21' => '20',
            'RE22' => '21',
            'RE23' => '22',
            'RE24' => '23',
            'RE25' => '25',
            'RE26' => '26',
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
