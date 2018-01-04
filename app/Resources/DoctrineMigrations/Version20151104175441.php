<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration rids Ilios of the notion of 'soft-deletes' by removing all records and associations to
 * records that are flagged as 'deleted', and then by removing all 'deleted' columns from tables.
 *
 * Obligatory ACHTUNG!
 * The deletion of records is irreversible, data loss will occur.
 * [ST 2015/11/04]
 */
class Version20151104175441 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        // Remove soft-deleted departments.
        $this->addSql('DELETE from department WHERE deleted = true');

        // Remove soft-deleted reports.
        $this->addSql('DELETE FROM report WHERE deleted = true');

        // Remove permissions to soft-deleted courses/programs/schools.
        $this->addSql("DELETE FROM permission WHERE table_name = 'course' AND table_row_id IN (SELECT course_id FROM course WHERE deleted = true)");
        $this->addSql("DELETE FROM permission WHERE table_name = 'program' AND table_row_id IN (SELECT program_id FROM program WHERE deleted = true)");
        $this->addSql("DELETE FROM permission WHERE table_name = 'school' AND table_row_id IN (SELECT school_id FROM school WHERE deleted = true)");

        // Remove soft-deleted offerings and associations.
        // Then, delete offerings and associations belonging to soft-deleted sessions and courses.
        $this->addSql("DELETE FROM offering_x_learner WHERE offering_id IN (SELECT offering_id FROM offering WHERE deleted = true)");
        $this->addSql("DELETE FROM offering_x_group WHERE offering_id IN (SELECT offering_id FROM offering WHERE deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor WHERE offering_id IN (SELECT offering_id FROM offering WHERE deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor_group WHERE offering_id IN (SELECT offering_id FROM offering WHERE deleted = true)");
        $this->addSql("DELETE FROM offering WHERE deleted = true");

        $this->addSql("DELETE FROM offering_x_learner WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id WHERE `session`.deleted = true)");
        $this->addSql("DELETE FROM offering_x_group WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id WHERE `session`.deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id WHERE `session`.deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor_group WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id WHERE `session`.deleted = true)");
        $this->addSql("DELETE FROM offering WHERE session_id IN (SELECT session_id FROM `session` WHERE deleted = true)");

        $this->addSql("DELETE FROM offering_x_learner WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM offering_x_group WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM offering_x_instructor_group WHERE offering_id IN (SELECT offering_id FROM offering JOIN `session` ON `session`.session_id = offering.session_id JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM offering WHERE session_id IN (SELECT session_id FROM `session` JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");

        // Remove soft-deleted sessions and sessions belonging to soft-deleted courses
        $this->addSql("DELETE FROM session_learning_material WHERE session_id IN (SELECT session_id FROM `session` WHERE deleted = true)");
        $this->addSql("DELETE FROM session_description WHERE session_id IN (SELECT session_id FROM `session` WHERE deleted = true)");
        $this->addSql("DELETE FROM curriculum_inventory_sequence_block_session WHERE session_id IN (SELECT session_id FROM `session` WHERE deleted = true)");
        $this->addSql("DELETE FROM `session` WHERE deleted = true");

        $this->addSql("DELETE FROM session_learning_material WHERE session_id IN (SELECT session_id FROM `session` JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM session_description WHERE session_id IN (SELECT session_id FROM `session` JOIN course ON course.course_id = `session`.course_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM curriculum_inventory_sequence_block_session WHERE session_id IN (SELECT session_id FROM `session` JOIN course ON course.course_id = `session`.session_id WHERE course.deleted = true)");
        $this->addSql("DELETE FROM `session` WHERE course_id IN (SELECT course_id FROM course WHERE deleted = true)");

        // Un-link curriculum inventory sequence blocks from soft-deleted courses
        $this->addSql("UPDATE curriculum_inventory_sequence_block SET course_id = NULL WHERE course_id IN (SELECT course_id FROM course WHERE deleted = true)");

        // Remove soft-deleted courses.
        $this->addSql("DELETE FROM course_director WHERE course_id IN (SELECT course_id FROM course WHERE deleted = true)");
        $this->addSql("DELETE FROM course_learning_material WHERE course_id IN (SELECT course_id FROM course WHERE deleted = true)");
        $this->addSql("DELETE FROM course WHERE deleted = true");

        // Delete curriculum inventory report related data belonging to reports owned by soft-deleted programs,
        // sans exports (see comment below).
        $this->addSql("DELETE FROM curriculum_inventory_academic_level WHERE report_id IN (SELECT report_id FROM curriculum_inventory_report JOIN program ON program.program_id = curriculum_inventory_report.program_id WHERE program.deleted = TRUE)");
        $this->addSql("UPDATE curriculum_inventory_sequence_block SET parent_sequence_block_id = NULL WHERE report_id IN (SELECT report_id FROM curriculum_inventory_report JOIN program ON program.program_id = curriculum_inventory_report.program_id WHERE program.deleted = TRUE)");
        $this->addSql("DELETE FROM curriculum_inventory_sequence_block WHERE report_id IN (SELECT report_id FROM curriculum_inventory_report JOIN program ON program.program_id = curriculum_inventory_report.program_id WHERE program.deleted = TRUE)");
        $this->addSql("DELETE FROM curriculum_inventory_sequence WHERE report_id IN (SELECT report_id FROM curriculum_inventory_report JOIN program ON program.program_id = curriculum_inventory_report.program_id WHERE program.deleted = TRUE)");

        // Delete curriculum inventory reports belonging to soft-deleted programs.
        // This will fail for exported reports.
        // Report-exports will have to be removed out-of-band before the report-owning
        // soft-deleted program.
        // [ST 2015/11/04]
        $this->addSql("DELETE FROM curriculum_inventory_report WHERE program_id IN (SELECT program_id FROM program WHERE deleted = true)");

        // Remove learner groups belonging to cohorts in soft-deleted program years/programs.
        $this->addSql("DELETE FROM group_x_user WHERE group_id IN (SELECT group_id FROM `group` JOIN cohort ON cohort.cohort_id = `group`.cohort_id JOIN program_year on program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program_year.deleted = true OR program.deleted = true)");
        $this->addSql("DELETE FROM offering_x_group WHERE group_id IN (SELECT group_id FROM `group` JOIN cohort ON cohort.cohort_id = `group`.cohort_id JOIN program_year on program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program_year.deleted = true OR program.deleted = true)");
        $this->addSql("UPDATE `group` SET parent_group_id = null WHERE cohort_id IN (SELECT cohort_id FROM cohort JOIN program_year on program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program_year.deleted = true OR program.deleted = true)");
        $this->addSql("DELETE FROM `group` WHERE cohort_id IN (SELECT cohort_id FROM cohort JOIN program_year on program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program_year.deleted = true OR program.deleted = true)");

        // Remove users from cohorts belonging to soft-deleted program years/programs.
        $this->addSql("UPDATE user SET primary_cohort_id = NULL WHERE primary_cohort_id IN (SELECT cohort_id from cohort JOIN program_year ON program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program.deleted = true OR program_year.deleted = true)");
        $this->addSql("DELETE FROM user_x_cohort WHERE cohort_id IN (SELECT cohort_id FROM cohort JOIN program_year on program_year.program_year_id = cohort.program_year_id JOIN program ON program.program_id = program_year.program_id WHERE program_year.deleted = true OR program.deleted = true)");

        // Remove cohorts belonging to soft-deleted program years/programs.
        $this->addSql("DELETE FROM cohort WHERE program_year_id IN (SELECT program_year_id FROM program_year JOIN program ON program.program_id = program_year.program_id  WHERE program_year.deleted = true OR program.deleted = TRUE)");

        // Remove soft-deleted program years.
        $this->addSql("DELETE FROM program_year_director WHERE program_year_id IN (SELECT program_year_id FROM program_year JOIN program ON program.program_id = program_year.program_id  WHERE program_year.deleted = true OR program.deleted = TRUE)");
        $this->addSql("DELETE FROM program_year WHERE deleted = true");
        $this->addSql("DELETE FROM program_year WHERE program_id IN (SELECT program_id FROM program WHERE deleted = true)");

        // Finally, remove soft-deleted programs.
        $this->addSql("DELETE FROM program WHERE deleted = true");

        // Remove soft-deleted schools. do not attempt to remove any non-cascading references.
        // this will most likely blow if there is anything (programs/users/etc) attached to a school to-be-deleted.
        // this must be handled out of band.
        // [ST 2015/11/04]
        $this->addSql('DELETE FROM school WHERE deleted = true');

        // drop 'deleted' columns.
        $this->addSql('ALTER TABLE course DROP deleted');
        $this->addSql('ALTER TABLE department DROP deleted');
        $this->addSql('ALTER TABLE offering DROP deleted');
        $this->addSql('ALTER TABLE program DROP deleted');
        $this->addSql('ALTER TABLE program_year DROP deleted');
        $this->addSql('ALTER TABLE report DROP deleted');
        $this->addSql('ALTER TABLE school DROP deleted');
        $this->addSql('ALTER TABLE session DROP deleted');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE school ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE offering ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE report ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE course ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE session ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE department ADD deleted TINYINT(1) NOT NULL');
    }
}
