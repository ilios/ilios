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
    `aamc_id` VARCHAR(10) NOT NULL,
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
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_program` (
    `program_year_id` INT(10) UNSIGNED NOT NULL,
    `aamc_id` VARCHAR(10) NULL,
    `name` VARCHAR(200) NULL DEFAULT NULL,
    `education_program_context_id` INT UNSIGNED NULL,
    `profession_id` INT UNSIGNED NULL,
    `specialty_id` INT UNSIGNED NULL,
    `start_date` DATE NULL DEFAULT NULL,
    `end_date` DATE NULL DEFAULT NULL,
    PRIMARY KEY (`program_year_id`),
    CONSTRAINT `fkey_curriculum_inventory_program_program_year_id`
        FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_sequence` (
    `program_year_id` INT(10) UNSIGNED NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`program_year_id`),
    CONSTRAINT `fkey_curriculum_inventory_sequence_program_year_id`
        FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_academic_level` (
    `academic_level_id` INT(10) UNSIGNED NOT NULL,
    `program_year_id` INT(10) UNSIGNED NOT NULL,
    `level` INT(2) UNSIGNED NOT NULL,
    `name` VARCHAR(50) NOT NULL,
    `description` TEXT NULL,
    PRIMARY KEY (`academic_level_id`),
    UNIQUE INDEX `program_year_id_level` (`program_year_id`, `level`),
    CONSTRAINT `fkey_curriculum_inventory_academic_level_program_year_id` 
        FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `curriculum_inventory_sequence_block` (
    `sequence_block_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_year_id` INT(10) UNSIGNED NOT NULL,
    `status` TINYINT UNSIGNED NOT NULL DEFAULT '0',
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
    `duration` INT(11) NOT NULL DEFAULT '0',
    `course_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    `parent_sequence_block_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`sequence_block_id`),
    INDEX `fkey_curriculum_inventory_sequence_block_program_year_id` (`program_year_id`),
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
    CONSTRAINT `fkey_curriculum_inventory_sequence_block_program_year_id`
        FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
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
        $this->db->query("DROP TABLE `curriculum_inventory_sequence_block`");
        $this->db->query("DROP TABLE `curriculum_inventory_academic_level`");
        $this->db->query("DROP TABLE `curriculum_inventory_sequence`");
        $this->db->query("DROP TABLE `curriculum_inventory_program`");
        $this->db->query("DROP TABLE `curriculum_inventory_institution`");
        $this->db->trans_complete();
    }
}