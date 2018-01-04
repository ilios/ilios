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

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C32A47EE');
        $this->addSql('ALTER TABLE user CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        //for MySQL 5.6+, we need to drop previously-created FK Restraints that will prevent a change...
        $this->addSql('ALTER TABLE learning_material DROP FOREIGN KEY FK_58CE718B67A71A40');
        $this->addSql('ALTER TABLE learning_material DROP FOREIGN KEY FK_58CE718BA0407615');
        $this->addSql('ALTER TABLE learning_material DROP FOREIGN KEY FK_58CE718B7505C8EA');
        //... make the change...
        $this->addSql('ALTER TABLE learning_material CHANGE owning_user_id owning_user_id INT NOT NULL, CHANGE learning_material_user_role_id learning_material_user_role_id INT NOT NULL, CHANGE learning_material_status_id learning_material_status_id INT NOT NULL');
        //... and then re-add them after the necessary change takes place
        $this->addSql('ALTER TABLE learning_material ADD CONSTRAINT FK_58CE718B7505C8EA FOREIGN KEY (learning_material_user_role_id) REFERENCES learning_material_user_role (learning_material_user_role_id)');
        $this->addSql('ALTER TABLE learning_material ADD CONSTRAINT FK_58CE718BA0407615 FOREIGN KEY (learning_material_status_id) REFERENCES learning_material_status (learning_material_status_id)');
        $this->addSql('ALTER TABLE learning_material ADD CONSTRAINT FK_58CE718B67A71A40 FOREIGN KEY (owning_user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE user_made_reminder DROP FOREIGN KEY FK_44EF4595A76ED395');
        $this->addSql('ALTER TABLE user_made_reminder CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_made_reminder ADD CONSTRAINT FK_44EF4595A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('ALTER TABLE report CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C535983C93');
        $this->addSql('ALTER TABLE `group` CHANGE cohort_id cohort_id INT NOT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C535983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (cohort_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_steward DROP FOREIGN KEY FK_38AC2B7BC32A47EE');
        $this->addSql('ALTER TABLE program_year_steward DROP FOREIGN KEY FK_38AC2B7BCB2B0673');
        $this->addSql('ALTER TABLE program_year_steward CHANGE school_id school_id INT NOT NULL, CHANGE program_year_id program_year_id INT NOT NULL');
        $this->addSql('ALTER TABLE program_year_steward ADD CONSTRAINT FK_38AC2B7BC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_steward ADD CONSTRAINT FK_38AC2B7BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_learning_material DROP FOREIGN KEY FK_9BE2AF8DC1D99609');
        $this->addSql('ALTER TABLE session_learning_material CHANGE learning_material_id learning_material_id INT NOT NULL');
        $this->addSql('ALTER TABLE session_learning_material ADD CONSTRAINT FK_9BE2AF8DC1D99609 FOREIGN KEY (learning_material_id) REFERENCES learning_material (learning_material_id)');
        $this->addSql('ALTER TABLE session_type DROP FOREIGN KEY FK_4AAF5703C32A47EE');
        $this->addSql('ALTER TABLE session_type CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE session_type ADD CONSTRAINT FK_4AAF5703C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B6806081C3B0');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block DROP FOREIGN KEY FK_22E6B680591CC992');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block CHANGE academic_level_id academic_level_id INT NOT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B6806081C3B0 FOREIGN KEY (academic_level_id) REFERENCES curriculum_inventory_academic_level (academic_level_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block ADD CONSTRAINT FK_22E6B680591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4591CC992');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4D7940EC9');
        $this->addSql('ALTER TABLE session CHANGE course_id course_id INT NOT NULL, CHANGE session_type_id session_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4D7940EC9 FOREIGN KEY (session_type_id) REFERENCES session_type (session_type_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session DROP FOREIGN KEY FK_CF8E4F12613FECDF');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session DROP FOREIGN KEY FK_CF8E4F1261D1D223');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session CHANGE session_id session_id INT NOT NULL, CHANGE sequence_block_id sequence_block_id INT NOT NULL');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F12613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id)');
        $this->addSql('ALTER TABLE curriculum_inventory_sequence_block_session ADD CONSTRAINT FK_CF8E4F1261D1D223 FOREIGN KEY (sequence_block_id) REFERENCES curriculum_inventory_sequence_block (sequence_block_id)');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784C32A47EE');
        $this->addSql('ALTER TABLE program CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3FC32A47EE');
        $this->addSql('ALTER TABLE discipline CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3FC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('ALTER TABLE department DROP FOREIGN KEY FK_CD1DE18AC32A47EE');
        $this->addSql('ALTER TABLE department CHANGE school_id school_id INT NOT NULL');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
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
