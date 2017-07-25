<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Replaces the existing sequence-block/session join table with a streamlined equivalent.
 * Class Version20160812180141
 */
class Version20160812180141 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE curriculum_inventory_sequence_block_x_session (sequence_block_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_E1268BFB61D1D223 (sequence_block_id), INDEX IDX_E1268BFB613FECDF (session_id), PRIMARY KEY(sequence_block_id, session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_x_session ADD CONSTRAINT FK_E1268BFB61D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_x_session ADD CONSTRAINT FK_E1268BFB613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO curriculum_inventory_sequence_block_x_session (sequence_block_id, session_id) (SELECT sequence_block_id, session_id FROM curriculum_inventory_sequence_block_session)');
        $this->addSql('DROP TABLE curriculum_inventory_sequence_block_session');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE curriculum_inventory_sequence_block_session (sequence_block_session_id BIGINT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, sequence_block_id INT NOT NULL, count_offerings_once TINYINT(1) NOT NULL, UNIQUE INDEX report_session (sequence_block_id, session_id), INDEX IDX_CF8E4F1261D1D223 (sequence_block_id), INDEX fkey_curriculum_inventory_sequence_block_session_session_id (session_id), PRIMARY KEY(sequence_block_session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F12613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F1261D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO curriculum_inventory_sequence_block_session (sequence_block_id, session_id, count_offerings_once) (SELECT sequence_block_id, session_id, TRUE FROM curriculum_inventory_sequence_block_x_session)');
        $this->addSql('DROP TABLE curriculum_inventory_sequence_block_x_session');
    }
}
