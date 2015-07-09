<?php
//its in a class because I already had the code that way and I din't feel like extracting it.
$migration = new Migrate();
$migration->printQueries();

class Migrate
{

    public function printQueries()
    {

        $this->printAddProcedures();

        $queries = array();
        $queries = array_merge($queries, $this->getDropTriggers());
        $queries = array_merge($queries, $this->getDropForeignKeys());
        $queries = array_merge($queries, $this->getDropIndexes());
        $queries = array_merge($queries, $this->getDropKeys());
        $queries = array_merge($queries, $this->getDropPrimaryKeys());
        $queries = array_merge($queries, $this->getDropTables());
        $queries = array_merge($queries, $this->getChangeEngine());
        $queries = array_merge($queries, $this->getChangeCharset());
        $queries = array_merge($queries, $this->getAddColumns());
        $queries = array_merge($queries, $this->getColumnChanges());
        $queries = array_merge($queries, $this->getDropColumns());
        $queries = array_merge($queries, $this->getAddPrimaryKeys());
        $queries = array_merge($queries, $this->getAddIndexes());
        $queries = array_merge($queries, $this->getAddForeignKeys());

        foreach ($queries as $sql) {
            print $sql . ";\n";
        }

        $this->printDropProcedures();
    }

    protected function printAddProcedures()
    {
        $this->printDropProcedures();
        print "delimiter //\n";
        print "CREATE PROCEDURE drop_index_if_exists(theTable VARCHAR(128), theIndexName VARCHAR(128))\n"
        . "BEGIN\n"
        . " IF((SELECT COUNT(*) AS index_exists FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() and table_name = theTable AND index_name = theIndexName) > 0) THEN\n"
        . "   SET @s = CONCAT('DROP INDEX ' , theIndexName , ' ON ' , theTable);\n"
        . "   PREPARE stmt FROM @s;\n"
        . "   EXECUTE stmt;\n"
        . " END IF;\n"
        . "END\n"
        . '//' . "\n";
        print "CREATE PROCEDURE drop_key_if_exists(theTable VARCHAR(128), theName VARCHAR(128))\n"
        . "BEGIN\n"
        . " IF((SELECT COUNT(*) AS index_exists FROM information_schema.statistics WHERE TABLE_SCHEMA = DATABASE() and table_name = theTable AND index_name = theName) > 0) THEN\n"
        . "   SET @s = CONCAT('ALTER TABLE `' , theTable , '` DROP KEY ' , theName);\n"
        . "   PREPARE stmt FROM @s;\n"
        . "   EXECUTE stmt;\n"
        . " END IF;\n"
        . "END\n"
        . '//' . "\n";
        print "CREATE PROCEDURE drop_fk_if_exists(theTable VARCHAR(128), theName VARCHAR(128))\n"
        . "BEGIN\n"
        . " IF((SELECT COUNT(*) AS doesExist FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() and table_name = theTable AND CONSTRAINT_NAME = theName) > 0) THEN\n"
        . "   SET @s = CONCAT('ALTER TABLE `' , theTable , '` DROP FOREIGN KEY ' , theName);\n"
        . "   PREPARE stmt FROM @s;\n"
        . "   EXECUTE stmt;\n"
        . " END IF;\n"
        . "END\n"
        . '//' . "\n";

        print "delimiter ;\n";
    }

    protected function printDropProcedures()
    {
        print "DROP PROCEDURE IF EXISTS drop_index_if_exists;\n";
        print "DROP PROCEDURE IF EXISTS drop_key_if_exists;\n";
        print "DROP PROCEDURE IF EXISTS drop_fk_if_exists;\n";

    }

    protected function getDropTriggers()
    {
        $queries = [];
        $queries[] = "DROP TRIGGER IF EXISTS `trig_session_pre_update`";
        $queries[] = "DROP TRIGGER IF EXISTS `trig_offering_x_group_post_delete`";
        $queries[] = "DROP TRIGGER IF EXISTS `trig_offering_x_instructor_post_delete`";
        $queries[] = "DROP TRIGGER IF EXISTS `trig_offering_instructor_post_delete`";
        $queries[] = "DROP TRIGGER IF EXISTS `trig_offering_x_learner_post_delete`";
        $queries[] = "DROP TRIGGER IF EXISTS `trig_offering_learner_post_delete`";

        return $queries;
    }

    protected function getDropIndexes()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'alert_change',
            'index' => 'alert_id_alert_change_type_id'
        );
        $changes[] = array(
            'table' => 'alert_change',
            'index' => 'alert_id'
        );
        $changes[] = array(
            'table' => 'alert_change',
            'index' => 'fkey_alert_change_alert_type_id'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'index' => 'alert_id_user_id'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'index' => 'alert_id'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'index' => 'user_id'
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'index' => 'alert_id_school_id'
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'index' => 'fk_d97ae69dc32a47ee'
        );
        $changes[] = array(
            'table' => 'api_key',
            'index' => 'api_key'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'index' => 'idx_audit_atom_created_at'
        );
        $changes[] = array(
            'table' => 'authentication',
            'index' => 'username'
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'index' => 'aamc_pcrs_id_fkey'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'index' => 'course_lm_k'
        );
        $changes[] = array(
            'table' => 'course',
            'index' => 'external_id_k'
        );
        $changes[] = array(
            'table' => 'course',
            'index' => 'external_id'
        );
        $changes[] = array(
            'table' => 'course_director',
            'index' => 'fkey_course_director_user_id'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'index' => 'fkey_course_x_cohort_cohort_id'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'index' => 'course_cohort_id_k'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'index' => 'course_discipline_id_k'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'index' => 'fkey_course_x_discipline_discipline_id'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'index' => 'fkey_course_x_objective_objective_id'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'index' => 'course_objective_id_k'
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'index' => 'fkey_course_x_mesh_course_id'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'index' => 'learning_material_id_k'
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'index' => 'clm_id_k'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'fkey_curriculum_inventory_sequence_block_academic_level_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'fkey_curriculum_inventory_sequence_block_course_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'fkey_curriculum_inventory_sequence_block_parent_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'fkey_curriculum_inventory_sequence_block_report_id'
        );
        $changes[] = array(
            'table' => 'department',
            'index' => 'department_school_k'
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'index' => 'fkey_group_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'index' => 'group_user_id_k'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'index' => 'user_id_k'
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'index' => 'fkey_group_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'index' => 'fkey_ilm_session_facet_x_group_group_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'index' => 'fkey_ilm_session_facet_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'index' => 'fkey_ilm_session_facet_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'index' => 'fkey_ilm_session_facet_x_learner_user_id'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'index' => 'user_id_k'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'index' => 'instructor_group_user_id_k'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'index' => 'cn_index'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'index' => 'sn_index'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'index' => 'n_index'
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'index' => 'mesh_concept_uid'
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'index' => 'mesh_term_uid'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor',
            'index' => 'a_index'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor',
            'index' => 'n_index'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'index' => 'mesh_concept_uid'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'index' => 'mesh_descriptor_uid'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'index' => 'mesh_qualifier_uid'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'index' => 'mesh_descriptor_uid'
        );
        $changes[] = array(
            'table' => 'mesh_previous_indexing',
            'index' => 'pi_index'
        );
        $changes[] = array(
            'table' => 'mesh_qualifier',
            'index' => 'n_index'
        );
        $changes[] = array(
            'table' => 'mesh_semantic_type',
            'index' => 'n_index'
        );
        $changes[] = array(
            'table' => 'mesh_term',
            'index' => 'n_index'
        );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'index' => 'sp_index'
        );
        $changes[] = array(
            'table' => 'objective',
            'index' => 'fkey_objective_competency'
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'index' => 'fkey_objective_x_objective_objective_id'
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'index' => 'objective_objective_id_k'
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'index' => 'fkey_objective_x_mesh_objective_id'
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'index' => 'fkey_offering_x_group_group_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'index' => 'fkey_offering_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'index' => 'fkey_offering_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'index' => 'fkey_offering_x_learner_user_id'
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'index' => 'offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'index' => 'offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'index' => 'offering_id'
        );
        $changes[] = array(
            'table' => 'program_year',
            'index' => 'fkey_program_year_program_id'
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'index' => 'fkey_program_year_director_user'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'index' => 'fkey_program_year_x_competency_competency_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'index' => 'program_year_competency_id_k'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'index' => 'fkey_program_year_x_competency_competency_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'index' => 'program_year_discipline_id_k'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'index' => 'fkey_program_year_x_discipline_discipline_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'index' => 'program_year_objective_id_k'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'index' => 'fkey_program_year_x_objective_obj_id'
        );
        $changes[] = array(
            'table' => 'report_po_value',
            'index' => 'fkey_report_po_value_report_id'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'index' => 'fkey_recurring_event_previous_recurring_event_id'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'index' => 'fkey_recurring_event_next_recurring_event_id'
        );
        $changes[] = array(
            'table' => 'session_description',
            'index' => 'session_id_k'
        );
        $changes[] = array(
            'table' => 'session_type',
            'index' => 'session_type_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session_type',
            'index' => 'type_color_k'
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'index' => 'session_type_id_method_id'
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'index' => 'slm_id_k'
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'index' => 'aamc_method_id_fkey'
        );
        $changes[] = array(
            'index' => 'fkey_session_x_objective_objective_id',
            'table' => 'session_x_objective'
        );
        $changes[] = array(
            'index' => 'fkey_session_x_discipline_discipline_id',
            'table' => 'session_x_discipline'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'index' => 'fkey_user_x_cohort_cohort'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'index' => 'user_x_user_role_user_id'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'index' => 'user_x_user_role_user_role_id'
        );

        $queries = array();

        foreach ($changes as $arr) {
            $queries[] = "CALL drop_index_if_exists('{$arr['table']}', '{$arr['index']}')";
        }

        return $queries;
    }

    protected function getDropKeys()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'group',
            'key' => 'fkey_group_cohort_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'key' => 'fkey_program_year_x_objective_obj_id'
        );
        $changes[] = array(
            'table' => 'session_type',
            'key' => 'assessment_option_fkey'
        );
        $changes[] = array(
            'table' => 'mesh_term',
            'key' => 'mesh_term_uid'
        );
        $changes[] = array(
            'table' => 'mesh_tree_x_descriptor',
            'key' => 'mesh_descriptor_uid'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_department'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_school'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'program_year_id_school_id_department_id'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_school'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_department'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'py_s_k'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'key' => 'fkey_user_x_cohort_cohort'
        );

        $queries = array();
        foreach ($changes as $arr) {
            $queries[] = "CALL drop_key_if_exists('{$arr['table']}', '{$arr['key']}')";
        }

        return $queries;
    }

    protected function getDropForeignKeys()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'alert_change',
            'key' => 'fkey_alert_change_alert_id'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'key' => 'fkey_alert_instigator_user_id'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'key' => 'fkey_alert_instigator_alert_id'
        );
        $changes[] = array(
            'table' => 'api_key',
            'key' => 'fk_api_key_user_id'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'key' => 'fkey_audit_atom_created_by'
        );
        $changes[] = array(
            'table' => 'audit_content',
            'key' => 'audit_content_ibfk_1'
        );
        $changes[] = array(
            'table' => 'audit_event',
            'key' => 'audit_event_ibfk_1'
        );
        $changes[] = array(
            'table' => 'authentication',
            'key' => 'fkey_authentication_user'
        );
        $changes[] = array(
            'table' => 'cohort',
            'key' => 'fkey_cohort_program_year_id'
        );
        $changes[] = array(
            'table' => 'competency',
            'key' => 'competency_ibfk_1'
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'key' => 'aamc_pcrs_id_fkey'
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'key' => 'competency_id_fkey'
        );
        $changes[] = array(
            'table' => 'course_director',
            'key' => 'fkey_course_director_course_id'
        );
        $changes[] = array(
            'table' => 'course_director',
            'key' => 'fkey_course_director_user_id'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'key' => 'course_learning_material_ibfk_1'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'key' => 'course_learning_material_ibfk_2'
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'key' => 'course_learning_material_x_mesh_ibfk_1'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'key' => 'fkey_course_x_cohort_course_id'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'key' => 'fkey_course_x_cohort_cohort_id'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'key' => 'fkey_course_x_discipline_course_id'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'key' => 'fkey_course_x_discipline_discipline_id'
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'key' => 'fkey_course_x_mesh_course_id'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'key' => 'fkey_course_x_objective_course_id'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'key' => 'fkey_course_x_objective_objective_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_academic_level',
            'key' => 'fkey_curriculum_inventory_academic_level_report_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'key' => 'fkey_curriculum_inventory_export_report_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'key' => 'fkey_curriculum_inventory_export_user_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_institution',
            'key' => 'fkey_curriculum_inventory_institution_school_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_report',
            'key' => 'fkey_curriculum_inventory_report_program_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence',
            'key' => 'fkey_curriculum_inventory_sequence_report_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_parent_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_report_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_course_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_academic_level_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'key' => 'fkey_ci_sequence_block_session_sequence_block_id'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'key' => 'fkey_curriculum_inventory_sequence_block_session_session_id'
        );
        $changes[] = array(
            'table' => 'group',
            'key' => 'fkey_group_cohort_id'
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'key' => 'fkey_group_x_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'key' => 'fkey_group_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'key' => 'fkey_group_x_instructor_group_group_id'
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'key' => 'fkey_group_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'key' => 'group_x_user_ibfk_1'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'key' => 'group_x_user_ibfk_2'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'key' => 'fkey_ilm_session_facet_x_group_group_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'key' => 'fkey_ilm_session_facet_x_group_ilm_session_facet_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'key' => 'fkey_ilm_session_facet_x_instructor_ilm_session_facet_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'key' => 'fkey_ilm_session_facet_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'key' => 'fkey_ilm_session_facet_x_instructor_group_ilm_session_facet_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'key' => 'fkey_ilm_session_facet_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'key' => 'fkey_ilm_session_facet_x_learner_user_id'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'key' => 'fkey_ilm_session_facet_x_learner_ilm_session_facet_id'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'key' => 'instructor_group_x_user_ibfk_2'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'key' => 'instructor_group_x_user_ibfk_1'
        );
        $changes[] = array(
            'table' => 'objective',
            'key' => 'fkey_objective_competency'
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'key' => 'fkey_objective_x_mesh_objective_id'
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'key' => 'fkey_objective_x_objective_objective_id'
        );
        $changes[] = array(
            'table' => 'offering',
            'key' => 'offering_ibfk_1'
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'key' => 'fkey_offering_x_group_offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'key' => 'fkey_offering_x_group_group_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'key' => 'fkey_offering_x_instructor_user_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'key' => 'fkey_offering_x_instructor_offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'key' => 'fkey_offering_x_instructor_group_offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'key' => 'fkey_offering_x_instructor_group_instructor_group_id'
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'key' => 'fkey_offering_x_learner_offering_id'
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'key' => 'fkey_offering_x_learner_user_id'
        );
        $changes[] = array(
            'table' => 'program_year',
            'key' => 'fkey_program_year_program_id'
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'key' => 'fkey_program_year_director_user'
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'key' => 'fkey_program_year_director_program_year'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_department'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_school'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_program_year'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'key' => 'fkey_program_year_x_competency_prg_yr_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'key' => 'fkey_program_year_x_competency_competency_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'key' => 'fkey_program_year_x_discipline_prg_yr_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'key' => 'fkey_program_year_x_discipline_discipline_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'key' => 'fkey_program_year_x_objective_obj_id'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'key' => 'fkey_program_year_x_objective_prg_yr_id'
        );
        $changes[] = array(
            'table' => 'report_po_value',
            'key' => 'fkey_report_po_value_report_id'
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_3'
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_2'
        );
        $changes[] = array(
            'table' => 'session_description',
            'key' => 'session_description_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'key' => 'session_learning_material_ibfk_2'
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'key' => 'session_learning_material_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'key' => 'session_learning_material_x_mesh_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'key' => 'session_learning_material_x_mesh_fk_session'
        );
        $changes[] = array(
            'table' => 'session_type',
            'key' => 'assessment_option_fkey'
        );
        $changes[] = array(
            'table' => 'session_type',
            'key' => 'session_type_ibfk_1'
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'key' => 'aamc_method_id_fkey'
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'key' => 'session_type_id_fkey'
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'key' => 'fkey_session_x_discipline_session_id'
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'key' => 'fkey_session_x_discipline_discipline_id'
        );
        $changes[] = array(
            'table' => 'session_x_mesh',
            'key' => 'fkey_session_x_mesh_session_id'
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'key' => 'fkey_session_x_objective_session_id'
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'key' => 'fkey_session_x_objective_objective_id'
        );
        $changes[] = array(
            'table' => 'user',
            'key' => 'fkey_user_primary_school'
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'key' => 'user_id_fkey'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'key' => 'fkey_user_x_cohort_cohort'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'key' => 'fkey_user_x_cohort_user'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'key' => 'fkey_user_x_user_role_user_role_id'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'key' => 'fkey_user_x_user_role_user_id'
        );
        foreach ($changes as $arr) {
            $queries[] = "CALL drop_fk_if_exists('{$arr['table']}', '{$arr['key']}')";
        }

        return $queries;
    }

    protected function getDropPrimaryKeys()
    {
        $arr = array(
            'session_type_x_aamc_method'
        );

        $queries = array();
        foreach ($arr as $table) {
            $queries[] = "ALTER TABLE `{$table}` DROP PRIMARY KEY";
        }
        return $queries;
    }

    protected function getAddColumns()
    {
        $changes = array();

        $changes[] = array(
            'table' => 'authentication',
            'column' => 'password_bcrypt',
            'definition' => 'VARCHAR(255) DEFAULT NULL'
        );

        $changes[] = array(
            'table' => 'authentication',
            'column' => 'eppn',
            'definition' => 'VARCHAR(255) DEFAULT NULL'
        );

        $changes[] = array(
            'table' => 'program_year_steward',
            'column' => 'program_year_steward_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL PRIMARY KEY'
        );

        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'type',
            'definition' => 'VARCHAR(255) NOT NULL'
        );

        $changes[] = array(
            'table' => 'user',
            'column' => 'primary_cohort_id',
            'definition' => 'INT DEFAULT NULL'
        );

        $queries = array();
        foreach ($changes as $arr) {
            $queries[] = "ALTER TABLE `{$arr['table']}` ADD `{$arr['column']}` {$arr['definition']}";
        }

        $queries[] = "UPDATE learning_material set type= CASE " .
        "WHEN citation IS NOT NULL THEN 'citation' " .
        "WHEN filename IS NOT NULL THEN 'file' " .
        "ELSE 'link' END";

        $queries[] = "UPDATE user u SET primary_cohort_id = " .
        "(SELECT cohort_id from user_x_cohort x " .
        "WHERE x.user_id = u.user_id AND " .
        "x.is_primary)";

        return $queries;
    }

    protected function getColumnChanges()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'aamc_method',
            'column' => 'method_id',
            'definition' => 'VARCHAR(10) NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert',
            'column' => 'alert_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert',
            'column' => 'table_row_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_change',
            'column' => 'alert_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_change',
            'column' => 'alert_change_type_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_change_type',
            'column' => 'alert_change_type_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'column' => 'alert_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'column' => 'school_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'column' => 'alert_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'api_key',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'assessment_option',
            'column' => 'assessment_option_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'column' => 'event_type',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'column' => 'created_by',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'column' => 'table_row_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'column' => 'audit_atom_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'authentication',
            'column' => 'password_sha256',
            'definition' => 'VARCHAR(64) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'authentication',
            'column' => 'person_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ci_sessions',
            'column' => 'ip_address',
            'definition' => 'VARCHAR(45) NOT NULL'
        );
        $changes[] = array(
            'table' => 'ci_sessions',
            'column' => 'last_activity',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ci_sessions',
            'column' => 'session_id',
            'definition' => 'VARCHAR(40) NOT NULL'
        );
        $changes[] = array(
            'table' => 'cohort',
            'column' => 'program_year_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'cohort',
            'column' => 'cohort_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'competency',
            'column' => 'owning_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'competency',
            'column' => 'parent_competency_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'competency',
            'column' => 'title',
            'definition' => 'VARCHAR(200) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'competency',
            'column' => 'competency_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'column' => 'competency_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'title',
            'definition' => 'VARCHAR(200) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'owning_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'year',
            'definition' => 'SMALLINT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'course_level',
            'definition' => 'SMALLINT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'publish_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'course',
            'column' => 'course_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_director',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_director',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'column' => 'course_learning_material_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'column' => 'learning_material_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'column' => 'course_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'column' => 'course_learning_material_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'column' => 'cohort_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'column' => 'discipline_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'column' => 'course_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'column' => 'objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_academic_level',
            'column' => 'level',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_academic_level',
            'column' => 'report_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_academic_level',
            'column' => 'academic_level_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'column' => 'created_on',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'column' => 'report_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'column' => 'created_by',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_institution',
            'column' => 'address_country_code',
            'definition' => 'VARCHAR(2) NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_institution',
            'column' => 'school_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_report',
            'column' => 'report_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_report',
            'column' => 'year',
            'definition' => 'SMALLINT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_report',
            'column' => 'program_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence',
            'column' => 'report_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'track',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'academic_level_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'duration',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'maximum',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'course_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'report_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'parent_sequence_block_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'sequence_block_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'minimum',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'required',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'order_in_sequence',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'column' => 'child_sequence_order',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'column' => 'count_offerings_once',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'column' => 'sequence_block_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'column' => 'session_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'column' => 'sequence_block_session_id',
            'definition' => 'BIGINT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'department',
            'column' => 'school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'department',
            'column' => 'department_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'discipline',
            'column' => 'title',
            'definition' => 'VARCHAR(200) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'discipline',
            'column' => 'owning_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'discipline',
            'column' => 'discipline_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group',
            'column' => 'parent_group_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'group',
            'column' => 'cohort_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'group',
            'column' => 'group_id',
            'definition' => 'INT SIGNED AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_group',
            'column' => 'group_a_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_group',
            'column' => 'group_b_id',
            'definition' => 'INT SIGNED NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'column' => 'group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'column' => 'instructor_group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'column' => 'group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'column' => 'group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet',
            'column' => 'hours',
            'definition' => 'NUMERIC(6, 2) NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT SIGNED AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'column' => 'group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'column' => 'instructor_group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'ingestion_exception',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'session_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'modification_time_stamp',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'hours_accrued',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'instruction_hours_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'column' => 'generation_time_stamp',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'instructor_group',
            'column' => 'school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'instructor_group',
            'column' => 'instructor_group_id',
            'definition' => 'INT SIGNED AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'column' => 'instructor_group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'learning_material_status_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'mime_type',
            'definition' => 'VARCHAR(96) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'token',
            'definition' => 'VARCHAR(64) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'learning_material_user_role_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'owning_user_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'filesize',
            'definition' => 'INT UNSIGNED DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'learning_material_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material',
            'column' => 'copyright_ownership',
            'definition' => 'TINYINT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material_status',
            'column' => 'learning_material_status_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'learning_material_user_role',
            'column' => 'learning_material_user_role_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'column' => 'updated_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'column' => 'mesh_concept_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_concept',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor',
            'column' => 'updated_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_descriptor',
            'column' => 'mesh_descriptor_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_previous_indexing',
            'column' => 'mesh_descriptor_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_qualifier',
            'column' => 'updated_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_qualifier',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_qualifier',
            'column' => 'mesh_qualifier_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_semantic_type',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_semantic_type',
            'column' => 'updated_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_semantic_type',
            'column' => 'mesh_semantic_type_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_term',
            'column' => 'updated_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_term',
            'column' => 'created_at',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_tree_x_descriptor',
            'column' => 'tree_number',
            'definition' => 'VARCHAR(31) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'column' => 'mesh_user_selection_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'column' => 'mesh_descriptor_uid',
            'definition' => 'VARCHAR(9) NOT NULL'
        );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'column' => 'search_phrase',
            'definition' => 'VARCHAR(127) NOT NULL'
        );
        $changes[] = array(
            'table' => 'objective',
            'column' => 'objective_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'objective',
            'column' => 'competency_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'column' => 'objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'column' => 'objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'column' => 'parent_objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering',
            'column' => 'last_updated_on',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering',
            'column' => 'publish_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'offering',
            'column' => 'offering_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering',
            'column' => 'session_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'column' => 'group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'column' => 'offering_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'column' => 'offering_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'column' => 'instructor_group_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'column' => 'offering_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'column' => 'offering_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'column' => 'recurring_event_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'column' => 'offering_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'permission',
            'column' => 'table_row_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'permission',
            'column' => 'permission_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'permission',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program',
            'column' => 'owning_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program',
            'column' => 'title',
            'definition' => 'VARCHAR(200) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program',
            'column' => 'publish_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program',
            'column' => 'duration',
            'definition' => 'SMALLINT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program',
            'column' => 'program_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year',
            'column' => 'publish_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program_year',
            'column' => 'start_year',
            'definition' => 'SMALLINT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year',
            'column' => 'program_year_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year',
            'column' => 'program_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'column' => 'program_year_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'column' => 'department_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'column' => 'school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'column' => 'program_year_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'column' => 'program_year_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'column' => 'competency_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'column' => 'discipline_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'column' => 'program_year_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'column' => 'program_year_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'column' => 'objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'publish_event',
            'column' => 'table_row_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'publish_event',
            'column' => 'table_name',
            'definition' => 'VARCHAR(30) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'publish_event',
            'column' => 'time_stamp',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'publish_event',
            'column' => 'publish_event_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'publish_event',
            'column' => 'administrator_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_saturday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'repetition_count',
            'definition' => 'SMALLINT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'next_recurring_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_friday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'previous_recurring_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_wednesday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_sunday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_thursday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_monday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'on_tuesday',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'column' => 'recurring_event_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'report',
            'column' => 'user_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'report',
            'column' => 'report_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'report_po_value',
            'column' => 'report_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'school',
            'column' => 'school_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'ilm_session_facet_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'title',
            'definition' => 'VARCHAR(200) DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'last_updated_on',
            'definition' => 'DATETIME NOT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'publish_event_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'session_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'course_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session',
            'column' => 'session_type_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_description',
            'column' => 'session_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_description',
            'column' => 'description',
            'definition' => 'TEXT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'column' => 'learning_material_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'column' => 'session_learning_material_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'column' => 'session_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'column' => 'session_learning_material_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_type',
            'column' => 'session_type_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_type',
            'column' => 'assessment',
            'definition' => 'TINYINT(1) NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_type',
            'column' => 'owning_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_type',
            'column' => 'assessment_option_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'column' => 'session_type_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'column' => 'session_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'column' => 'discipline_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_x_mesh',
            'column' => 'session_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'column' => 'session_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'column' => 'objective_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user',
            'column' => 'primary_school_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'user',
            'column' => 'user_id',
            'definition' => 'INT SIGNED AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_made_reminder',
            'column' => 'user_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'user_made_reminder',
            'column' => 'user_made_reminder_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_role',
            'column' => 'user_role_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'column' => 'user_id',
            'definition' => 'INT DEFAULT NULL'
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'column' => 'exception_id',
            'definition' => 'INT AUTO_INCREMENT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'column' => 'process_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'column' => 'exception_code',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'column' => 'cohort_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'column' => 'user_id',
            'definition' => 'INT NOT NULL'
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'column' => 'user_role_id',
            'definition' => 'INT NOT NULL'
        );

        $queries = array();
        foreach ($changes as $arr) {
            $queries[] = "ALTER TABLE `{$arr['table']}` CHANGE `{$arr['column']}` `{$arr['column']}` {$arr['definition']}";
        }

        return $queries;
    }


    protected function getAddForeignKeys()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'alert_change',
            'key' => 'fkey_alert_change_alert_type_id',
            'localColumn' => 'alert_change_type_id',
            'remoteTable' => 'alert_change_type',
            'remoteColumn' => 'alert_change_type_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'alert_change',
            'key' => 'fkey_alert_change_alert_id',
            'localColumn' => 'alert_id',
            'remoteTable' => 'alert',
            'remoteColumn' => 'alert_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'key' => 'fkey_alert_instigator_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'key' => 'fkey_alert_instigator_alert_id',
            'localColumn' => 'alert_id',
            'remoteTable' => 'alert',
            'remoteColumn' => 'alert_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'key' => 'FK_D97AE69DC32A47EE',
            'localColumn' => 'school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'key' => 'FK_D97AE69D93035F72',
            'localColumn' => 'alert_id',
            'remoteTable' => 'alert',
            'remoteColumn' => 'alert_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'api_key',
            'key' => 'fk_api_key_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'key' => 'fkey_audit_atom_created_by',
            'localColumn' => 'created_by',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'authentication',
            'key' => 'fkey_authentication_user',
            'localColumn' => 'person_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'cohort',
            'key' => 'fkey_cohort_program_year_id',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'competency',
            'key' => 'competency_ibfk_1',
            'localColumn' => 'parent_competency_id',
            'remoteTable' => 'competency',
            'remoteColumn' => 'competency_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'competency',
            'key' => 'fkey_competency_owning_school_id',
            'localColumn' => 'owning_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'key' => 'competency_id_fkey',
            'localColumn' => 'competency_id',
            'remoteTable' => 'competency',
            'remoteColumn' => 'competency_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'key' => 'aamc_pcrs_id_fkey',
            'localColumn' => 'pcrs_id',
            'remoteTable' => 'aamc_pcrs',
            'remoteColumn' => 'pcrs_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course',
            'key' => 'fkey_course_owning_school_id',
            'localColumn' => 'owning_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course',
            'key' => 'fkey_course_publish_event_id',
            'localColumn' => 'publish_event_id',
            'remoteTable' => 'publish_event',
            'remoteColumn' => 'publish_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_director',
            'key' => 'fkey_course_director_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_director',
            'key' => 'fkey_course_director_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'key' => 'course_learning_material_ibfk_2',
            'localColumn' => 'learning_material_id',
            'remoteTable' => 'learning_material',
            'remoteColumn' => 'learning_material_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'key' => 'FK_F841D788591CC992',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
           'table' => 'course_learning_material_x_mesh',
           'key' => 'FK_476BB36FCDB3C93B',
           'localColumn' => 'mesh_descriptor_uid',
           'remoteTable' => 'mesh_descriptor',
           'remoteColumn' => 'mesh_descriptor_uid',
           'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'key' => 'course_learning_material_x_mesh_ibfk_1',
            'localColumn' => 'course_learning_material_id',
            'remoteTable' => 'course_learning_material',
            'remoteColumn' => 'course_learning_material_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'key' => 'fkey_course_x_cohort_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'key' => 'fkey_course_x_cohort_cohort_id',
            'localColumn' => 'cohort_id',
            'remoteTable' => 'cohort',
            'remoteColumn' => 'cohort_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'key' => 'fkey_course_x_discipline_discipline_id',
            'localColumn' => 'discipline_id',
            'remoteTable' => 'discipline',
            'remoteColumn' => 'discipline_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'key' => 'fkey_course_x_discipline_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'key' => 'fkey_course_x_mesh_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
           'table' => 'course_x_mesh',
           'key' => 'fkey_course_x_mesh_descriptor',
           'localColumn' => 'mesh_descriptor_uid',
           'remoteTable' => 'mesh_descriptor',
           'remoteColumn' => 'mesh_descriptor_uid',
           'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'key' => 'fkey_course_x_objective_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'key' => 'fkey_course_x_objective_objective_id',
            'localColumn' => 'objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_academic_level',
            'key' => 'fkey_curriculum_inventory_academic_level_report_id',
            'localColumn' => 'report_id',
            'remoteTable' => 'curriculum_inventory_report',
            'remoteColumn' => 'report_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'key' => 'fkey_curriculum_inventory_export_user_id',
            'localColumn' => 'created_by',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'key' => 'fkey_curriculum_inventory_export_report_id',
            'localColumn' => 'report_id',
            'remoteTable' => 'curriculum_inventory_report',
            'remoteColumn' => 'report_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_institution',
            'key' => 'fkey_curriculum_inventory_institution_school_id',
            'localColumn' => 'school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_report',
            'key' => 'fkey_curriculum_inventory_report_program_id',
            'localColumn' => 'program_id',
            'remoteTable' => 'program',
            'remoteColumn' => 'program_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence',
            'key' => 'fkey_curriculum_inventory_sequence_report_id',
            'localColumn' => 'report_id',
            'remoteTable' => 'curriculum_inventory_report',
            'remoteColumn' => 'report_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_parent_id',
            'localColumn' => 'parent_sequence_block_id',
            'remoteTable' => 'curriculum_inventory_sequence_block',
            'remoteColumn' => 'sequence_block_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_course_id',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_report_id',
            'localColumn' => 'report_id',
            'remoteTable' => 'curriculum_inventory_report',
            'remoteColumn' => 'report_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'key' => 'fkey_curriculum_inventory_sequence_block_academic_level_id',
            'localColumn' => 'academic_level_id',
            'remoteTable' => 'curriculum_inventory_academic_level',
            'remoteColumn' => 'academic_level_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'key' => 'fkey_ci_sequence_block_session_sequence_block_id',
            'localColumn' => 'sequence_block_id',
            'remoteTable' => 'curriculum_inventory_sequence_block',
            'remoteColumn' => 'sequence_block_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block_session',
            'key' => 'fkey_curriculum_inventory_sequence_block_session_session_id',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'department',
            'key' => 'fkey_department_school_id',
            'localColumn' => 'school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'discipline',
            'key' => 'fkey_discipline_owning_school_id',
            'localColumn' => 'owning_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'group',
            'key' => 'fkey_group_parent_group_id',
            'localColumn' => 'parent_group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'group',
            'key' => 'fkey_group_cohort_id',
            'localColumn' => 'cohort_id',
            'remoteTable' => 'cohort',
            'remoteColumn' => 'cohort_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'key' => 'fkey_group_x_instructor_group_id',
            'localColumn' => 'group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'key' => 'fkey_group_x_instructor_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'key' => 'fkey_group_x_instructor_group_instructor_group_id',
            'localColumn' => 'instructor_group_id',
            'remoteTable' => 'instructor_group',
            'remoteColumn' => 'instructor_group_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'key' => 'fkey_group_x_instructor_group_group_id',
            'localColumn' => 'group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'key' => 'group_x_user_ibfk_1',
            'localColumn' => 'group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'key' => 'group_x_user_ibfk_2',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'key' => 'fkey_ilm_session_facet_x_group_ilm_session_facet_id',
            'localColumn' => 'ilm_session_facet_id',
            'remoteTable' => 'ilm_session_facet',
            'remoteColumn' => 'ilm_session_facet_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'key' => 'fkey_ilm_session_facet_x_group_group_id',
            'localColumn' => 'group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'key' => 'fkey_ilm_session_facet_x_instructor_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'key' => 'fkey_ilm_session_facet_x_instructor_ilm_session_facet_id',
            'localColumn' => 'ilm_session_facet_id',
            'remoteTable' => 'ilm_session_facet',
            'remoteColumn' => 'ilm_session_facet_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'key' => 'fkey_ilm_session_facet_x_instructor_group_instructor_group_id',
            'localColumn' => 'instructor_group_id',
            'remoteTable' => 'instructor_group',
            'remoteColumn' => 'instructor_group_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'key' => 'fkey_ilm_session_facet_x_instructor_group_ilm_session_facet_id',
            'localColumn' => 'ilm_session_facet_id',
            'remoteTable' => 'ilm_session_facet',
            'remoteColumn' => 'ilm_session_facet_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'key' => 'fkey_ilm_session_facet_x_learner_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'key' => 'fkey_ilm_session_facet_x_learner_ilm_session_facet_id',
            'localColumn' => 'ilm_session_facet_id',
            'remoteTable' => 'ilm_session_facet',
            'remoteColumn' => 'ilm_session_facet_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'ingestion_exception',
            'key' => 'fkey_ingestion_exception_user',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'key' => 'FK_E52A7DDBA76ED395',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'key' => 'FK_E52A7DDB613FECDF',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'instructor_group',
            'key' => 'FK_BF12A389C32A47EE',
            'localColumn' => 'school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'key' => 'instructor_group_x_user_ibfk_1',
            'localColumn' => 'instructor_group_id',
            'remoteTable' => 'instructor_group',
            'remoteColumn' => 'instructor_group_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'key' => 'instructor_group_x_user_ibfk_2',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'key' => 'fkey_learning_material_learning_material_status_id',
            'localColumn' => 'learning_material_status_id',
            'remoteTable' => 'learning_material_status',
            'remoteColumn' => 'learning_material_status_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'key' => 'fkey_learning_material_owning_user_id',
            'localColumn' => 'owning_user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'key' => 'FK_58CE718B7505C8EA',
            'localColumn' => 'learning_material_user_role_id',
            'remoteTable' => 'learning_material_user_role',
            'remoteColumn' => 'learning_material_user_role_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'key' => 'FK_100AC50FE34D9FF5',
            'localColumn' => 'mesh_concept_uid',
            'remoteTable' => 'mesh_concept',
            'remoteColumn' => 'mesh_concept_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'key' => 'FK_100AC50F17293A95',
            'localColumn' => 'mesh_term_uid',
            'remoteTable' => 'mesh_term',
            'remoteColumn' => 'mesh_term_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'key' => 'FK_1AF85275E34D9FF5',
            'localColumn' => 'mesh_concept_uid',
            'remoteTable' => 'mesh_concept',
            'remoteColumn' => 'mesh_concept_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'key' => 'FK_1AF85275CDB3C93B',
            'localColumn' => 'mesh_descriptor_uid',
            'remoteTable' => 'mesh_descriptor',
            'remoteColumn' => 'mesh_descriptor_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_previous_indexing',
            'key' => 'FK_32B6E2F4CDB3C93B',
            'localColumn' => 'mesh_descriptor_uid',
            'remoteTable' => 'mesh_descriptor',
            'remoteColumn' => 'mesh_descriptor_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'key' => 'FK_FC5A6AD763490620',
            'localColumn' => 'mesh_qualifier_uid',
            'remoteTable' => 'mesh_qualifier',
            'remoteColumn' => 'mesh_qualifier_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'key' => 'FK_FC5A6AD7CDB3C93B',
            'localColumn' => 'mesh_descriptor_uid',
            'remoteTable' => 'mesh_descriptor',
            'remoteColumn' => 'mesh_descriptor_uid',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'key' => 'fkey_mesh_user_select_descriptor',
            'localColumn' => 'mesh_descriptor_uid',
            'remoteTable' => 'mesh_descriptor',
            'remoteColumn' => 'mesh_descriptor_uid',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'objective',
            'key' => 'fkey_objective_competency',
            'localColumn' => 'competency_id',
            'remoteTable' => 'competency',
            'remoteColumn' => 'competency_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'key' => 'fkey_objective_x_mesh_objective_id',
            'localColumn' => 'objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
           'table' => 'objective_x_mesh',
           'key' => 'FK_936D6674CDB3C93B',
           'localColumn' => 'mesh_descriptor_uid',
           'remoteTable' => 'mesh_descriptor',
           'remoteColumn' => 'mesh_descriptor_uid',
           'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'key' => 'fkey_objective_x_objective_objective_id',
            'localColumn' => 'objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'key' => 'fkey_objective_x_objective_parent_objective_id',
            'localColumn' => 'parent_objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering',
            'key' => 'offering_ibfk_1',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => false
        );
        //disabled while we investigate why offerings exist with no publish events
        $changes[] = array(
            'table' => 'offering',
            'key' => 'fkey_offering_publish_event_id',
            'localColumn' => 'publish_event_id',
            'remoteTable' => 'publish_event',
            'remoteColumn' => 'publish_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'key' => 'fkey_offering_x_group_group_id',
            'localColumn' => 'group_id',
            'remoteTable' => 'group',
            'remoteColumn' => 'group_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'key' => 'fkey_offering_x_group_offering_id',
            'localColumn' => 'offering_id',
            'remoteTable' => 'offering',
            'remoteColumn' => 'offering_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'key' => 'fkey_offering_x_instructor_offering_id',
            'localColumn' => 'offering_id',
            'remoteTable' => 'offering',
            'remoteColumn' => 'offering_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'key' => 'fkey_offering_x_instructor_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'key' => 'fkey_offering_x_instructor_group_instructor_group_id',
            'localColumn' => 'instructor_group_id',
            'remoteTable' => 'instructor_group',
            'remoteColumn' => 'instructor_group_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'key' => 'fkey_offering_x_instructor_group_offering_id',
            'localColumn' => 'offering_id',
            'remoteTable' => 'offering',
            'remoteColumn' => 'offering_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'key' => 'fkey_offering_x_learner_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'key' => 'fkey_offering_x_learner_offering_id',
            'localColumn' => 'offering_id',
            'remoteTable' => 'offering',
            'remoteColumn' => 'offering_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'key' => 'FK_D6FB967C8EDF74F0',
            'localColumn' => 'offering_id',
            'remoteTable' => 'offering',
            'remoteColumn' => 'offering_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'key' => 'FK_D6FB967CE54B259A',
            'localColumn' => 'recurring_event_id',
            'remoteTable' => 'recurring_event',
            'remoteColumn' => 'recurring_event_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'permission',
            'key' => 'FK_E04992AAA76ED395',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program',
            'key' => 'fkey_program_publish_event_id',
            'localColumn' => 'publish_event_id',
            'remoteTable' => 'publish_event',
            'remoteColumn' => 'publish_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program',
            'key' => 'fkey_program_owning_school_id',
            'localColumn' => 'owning_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program_year',
            'key' => 'fkey_program_year_publish_event_id',
            'localColumn' => 'publish_event_id',
            'remoteTable' => 'publish_event',
            'remoteColumn' => 'publish_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program_year',
            'key' => 'fkey_program_year_program_id',
            'localColumn' => 'program_id',
            'remoteTable' => 'program',
            'remoteColumn' => 'program_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'key' => 'fkey_program_year_director_user',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'key' => 'fkey_program_year_director_program_year',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_program_year',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_department',
            'localColumn' => 'department_id',
            'remoteTable' => 'department',
            'remoteColumn' => 'department_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'key' => 'fkey_program_year_steward_school',
            'localColumn' => 'school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'key' => 'fkey_program_year_x_competency_prg_yr_id',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'key' => 'fkey_program_year_x_competency_competency_id',
            'localColumn' => 'competency_id',
            'remoteTable' => 'competency',
            'remoteColumn' => 'competency_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'key' => 'fkey_program_year_x_discipline_prg_yr_id',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'key' => 'fkey_program_year_x_discipline_discipline_id',
            'localColumn' => 'discipline_id',
            'remoteTable' => 'discipline',
            'remoteColumn' => 'discipline_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'key' => 'fkey_program_year_x_objective_prg_yr_id',
            'localColumn' => 'program_year_id',
            'remoteTable' => 'program_year',
            'remoteColumn' => 'program_year_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'key' => 'fkey_program_year_x_objective_obj_id',
            'localColumn' => 'objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'publish_event',
            'key' => 'fkey_publish_event_administrator_id',
            'localColumn' => 'administrator_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'key' => 'fkey_recurring_event_previous_recurring_event_id',
            'localColumn' => 'previous_recurring_event_id',
            'remoteTable' => 'recurring_event',
            'remoteColumn' => 'recurring_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'key' => 'fkey_recurring_event_next_recurring_event_id',
            'localColumn' => 'next_recurring_event_id',
            'remoteTable' => 'recurring_event',
            'remoteColumn' => 'recurring_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'report',
            'key' => 'fkey_report_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'report_po_value',
            'key' => 'fkey_report_po_value_report_id',
            'localColumn' => 'report_id',
            'remoteTable' => 'report',
            'remoteColumn' => 'report_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'fkey_session_publish_event_id',
            'localColumn' => 'publish_event_id',
            'remoteTable' => 'publish_event',
            'remoteColumn' => 'publish_event_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_2',
            'localColumn' => 'course_id',
            'remoteTable' => 'course',
            'remoteColumn' => 'course_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_3',
            'localColumn' => 'ilm_session_facet_id',
            'remoteTable' => 'ilm_session_facet',
            'remoteColumn' => 'ilm_session_facet_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session',
            'key' => 'session_ibfk_1',
            'localColumn' => 'session_type_id',
            'remoteTable' => 'session_type',
            'remoteColumn' => 'session_type_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_description',
            'key' => 'session_description_ibfk_1',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'key' => 'session_learning_material_ibfk_1',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'key' => 'session_learning_material_ibfk_2',
            'localColumn' => 'learning_material_id',
            'remoteTable' => 'learning_material',
            'remoteColumn' => 'learning_material_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
           'table' => 'session_learning_material_x_mesh',
           'key' => 'session_learning_material_x_mesh_fk_mesh',
           'localColumn' => 'mesh_descriptor_uid',
           'remoteTable' => 'mesh_descriptor',
           'remoteColumn' => 'mesh_descriptor_uid',
           'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'key' => 'session_learning_material_x_mesh_fk_session',
            'localColumn' => 'session_learning_material_id',
            'remoteTable' => 'session_learning_material',
            'remoteColumn' => 'session_learning_material_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_type',
            'key' => 'session_type_ibfk_1',
            'localColumn' => 'owning_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_type',
            'key' => 'assessment_option_fkey',
            'localColumn' => 'assessment_option_id',
            'remoteTable' => 'assessment_option',
            'remoteColumn' => 'assessment_option_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'key' => 'session_type_id_fkey',
            'localColumn' => 'session_type_id',
            'remoteTable' => 'session_type',
            'remoteColumn' => 'session_type_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'key' => 'aamc_method_id_fkey',
            'localColumn' => 'method_id',
            'remoteTable' => 'aamc_method',
            'remoteColumn' => 'method_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'key' => 'fkey_session_x_discipline_discipline_id',
            'localColumn' => 'discipline_id',
            'remoteTable' => 'discipline',
            'remoteColumn' => 'discipline_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'key' => 'fkey_session_x_discipline_session_id',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
           'table' => 'session_x_mesh',
           'key' => 'fkey_session_x_mesh_mesh_d',
           'localColumn' => 'mesh_descriptor_uid',
           'remoteTable' => 'mesh_descriptor',
           'remoteColumn' => 'mesh_descriptor_uid',
           'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_x_mesh',
            'key' => 'fkey_session_x_mesh_session_id',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'key' => 'fkey_session_x_objective_session_id',
            'localColumn' => 'session_id',
            'remoteTable' => 'session',
            'remoteColumn' => 'session_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'key' => 'fkey_session_x_objective_objective_id',
            'localColumn' => 'objective_id',
            'remoteTable' => 'objective',
            'remoteColumn' => 'objective_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'user',
            'key' => 'fkey_user_primary_school',
            'localColumn' => 'primary_school_id',
            'remoteTable' => 'school',
            'remoteColumn' => 'school_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'user',
            'key' => 'fkey_user_primary_cohort',
            'localColumn' => 'primary_cohort_id',
            'remoteTable' => 'cohort',
            'remoteColumn' => 'cohort_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'user_sync_exception',
            'key' => 'user_id_fkey',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'user_made_reminder',
            'key' => 'fkey_user_mode_reminder_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'key' => 'fkey_user_x_cohort_user',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'key' => 'fkey_user_x_cohort_cohort',
            'localColumn' => 'cohort_id',
            'remoteTable' => 'cohort',
            'remoteColumn' => 'cohort_id',
            'cascadeDelete' => false
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'key' => 'fkey_user_x_user_role_user_id',
            'localColumn' => 'user_id',
            'remoteTable' => 'user',
            'remoteColumn' => 'user_id',
            'cascadeDelete' => true
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'key' => 'fkey_user_x_user_role_user_role_id',
            'localColumn' => 'user_role_id',
            'remoteTable' => 'user_role',
            'remoteColumn' => 'user_role_id',
            'cascadeDelete' => true
        );

        $queries = array();
        $queries[] = "DELETE FROM objective_x_objective WHERE parent_objective_id NOT IN (select objective_id from objective)";
        $queries[] = "DELETE FROM objective_x_objective WHERE objective_id NOT IN (select objective_id from objective)";
        $queries[] = "DELETE FROM offering_x_group WHERE offering_id NOT IN (SELECT offering_id FROM offering)";
        $queries[] = "DELETE FROM offering_x_instructor WHERE offering_id NOT IN (SELECT offering_id FROM offering)";
        $queries[] = "DELETE FROM offering_x_learner WHERE offering_id NOT IN (SELECT offering_id FROM offering)";
        $queries[] = "DELETE FROM offering_x_recurring_event WHERE offering_id NOT IN (SELECT offering_id FROM offering)";
        $queries[] = "DELETE FROM program_year_steward WHERE program_year_id NOT IN (select program_year_id from program_year)";
        $queries[] = "DELETE FROM program_year_steward WHERE school_id NOT IN (select school_id from school)";
        $queries[] = "DELETE FROM program_year_steward WHERE department_id IS NOT NULL and department_id NOT IN (select department_id from department)";
        $queries[] = "UPDATE learning_material SET learning_material_status_id=1 WHERE learning_material_status_id NOT IN (SELECT learning_material_status_id FROM learning_material_status)";
        $queries[] = "UPDATE learning_material SET learning_material_user_role_id=3 WHERE learning_material_user_role_id NOT IN (SELECT learning_material_user_role_id FROM learning_material_user_role)";
        $queries[] = "UPDATE publish_event SET administrator_id=(SELECT user_id FROM user ORDER BY user_id ASC LIMIT 1) WHERE administrator_id NOT IN (SELECT user_id FROM user)";
        $queries[] = "UPDATE offering SET publish_event_id=(SELECT publish_event_id FROM session WHERE session_id = offering.session_id) WHERE publish_event_id NOT IN (SELECT publish_event_id FROM publish_event)";

        foreach ($changes as $arr) {
            $query = "ALTER TABLE `{$arr['table']}` ADD CONSTRAINT {$arr['key']} FOREIGN KEY (`{$arr['localColumn']}`) REFERENCES `{$arr['remoteTable']}` (`{$arr['remoteColumn']}`)";
            if ($arr['cascadeDelete']) {
                $query .= ' ON DELETE CASCADE';
            }
            $queries[] = $query;
        }
        return $queries;
    }

    protected function getAddIndexes()
    {
        $changes = array();
        $changes[] = array(
            'table' => 'alert_change',
            'index' => 'IDX_77659F2793035F72',
            'column' => 'alert_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'alert_change',
            'index' => 'IDX_77659F2748FCA242',
            'column' => 'alert_change_type_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'index' => 'IDX_69CBD53C93035F72',
            'column' => 'alert_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'alert_instigator',
            'index' => 'IDX_69CBD53CA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'alert_recipient',
            'index' => 'IDX_D97AE69DC32A47EE',
            'column' => 'school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'api_key',
            'index' => 'UNIQ_C912ED9DA76ED395',
            'column' => 'user_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'api_key',
            'index' => 'api_key_api_key',
            'column' => 'api_key',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'audit_atom',
            'index' => 'idx_audit_atom_created_at',
            'column' => 'created_at',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'authentication',
            'index' => 'UNIQ_FEB4C9FDFC7885D4',
            'column' => 'eppn',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'authentication',
            'index' => 'UNIQ_FEB4C9FD217BBB47',
            'column' => 'person_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'authentication',
            'index' => 'UNIQ_FEB4C9FDF85E0677',
            'column' => 'username',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'competency',
            'index' => 'IDX_80D53430DDDDCC69',
            'column' => 'owning_school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'competency_x_aamc_pcrs',
            'index' => 'IDX_1683F4A79CFCD25E',
            'column' => 'pcrs_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'cohort',
            'index' => 'UNIQ_D3B8C16BCB2B0673',
            'column' => 'program_year_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'course',
            'index' => 'IDX_169E6FB9DDDDCC69',
            'column' => 'owning_school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course',
            'index' => 'IDX_169E6FB956C92BE0',
            'column' => 'publish_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course',
            'index' => 'external_id',
            'column' => 'external_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_director',
            'index' => 'IDX_B724BEA6A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'index' => 'IDX_F841D788591CC992',
            'column' => 'course_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'index' => 'IDX_476BB36FCDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_x_cohort',
            'index' => 'IDX_4C4C18C35983C93',
            'column' => 'cohort_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_x_discipline',
            'index' => 'IDX_A52BE633A5522701',
            'column' => 'discipline_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_x_objective',
            'index' => 'IDX_4C880AE473484933',
            'column' => 'objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'index' => 'IDX_82E35212591CC992',
            'column' => 'course_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_x_mesh',
            'index' => 'IDX_82E35212CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material',
            'index' => 'IDX_F841D788C1D99609',
            'column' => 'learning_material_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'course_learning_material_x_mesh',
            'index' => 'IDX_476BB36F46C5AD2E',
            'column' => 'course_learning_material_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_export',
            'index' => 'UNIQ_E892E88E4BD2A4C0',
            'column' => 'report_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence',
            'index' => 'UNIQ_B8AE58F54BD2A4C0',
            'column' => 'report_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'IDX_22E6B6806081C3B0',
            'column' => 'academic_level_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'IDX_22E6B680591CC992',
            'column' => 'course_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'IDX_22E6B680DEB52F47',
            'column' => 'parent_sequence_block_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'curriculum_inventory_sequence_block',
            'index' => 'IDX_22E6B6804BD2A4C0',
            'column' => 'report_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'department',
            'index' => 'IDX_CD1DE18AC32A47EE',
            'column' => 'school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'discipline',
            'index' => 'IDX_75BEEE3FDDDDCC69',
            'column' => 'owning_school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'group',
            'index' => 'IDX_6DC044C561997596',
            'column' => 'parent_group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'group',
            'index' => 'IDX_6DC044C535983C93',
            'column' => 'cohort_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'group_x_instructor_group',
            'index' => 'IDX_49AFEA21FE367BE2',
            'column' => 'instructor_group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'group_x_user',
            'index' => 'IDX_93A1A790A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'group_x_instructor',
            'index' => 'IDX_8CE57915A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'index' => 'IDX_6423CE8CFE367BE2',
            'column' => 'instructor_group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'index' => 'IDX_82B9A47B504270C1',
            'column' => 'ilm_session_facet_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor_group',
            'index' => 'IDX_8171A2F3FE367BE2',
            'column' => 'instructor_group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_instructor',
            'index' => 'IDX_82B9A47BA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'index' => 'IDX_E385BC58504270C1',
            'column' => 'ilm_session_facet_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_learner',
            'index' => 'IDX_E385BC58A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'ilm_session_facet_x_group',
            'index' => 'IDX_B43B41DCFE54D947',
            'column' => 'group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'instructor_group_x_user',
            'index' => 'IDX_6423CE8CA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'index' => 'IDX_E52A7DDBA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'instruction_hours',
            'index' => 'IDX_E52A7DDB613FECDF',
            'column' => 'session_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'instructor_group',
            'index' => 'IDX_BF12A389C32A47EE',
            'column' => 'school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'index' => 'IDX_58CE718BA0407615',
            'column' => 'learning_material_status_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'index' => 'IDX_58CE718B67A71A40',
            'column' => 'owning_user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'learning_material',
            'index' => 'IDX_58CE718B7505C8EA',
            'column' => 'learning_material_user_role_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_previous_indexing',
            'index' => 'UNIQ_32B6E2F4CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'index' => 'IDX_100AC50FE34D9FF5',
            'column' => 'mesh_concept_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_concept_x_term',
            'index' => 'IDX_100AC50F17293A95',
            'column' => 'mesh_term_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'index' => 'IDX_1AF85275E34D9FF5',
            'column' => 'mesh_concept_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_concept',
            'index' => 'IDX_1AF85275CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'index' => 'IDX_FC5A6AD763490620',
            'column' => 'mesh_qualifier_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'mesh_descriptor_x_qualifier',
            'index' => 'IDX_FC5A6AD7CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        // $changes[] = array(
        //     'table' => 'mesh_term',
        //     'index' => 'UNIQ_D401FB885E237E06',
        //     'column' => 'name',
        //     'unique' => true
        // );
        $changes[] = array(
            'table' => 'mesh_user_selection',
            'index' => 'IDX_650D32BFCDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'objective',
            'index' => 'IDX_B996F101FB9F58C',
            'column' => 'competency_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'index' => 'IDX_9DC1F26573484933',
            'column' => 'objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'index' => 'IDX_936D667473484933',
            'column' => 'objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'objective_x_mesh',
            'index' => 'IDX_936D6674CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'objective_x_objective',
            'index' => 'IDX_9DC1F2652326141D',
            'column' => 'parent_objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering',
            'index' => 'IDX_A5682AB156C92BE0',
            'column' => 'publish_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'index' => 'IDX_4D68848F8EDF74F0',
            'column' => 'offering_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_group',
            'index' => 'IDX_4D68848FFE54D947',
            'column' => 'group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor',
            'index' => 'IDX_171DC549A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'index' => 'IDX_5540AEE1FE367BE2',
            'column' => 'instructor_group_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_instructor_group',
            'index' => 'IDX_5540AEE18EDF74F0',
            'column' => 'offering_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'index' => 'IDX_991D7DA38EDF74F0',
            'column' => 'offering_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_learner',
            'index' => 'IDX_991D7DA3A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'index' => 'IDX_D6FB967CE54B259A',
            'column' => 'recurring_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'offering_x_recurring_event',
            'index' => 'IDX_D6FB967C8EDF74F0',
            'column' => 'offering_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'permission',
            'index' => 'IDX_E04992AAA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program',
            'index' => 'IDX_92ED778456C92BE0',
            'column' => 'publish_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program',
            'index' => 'IDX_92ED7784DDDDCC69',
            'column' => 'owning_school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year',
            'index' => 'IDX_B664263056C92BE0',
            'column' => 'publish_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year',
            'index' => 'IDX_B66426303EB8070A',
            'column' => 'program_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_director',
            'index' => 'IDX_9212A50DA76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'index' => 'IDX_program_year_school',
            'column' => 'program_year_id, school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'index' => 'IDX_38AC2B7BAE80F5DF',
            'column' => 'department_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'index' => 'IDX_38AC2B7BCB2B0673',
            'column' => 'program_year_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'index' => 'IDX_38AC2B7BC32A47EE',
            'column' => 'school_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_steward',
            'index' => 'program_year_id_school_id_department_id',
            'column' => 'program_year_id, school_id, department_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'program_year_x_competency',
            'index' => 'IDX_1841AB9BFB9F58C',
            'column' => 'competency_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_x_objective',
            'index' => 'IDX_FF29E64373484933',
            'column' => 'objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'program_year_x_discipline',
            'index' => 'IDX_ED2A7194A5522701',
            'column' => 'discipline_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'publish_event',
            'index' => 'IDX_A018E3734B09E92C',
            'column' => 'administrator_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'report',
            'index' => 'IDX_C42F7784A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'report_po_value',
            'index' => 'UNIQ_9261FE834BD2A4C0',
            'column' => 'report_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'index' => 'IDX_51B1C7F8B494B099',
            'column' => 'previous_recurring_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'recurring_event',
            'index' => 'IDX_51B1C7F882312E9D',
            'column' => 'next_recurring_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session',
            'index' => 'UNIQ_D044D5D4504270C1',
            'column' => 'ilm_session_facet_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'session',
            'index' => 'IDX_D044D5D456C92BE0',
            'column' => 'publish_event_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_description',
            'index' => 'UNIQ_91BD5E51613FECDF',
            'column' => 'session_id',
            'unique' => true
        );
        $changes[] = array(
            'table' => 'session_learning_material',
            'index' => 'IDX_9BE2AF8D613FECDF',
            'column' => 'session_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'index' => 'IDX_EC36AECFCDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_learning_material_x_mesh',
            'index' => 'IDX_EC36AECFE8376E0A',
            'column' => 'session_learning_material_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_type',
            'index' => 'assessment_option_fkey',
            'column' => 'assessment_option_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'index' => 'IDX_5E10F74819883967',
            'column' => 'method_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_type_x_aamc_method',
            'index' => 'IDX_5E10F748D7940EC9',
            'column' => 'session_type_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_x_mesh',
            'index' => 'IDX_43B09906613FECDF',
            'column' => 'session_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_x_mesh',
            'index' => 'IDX_43B09906CDB3C93B',
            'column' => 'mesh_descriptor_uid',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_x_objective',
            'index' => 'IDX_C4BF244773484933',
            'column' => 'objective_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'session_x_discipline',
            'index' => 'IDX_EA7C234FA5522701',
            'column' => 'discipline_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'user',
            'index' => 'IDX_8D93D649FF038174',
            'column' => 'primary_cohort_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'user_made_reminder',
            'index' => 'IDX_44EF4595A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'user_x_cohort',
            'index' => 'IDX_4DFD48DA35983C93',
            'column' => 'cohort_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'index' => 'IDX_583C407A76ED395',
            'column' => 'user_id',
            'unique' => false
        );
        $changes[] = array(
            'table' => 'user_x_user_role',
            'index' => 'IDX_583C4078E0E3CA6',
            'column' => 'user_role_id',
            'unique' => false
        );

        $queries = array();
        foreach ($changes as $arr) {
            $unique = $arr['unique']?'UNIQUE ':'';
            $queries[] = "CREATE {$unique}INDEX {$arr['index']} ON `{$arr['table']}` ({$arr['column']})";
        }
        return $queries;
    }

    protected function getAddPrimaryKeys()
    {
        $queries = array();
        $arr = array(
            'report_po_value' => 'report_id',
            'program_year_x_objective' => 'program_year_id, objective_id',
            'program_year_x_discipline' => 'program_year_id, discipline_id',
            'program_year_x_competency' => 'program_year_id, competency_id',
            'offering_x_recurring_event' => 'offering_id, recurring_event_id',
            'instructor_group_x_user' => 'instructor_group_id, user_id',
            'group_x_group' => 'group_a_id, group_b_id',
            'course_learning_material_x_mesh' => 'course_learning_material_id, mesh_descriptor_uid',
            'course_x_objective' => 'course_id, objective_id',
            'course_x_cohort' => 'course_id, cohort_id',
            'alert_change' => 'alert_id, alert_change_type_id',
            'alert_recipient' => 'alert_id, school_id',
            'session_type_x_aamc_method' => 'session_type_id, method_id',
            'session_learning_material_x_mesh' => 'session_learning_material_id, mesh_descriptor_uid',
            'session_description' => 'session_id',
        );

        foreach ($arr as $table => $key) {
            $queries[] = "ALTER TABLE `{$table}` ADD PRIMARY KEY ({$key})";
        }
        $conflictingPKs = array(
            array(
                'table' => 'alert_instigator',
                'col1' => 'alert_id',
                'col2' => 'user_id'
            ),
            array(
                'table' => 'course_x_discipline',
                'col1' => 'course_id',
                'col2' => 'discipline_id'
            ),
            array(
                'table' => 'course_x_mesh',
                'col1' => 'course_id',
                'col2' => 'mesh_descriptor_uid'
            ),
            array(
                'table' => 'mesh_concept_x_semantic_type',
                'col1' => 'mesh_concept_uid',
                'col2' => 'mesh_semantic_type_uid'
            ),
            array(
                'table' => 'mesh_concept_x_term',
                'col1' => 'mesh_concept_uid',
                'col2' => 'mesh_term_uid'
            ),
            array(
                'table' => 'mesh_descriptor_x_concept',
                'col1' => 'mesh_concept_uid',
                'col2' => 'mesh_descriptor_uid'
            ),
            array(
                'table' => 'mesh_descriptor_x_qualifier',
                'col1' => 'mesh_qualifier_uid',
                'col2' => 'mesh_descriptor_uid'
            ),
            array(
                'table' => 'objective_x_objective',
                'col1' => 'objective_id',
                'col2' => 'parent_objective_id'
            ),
            array(
                'table' => 'objective_x_mesh',
                'col1' => 'objective_id',
                'col2' => 'mesh_descriptor_uid'
            )
        );
        foreach ($conflictingPKs as $arr) {
            $queries[] = "CREATE INDEX tmpidx_{$arr['col1']} ON `{$arr['table']}` ({$arr['col1']})";
            $queries[] = "CREATE INDEX tmpidx_{$arr['col2']} ON `{$arr['table']}` ({$arr['col2']})";
            $queries[] ="ALTER TABLE `{$arr['table']}` "
            . 'ADD COLUMN `id` '
            . 'BIGINT PRIMARY KEY AUTO_INCREMENT FIRST';
            //then delete duplicates
            $queries[] = "DELETE t1 FROM `{$arr['table']}` t1, `{$arr['table']}` t2 "
            . "WHERE t1.id > t2.id AND t1.`{$arr['col1']}` = t2.`{$arr['col1']}` "
            . "AND t1.`{$arr['col2']}` = t2.`{$arr['col2']}`";
            $queries[] = "ALTER TABLE `{$arr['table']}` DROP COLUMN `id`";
            $queries[] = "ALTER TABLE `{$arr['table']}` ADD PRIMARY KEY (`{$arr['col1']}`, `{$arr['col2']}`)";
            $queries[] = "DROP INDEX tmpidx_{$arr['col1']} ON `{$arr['table']}`";
            $queries[] = "DROP INDEX tmpidx_{$arr['col2']} ON `{$arr['table']}`";
        }

        return $queries;
    }

    protected function getChangeEngine()
    {
        $arr = array(
            'mesh_concept',
            'mesh_concept_x_semantic_type',
            'mesh_concept_x_term',
            'mesh_descriptor',
            'mesh_descriptor_x_concept',
            'mesh_descriptor_x_qualifier',
            'mesh_previous_indexing',
            'mesh_qualifier',
            'mesh_semantic_type',
            'mesh_term',
            'mesh_tree_x_descriptor',
            'mesh_user_selection'
        );

        $queries = array();
        foreach ($arr as $table) {
            $queries[] = "ALTER TABLE `{$table}` ENGINE=InnoDB";
        }
        return $queries;
    }

    protected function getChangeCharset()
    {
        $arr = array(
            'mesh_user_selection' => 'utf8 COLLATE utf8_unicode_ci',
            'instructor_group_x_user' => 'utf8 COLLATE utf8_unicode_ci'
        );

        $queries = array();
        foreach ($arr as $table => $charset) {
            $queries[] = "ALTER TABLE `{$table}` DEFAULT CHARACTER SET {$charset}";
            $queries[] = "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET {$charset}";
        }

        return $queries;
    }

    protected function getDropTables()
    {
        $arr = array(
            'group_default_instructor',
            'curriculum_inventory_program',
            'ilm_session_facet_learner',
            'ilm_session_facet_instructor',
            'offering_instructor',
            'offering_learner',
        );

        $queries = array();
        foreach ($arr as $table) {
            $queries[] = "DROP TABLE IF EXISTS `{$table}`";
        }

        return $queries;
    }

    protected function getDropColumns()
    {
        $arr = [];
        $arr[] = array(
            'table' => 'user_x_cohort',
            'column' => 'is_primary'
        );

        $queries = [];
        foreach ($arr as $arr) {
            $queries[] = "ALTER TABLE `{$arr['table']}` DROP COLUMN `{$arr['column']}`";
        }

        return $queries;
    }
}
