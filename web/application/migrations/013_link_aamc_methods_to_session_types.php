<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Links AAMC methods to Ilios session types.
 * Note that this only applies to the default session types as defined in the data population for the School of Medicine.
 * @see database/install/data_population/session_type_data.sql
 */
class Migration_Link_aamc_methods_to_session_types extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM010', `session_type_id` FROM `session_type` WHERE title = 'Independent Learning' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM012', `session_type_id` FROM `session_type` WHERE title = 'Laboratory' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM013', `session_type_id` FROM `session_type` WHERE title = 'Lecture' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM028', `session_type_id` FROM `session_type` WHERE title = 'Tutorial' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM003', `session_type_id` FROM `session_type` WHERE title = 'Exam - Institutionally Developed, Clinical Performance' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM030', `session_type_id` FROM `session_type` WHERE title = 'Workshop' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM019', `session_type_id` FROM `session_type` WHERE title = 'Problem-Based Learning (PBL)' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM008', `session_type_id` FROM `session_type` WHERE title = 'Discussion, Small Group (<=12)' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM018', `session_type_id` FROM `session_type` WHERE title = 'Preceptorship' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM029', `session_type_id` FROM `session_type` WHERE title = 'Ward Rounds' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM002', `session_type_id` FROM `session_type` WHERE title = 'Clinical Experience - Ambulatory' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM003', `session_type_id` FROM `session_type` WHERE title = 'Clinical Experience - Inpatient' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM026', `session_type_id` FROM `session_type` WHERE title = 'Team-Based Learning (TBL)' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM001', `session_type_id` FROM `session_type` WHERE title = 'Case-Based Instruction/Learning' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM004', `session_type_id` FROM `session_type` WHERE title = 'Concept Mapping' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM005', `session_type_id` FROM `session_type` WHERE title = 'Conference' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM006', `session_type_id` FROM `session_type` WHERE title = 'Demonstration' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM007', `session_type_id` FROM `session_type` WHERE title = 'Discussion, Large Group (>12)' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM009', `session_type_id` FROM `session_type` WHERE title = 'Games' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM011', `session_type_id` FROM `session_type` WHERE title = 'Journal Club' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM014', `session_type_id` FROM `session_type` WHERE title = 'Mentorship' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM015', `session_type_id` FROM `session_type` WHERE title = 'Patient Presentation - Faculty' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM016', `session_type_id` FROM `session_type` WHERE title = 'Patient Presentation - Learner' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM017', `session_type_id` FROM `session_type` WHERE title = 'Peer Teaching' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM020', `session_type_id` FROM `session_type` WHERE title = 'Reflection' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM021', `session_type_id` FROM `session_type` WHERE title = 'Research' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM022', `session_type_id` FROM `session_type` WHERE title = 'Role Play/Dramatization' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM023', `session_type_id` FROM `session_type` WHERE title = 'Self-Directed Learning' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM024', `session_type_id` FROM `session_type` WHERE title = 'Service Learning Activity' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM025', `session_type_id` FROM `session_type` WHERE title = 'Simulation' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'IM027', `session_type_id` FROM `session_type` WHERE title = 'Team-Building' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM001', `session_type_id` FROM `session_type` WHERE title = 'Clinical Documentation Review' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM002', `session_type_id` FROM `session_type` WHERE title = 'Clinical Performance - Rating/Checklist' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM004', `session_type_id` FROM `session_type` WHERE title = 'Exam - Institutionally Developed, Written/Computer-based' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM005', `session_type_id` FROM `session_type` WHERE title = 'Exam - Institutionally Developed, Oral' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM006', `session_type_id` FROM `session_type` WHERE title = 'Exam - Licensure, Clinical Performance' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM007', `session_type_id` FROM `session_type` WHERE title = 'Exam - Licensure, Written/Computer-based' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM008', `session_type_id` FROM `session_type` WHERE title = 'Exam - Nationally Normed/Standardized, Subject' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM009', `session_type_id` FROM `session_type` WHERE title = 'Multisource Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM010', `session_type_id` FROM `session_type` WHERE title = 'Narrative Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM011', `session_type_id` FROM `session_type` WHERE title = 'Oral Patient Presentation' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM012', `session_type_id` FROM `session_type` WHERE title = 'Participation' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM013', `session_type_id` FROM `session_type` WHERE title = 'Peer Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM014', `session_type_id` FROM `session_type` WHERE title = 'Portfolio-Based Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM015', `session_type_id` FROM `session_type` WHERE title = 'Practical (Lab) Exam' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM016', `session_type_id` FROM `session_type` WHERE title = 'Research or Project Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM017', `session_type_id` FROM `session_type` WHERE title = 'Self-Assessment' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM018', `session_type_id` FROM `session_type` WHERE title = 'Stimulated Recall' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM001', `session_type_id` FROM `session_type` WHERE title = 'Clinical Documentation Review [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM003', `session_type_id` FROM `session_type` WHERE title = 'Exam - Institutionally Developed, Clinical Performance [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM004', `session_type_id` FROM `session_type` WHERE title = 'Exam - Institutionally Developed, Written/Computer-based [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM008', `session_type_id` FROM `session_type` WHERE title = 'Exam - Nationally Normed/Standardized, Subject [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM010', `session_type_id` FROM `session_type` WHERE title = 'Narrative Assessment [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM015', `session_type_id` FROM `session_type` WHERE title = 'Practical (Lab) Exam [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $sql = "INSERT INTO `session_type_x_aamc_method` (`method_id`, `session_type_id`) (SELECT 'AM016', `session_type_id` FROM `session_type` WHERE title = 'Research or Project Assessment [formative]' AND `owning_school_id`  = 1)";
        $this->db->query($sql);
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        $this->db->query('DELETE FROM `session_type_x_aamc_method`');
        $this->db->trans_complete();
    }
}
