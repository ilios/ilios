<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Sets up the tables needed for mapping out the curriculum inventory according to the AAMC standard.
 */
class Migration_Setup_curriculum_inventory_tables extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_institution` (
    `school_id` INT(10) UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `aamc_code` VARCHAR(10) NOT NULL,
    `address_street` VARCHAR(100) NOT NULL,
    `address_city` VARCHAR(100) NOT NULL,
    `address_state_or_province` VARCHAR(50) NOT NULL,
    `address_zipcode` VARCHAR(10) NOT NULL,
    `address_country_code` CHAR(2) NOT NULL,
    PRIMARY KEY (`school_id`),
    CONSTRAINT `fkey_curriculum_inventory_institution_school_id`
       FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`)
       ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_report` (
    `report_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_id` INT(10) UNSIGNED NOT NULL,
    `year` SMALLINT(4) UNSIGNED NOT NULL,
    `name` VARCHAR(200) NULL DEFAULT NULL,
    `description` TEXT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    PRIMARY KEY (`report_id`),
    UNIQUE INDEX `program_id_year` (`program_id`, `year`),
    CONSTRAINT `fkey_curriculum_inventory_report_program_id`
        FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_sequence` (
    `report_id` INT(10) UNSIGNED NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`report_id`),
    CONSTRAINT `fkey_curriculum_inventory_sequence_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_academic_level` (
    `academic_level_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_id` INT(10) UNSIGNED NOT NULL,
    `level` INT(2) UNSIGNED NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`academic_level_id`),
    UNIQUE INDEX `report_id_level` (`report_id`, `level`),
    CONSTRAINT `fkey_curriculum_inventory_academic_level_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_sequence_block` (
    `sequence_block_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `report_id` INT(10) UNSIGNED NOT NULL,
    `required` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `child_sequence_order` TINYINT UNSIGNED NOT NULL DEFAULT '0',
    `order_in_sequence` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `minimum` INT(11) NOT NULL DEFAULT '-1',
    `maximum` INT(11) NOT NULL DEFAULT '-1',
    `track` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    `description` TEXT NULL,
    `title` VARCHAR(200) NOT NULL,
    `start_date` DATE NULL DEFAULT NULL,
    `end_date` DATE NULL DEFAULT NULL,
    `academic_level_id` INT(10) UNSIGNED NOT NULL,
    `duration` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    `course_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    `parent_sequence_block_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`sequence_block_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_report_id` (`report_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_course_id` (`course_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_parent_id` (`parent_sequence_block_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_academic_level_id` (`academic_level_id`),
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_academic_level_id`
        FOREIGN KEY (`academic_level_id`) REFERENCES `curriculum_inventory_academic_level` (`academic_level_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_course_id`
        FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_parent_id`
        FOREIGN KEY (`parent_sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_export` (
    `report_id` INT(10) UNSIGNED NOT NULL,
    `document` MEDIUMTEXT NOT NULL,
    `created_by` INT(10) UNSIGNED NOT NULL,
    `created_on` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`report_id`),
    CONSTRAINT `fkey_curriculum_inventory_export_report_id`
        FOREIGN KEY (`report_id`) REFERENCES `curriculum_inventory_report` (`report_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_curriculum_inventory_export_user_id`
        FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
        ON UPDATE RESTRICT ON DELETE NO ACTION
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `aamc_pcrs` (
    `pcrs_id` VARCHAR(21) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`pcrs_id`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `aamc_method` (
    `method_id` VARCHAR(10) NOT NULL,
    `description` TEXT NOT NULL,
    PRIMARY KEY (`method_id`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `session_type_x_aamc_method` (
    `session_type_id` INT(14) UNSIGNED NOT NULL,
    `method_id` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`session_type_id`),
    UNIQUE INDEX `session_type_id_method_id` (`session_type_id`, `method_id`),
    INDEX `aamc_method_id_fkey` (`method_id`),
    CONSTRAINT `aamc_method_id_fkey`
        FOREIGN KEY (`method_id`) REFERENCES `aamc_method` (`method_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `session_type_id_fkey`
        FOREIGN KEY (`session_type_id`) REFERENCES `session_type` (`session_type_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `competency_x_aamc_pcrs` (
    `competency_id` INT(14) UNSIGNED NOT NULL,
    `pcrs_id` VARCHAR(21) NOT NULL,
    PRIMARY KEY (`competency_id`, `pcrs_id`),
    INDEX `aamc_pcrs_id_fkey` (`pcrs_id`),
    CONSTRAINT `aamc_pcrs_id_fkey`
        FOREIGN KEY (`pcrs_id`) REFERENCES `aamc_pcrs` (`pcrs_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `competency_id_fkey`
        FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
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
        $this->db->query('DROP TABLE `competency_x_aamc_pcrs`');
        $this->db->query('DROP TABLE `session_type_x_aamc_method`');
        $this->db->query('DROP TABLE `aamc_method`');
        $this->db->query('DROP TABLE `aamc_pcrs`');
        $this->db->query('DROP TABLE `curriculum_inventory_export`');
        $this->db->query('DROP TABLE `curriculum_inventory_sequence_block`');
        $this->db->query('DROP TABLE `curriculum_inventory_academic_level`');
        $this->db->query('DROP TABLE `curriculum_inventory_sequence`');
        $this->db->query('DROP TABLE `curriculum_inventory_report`');
        $this->db->query('DROP TABLE `curriculum_inventory_institution`');
        $this->db->trans_complete();
    }
}
