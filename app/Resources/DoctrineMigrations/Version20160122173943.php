<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Change several columns to be not nullable.
 * @link https://github.com/ilios/ilios/issues/1270
 */
class Version20160122173943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        $this->addSql('ALTER TABLE user CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE learning_material CHANGE owning_user_id owning_user_id INT NOT NULL, CHANGE learning_material_user_role_id learning_material_user_role_id INT NOT NULL, CHANGE learning_material_status_id learning_material_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_made_reminder CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE report CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE `group` CHANGE cohort_id cohort_id INT NOT NULL');
        $this->addSql('ALTER TABLE program_year_steward CHANGE school_id school_id INT NOT NULL, CHANGE program_year_id program_year_id INT NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material CHANGE learning_material_id learning_material_id INT NOT NULL');
        $this->addSql('ALTER TABLE session_type CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block CHANGE academic_level_id academic_level_id INT NOT NULL');
        $this->addSql('ALTER TABLE session CHANGE course_id course_id INT NOT NULL, CHANGE session_type_id session_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session CHANGE session_id session_id INT NOT NULL, CHANGE sequence_block_id sequence_block_id INT NOT NULL');
        $this->addSql('ALTER TABLE program CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE discipline CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE department CHANGE school_id school_id INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block CHANGE academic_level_id academic_level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session CHANGE sequence_block_id sequence_block_id INT DEFAULT NULL, CHANGE session_id session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE department CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `group` CHANGE cohort_id cohort_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE learning_material CHANGE learning_material_user_role_id learning_material_user_role_id INT DEFAULT NULL, CHANGE learning_material_status_id learning_material_status_id INT DEFAULT NULL, CHANGE owning_user_id owning_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program_year_steward CHANGE program_year_id program_year_id INT DEFAULT NULL, CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session CHANGE session_type_id session_type_id INT DEFAULT NULL, CHANGE course_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session_learning_material CHANGE learning_material_id learning_material_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session_type CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_made_reminder CHANGE user_id user_id INT DEFAULT NULL');
    }
}
