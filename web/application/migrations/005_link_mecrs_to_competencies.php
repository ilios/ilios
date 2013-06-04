<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Links MECRS to Ilios competencies.
 * Note that this only applies to the default competencies as defined in the data population for School of Medicine.
 * @see database/install/data_population/SOM_competency_data.sql
 */
class Migration_link_mecrs_to_competencies extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 7, 'aamc-mecrs-comp-c0102' FROM competency
    WHERE competency_id = 7 AND title = 'History Taking'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 8, 'aamc-mecrs-comp-c0103' FROM competency
    WHERE competency_id = 8 AND title = 'Physical Exam'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 9, 'aamc-mecrs-comp-c0107' FROM competency
    WHERE competency_id = 9 AND title = 'Oral Case Presentation'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 10, 'aamc-mecrs-comp-c0405' FROM competency
    WHERE competency_id = 10 AND title = 'Medical Notes'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 11, 'aamc-mecrs-comp-c0102' FROM competency
    WHERE competency_id = 11 AND title = 'Procedures and Skills'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 12, 'aamc-mecrs-comp-c0101' FROM competency
    WHERE competency_id = 12 AND title = 'Patient Management'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 12, 'aamc-mecrs-comp-c0108' FROM competency
    WHERE competency_id = 12 AND title = 'Patient Management'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 12, 'aamc-mecrs-comp-c0109' FROM competency
    WHERE competency_id = 12 AND title = 'Patient Management'
    AND parent_competency_id = 1 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 13, 'aamc-mecrs-comp-c0203' FROM competency
    WHERE competency_id = 13 AND title = 'Problem-Solving and Diagnosis'
    AND parent_competency_id = 2 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 14, 'aamc-mecrs-comp-c0201' FROM competency
    WHERE competency_id = 14 AND title = 'Knowledge for Practice'
    AND parent_competency_id = 2 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 14, 'aamc-mecrs-comp-c0310' FROM competency
    WHERE competency_id = 14 AND title = 'Knowledge for Practice'
    AND parent_competency_id = 2 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 15, 'aamc-mecrs-comp-c0305' FROM competency
    WHERE competency_id = 15 AND title = 'Information Management'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 15, 'aamc-mecrs-comp-c0309' FROM competency
    WHERE competency_id = 15 AND title = 'Information Management'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 16, 'aamc-mecrs-comp-c0202' FROM competency
    WHERE competency_id = 16 AND title = 'Evidence-Based Medicine'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 16, 'aamc-mecrs-comp-c0204' FROM competency
    WHERE competency_id = 16 AND title = 'Evidence-Based Medicine'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 16, 'aamc-mecrs-comp-c0306' FROM competency
    WHERE competency_id = 16 AND title = 'Evidence-Based Medicine'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 17, 'aamc-mecrs-comp-c0301' FROM competency
    WHERE competency_id = 17 AND title = 'Reflection and Self-Improvement'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 17, 'aamc-mecrs-comp-c0307' FROM competency
    WHERE competency_id = 17 AND title = 'Reflection and Self-Improvement'
    AND parent_competency_id = 3 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 18, 'aamc-mecrs-comp-c0105' FROM competency
    WHERE competency_id = 18 AND title = 'Doctor-Patient Relationship'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 19, 'aamc-mecrs-comp-c0105' FROM competency
    WHERE competency_id = 19 AND title = 'Communication and Information Sharing with Patients and Families'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 19, 'aamc-mecrs-comp-c0401' FROM competency
    WHERE competency_id = 19 AND title = 'Communication and Information Sharing with Patients and Families'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 19, 'aamc-mecrs-comp-c0406' FROM competency
    WHERE competency_id = 19 AND title = 'Communication and Information Sharing with Patients and Families'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 20, 'aamc-mecrs-comp-c0402' FROM competency
    WHERE competency_id = 20 AND title = 'Communication with the Medical Team'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 20, 'aamc-mecrs-comp-c0405' FROM competency
    WHERE competency_id = 20 AND title = 'Communication with the Medical Team'
    AND parent_competency_id = 4 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 21, 'aamc-mecrs-comp-c0501' FROM competency
    WHERE competency_id = 21 AND title = 'Professional Relationships'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 21, 'aamc-mecrs-comp-c0505' FROM competency
    WHERE competency_id = 21 AND title = 'Professional Relationships'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 22, 'aamc-mecrs-comp-c0502' FROM competency
    WHERE competency_id = 22 AND title = 'Boundaries and Priorities'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 23, 'aamc-mecrs-comp-c0504' FROM competency
    WHERE competency_id = 23 AND title = 'Work Habits, Appearance, and Etiquette'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 24, 'aamc-mecrs-comp-c0506' FROM competency
    WHERE competency_id = 24 AND title = 'Ethical Principles'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 25, 'aamc-mecrs-comp-c0506' FROM competency
    WHERE competency_id = 25 AND title = 'Institutional, Regulatory, and Professional Society Standards'
    AND parent_competency_id = 5 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 26, 'aamc-mecrs-comp-c0403' FROM competency
    WHERE competency_id = 26 AND title = 'Healthcare Delivery Systems'
    AND parent_competency_id = 6 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 26, 'aamc-mecrs-comp-c0603' FROM competency
    WHERE competency_id = 26 AND title = 'Healthcare Delivery Systems'
    AND parent_competency_id = 6 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 50, 'aamc-mecrs-comp-c0606' FROM competency
    WHERE competency_id = 50 AND title = 'Systems Improvement'
    AND parent_competency_id = 6 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 51, 'aamc-mecrs-comp-c0203' FROM competency
    WHERE competency_id = 51 AND title = 'Treatment'
    AND parent_competency_id = 2 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `competency_x_aamc_mecrs` (`competency_id`, `mecrs_id`)
(
    SELECT 52, 'aamc-mecrs-comp-c0206' FROM competency
    WHERE competency_id = 52 AND title = 'Inquiry and Discovery'
    AND parent_competency_id = 2 AND owning_school_id = 1
)
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }

    /**
      * @see CI_Migration::down()
      */
    public function down ()
    {
        $this->db->trans_start();
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 7 AND `mecrs_id` = 'aamc-mecrs-comp-c0102'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 8 AND `mecrs_id` = 'aamc-mecrs-comp-c0103'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 9 AND `mecrs_id` = 'aamc-mecrs-comp-c0107'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 10 AND `mecrs_id` = 'aamc-mecrs-comp-c0405'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 11 AND `mecrs_id` = 'aamc-mecrs-comp-c0102'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 12 AND `mecrs_id` = 'aamc-mecrs-comp-c0101'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 12 AND `mecrs_id` = 'aamc-mecrs-comp-c0108'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 12 AND `mecrs_id` = 'aamc-mecrs-comp-c0109'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 13 AND `mecrs_id` = 'aamc-mecrs-comp-c0203'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 14 AND `mecrs_id` = 'aamc-mecrs-comp-c0201'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 14 AND `mecrs_id` = 'aamc-mecrs-comp-c0310'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 15 AND `mecrs_id` = 'aamc-mecrs-comp-c0305'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 15 AND `mecrs_id` = 'aamc-mecrs-comp-c0309'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 16 AND `mecrs_id` = 'aamc-mecrs-comp-c0202'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 16 AND `mecrs_id` = 'aamc-mecrs-comp-c0204'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 16 AND `mecrs_id` = 'aamc-mecrs-comp-c0306'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 17 AND `mecrs_id` = 'aamc-mecrs-comp-c0301'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 17 AND `mecrs_id` = 'aamc-mecrs-comp-c0307'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 18 AND `mecrs_id` = 'aamc-mecrs-comp-c0105'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 19 AND `mecrs_id` = 'aamc-mecrs-comp-c0105'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 19 AND `mecrs_id` = 'aamc-mecrs-comp-c0401'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 19 AND `mecrs_id` = 'aamc-mecrs-comp-c0406'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 20 AND `mecrs_id` = 'aamc-mecrs-comp-c0402'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 20 AND `mecrs_id` = 'aamc-mecrs-comp-c0405'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 21 AND `mecrs_id` = 'aamc-mecrs-comp-c0501'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 21 AND `mecrs_id` = 'aamc-mecrs-comp-c0505'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 22 AND `mecrs_id` = 'aamc-mecrs-comp-c0502'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 23 AND `mecrs_id` = 'aamc-mecrs-comp-c0504'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 24 AND `mecrs_id` = 'aamc-mecrs-comp-c0506'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 25 AND `mecrs_id` = 'aamc-mecrs-comp-c0506'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 26 AND `mecrs_id` = 'aamc-mecrs-comp-c0403'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 26 AND `mecrs_id` = 'aamc-mecrs-comp-c0603'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 50 AND `mecrs_id` = 'aamc-mecrs-comp-c0606'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 51 AND `mecrs_id` = 'aamc-mecrs-comp-c0203'");
        $this->db->query("DELETE FROM `competency_x_aamc_mecrs` WHERE `competency_id` = 52 AND `mecrs_id` = 'aamc-mecrs-comp-c0206'");
        $this->db->trans_complete();
    }
}
