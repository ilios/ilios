<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Optimize the column order of each table
 * This also serves to ensure that databases upgraded along different paths are in sync
 */
class Version20150901000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE alert MODIFY dispatched tinyint(1) NOT NULL AFTER table_row_id');
        $this->addSql('ALTER TABLE authentication MODIFY person_id int(11) NOT NULL FIRST');
        $this->addSql('ALTER TABLE cohort MODIFY program_year_id int(11) DEFAULT NULL AFTER title');
        $this->addSql('ALTER TABLE competency MODIFY title varchar(200) DEFAULT NULL AFTER school_id');
        $this->addSql('ALTER TABLE course MODIFY `title` varchar(200) DEFAULT NULL AFTER clerkship_type_id');
        $this->addSql('ALTER TABLE course MODIFY `external_id` varchar(18) DEFAULT NULL AFTER title');
        $this->addSql('ALTER TABLE department MODIFY `school_id` int(11) DEFAULT NULL AFTER department_id');
        $this->addSql('ALTER TABLE discipline MODIFY `school_id` int(11) DEFAULT NULL AFTER discipline_id');
        $this->addSql('ALTER TABLE `group` MODIFY `parent_group_id` int(11) DEFAULT NULL AFTER group_id');
        $this->addSql('ALTER TABLE `group` MODIFY `cohort_id` int(11) DEFAULT NULL AFTER parent_group_id');
        $this->addSql('ALTER TABLE instructor_group MODIFY `school_id` int(11) DEFAULT NULL AFTER instructor_group_id');
        $this->addSql('ALTER TABLE learning_material MODIFY `learning_material_status_id` int(11) DEFAULT NULL AFTER learning_material_id');
        $this->addSql('ALTER TABLE learning_material MODIFY `learning_material_user_role_id` int(11) DEFAULT NULL AFTER `learning_material_status_id`');
        $this->addSql('ALTER TABLE objective MODIFY `competency_id` int(11) DEFAULT NULL AFTER objective_id');
        $this->addSql('ALTER TABLE report MODIFY `title` varchar(240) DEFAULT NULL AFTER report_id');
        $this->addSql('ALTER TABLE program MODIFY `school_id` int(11) DEFAULT NULL AFTER program_id');
        $this->addSql('ALTER TABLE program MODIFY `publish_event_id` int(11) DEFAULT NULL AFTER school_id');
        $this->addSql('ALTER TABLE session MODIFY `session_type_id` int(11) DEFAULT NULL AFTER session_id');
        $this->addSql('ALTER TABLE session MODIFY `course_id` int(11) DEFAULT NULL AFTER session_type_id');
        $this->addSql('ALTER TABLE session_description MODIFY `description_id` int(11) NOT NULL AUTO_INCREMENT first');
        $this->addSql('ALTER TABLE session_type MODIFY `title` VARCHAR(100) NOT NULL AFTER assessment_option_id');
        $this->addSql('ALTER TABLE user MODIFY `school_id` int(11) DEFAULT NULL AFTER user_id');
        $this->addSql('ALTER TABLE user MODIFY `primary_cohort_id` int(11) DEFAULT NULL AFTER school_id');
        $this->addSql('ALTER TABLE user_made_reminder MODIFY `user_id` int(11) DEFAULT NULL AFTER user_made_reminder_id');
        
        //drop and add these keys to put them in the correct order
        //this is a superficial change to bring the DB schemas into line
        $this->addSql('ALTER TABLE `course_learning_material` DROP FOREIGN KEY `FK_F841D788591CC992`');
        $this->addSql('ALTER TABLE `course_learning_material` DROP FOREIGN KEY `FK_F841D788C1D99609`');
        $this->addSql('ALTER TABLE `course_learning_material` ADD CONSTRAINT `FK_F841D788591CC992` FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE `course_learning_material` ADD CONSTRAINT `FK_F841D788C1D99609` FOREIGN KEY (learning_material_id) REFERENCES learning_material (learning_material_id)');

        $this->addSql('ALTER TABLE `program_year` DROP FOREIGN KEY `FK_B66426303EB8070A`');
        $this->addSql('ALTER TABLE `program_year` DROP FOREIGN KEY `FK_B664263056C92BE0`');
        $this->addSql('ALTER TABLE `program_year` ADD CONSTRAINT `FK_B664263056C92BE0` FOREIGN KEY (`publish_event_id`) REFERENCES `publish_event` (`publish_event_id`)');
        $this->addSql('ALTER TABLE `program_year` ADD CONSTRAINT `FK_B66426303EB8070A` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)');
        
        $this->addSql('ALTER TABLE `session_learning_material` DROP FOREIGN KEY `FK_9BE2AF8D613FECDF`');
        $this->addSql('ALTER TABLE `session_learning_material` DROP FOREIGN KEY `FK_9BE2AF8DC1D99609`');
        $this->addSql('ALTER TABLE `session_learning_material` ADD CONSTRAINT `FK_9BE2AF8D613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`)');
        $this->addSql('ALTER TABLE `session_learning_material` ADD CONSTRAINT `FK_9BE2AF8DC1D99609` FOREIGN KEY (`learning_material_id`) REFERENCES `learning_material` (`learning_material_id`)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        //we don't need to do anything as the ordering isn't destructive
    }
}
