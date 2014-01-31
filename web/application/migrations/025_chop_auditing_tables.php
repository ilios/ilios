<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gets rid of the unused "audit_content" table, and rolls the "audit_event" and "audit_atom" tables into one.
 *
 * ACHTUNG MINEN!
 * Running this migration breaks backwards compatibility.
 * Downgrading this migration will result in complete loss of auditing trail data.
 */
class Migration_Chop_auditing_tables extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up()
    {
        $this->db->trans_start();

        // add timestamp and creator columns to audit_atom table
        $sql =<<< EOL
ALTER TABLE `audit_atom`
    ADD COLUMN `created_by` INT(14) UNSIGNED NULL DEFAULT NULL AFTER `root_atom`,
    ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_by`
EOL;
        $this->db->query($sql);

        // added indexes and foreign keys on new columns
        $sql =<<< EOL
ALTER TABLE `audit_atom`
    ADD INDEX `idx_audit_atom_created_at` (`created_at`),
    ADD CONSTRAINT `fkey_audit_atom_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`)
EOL;
        $this->db->query($sql);

        // merge audit_event data into audit_atom table
        $sql =<<< EOL
UPDATE `audit_atom`, `audit_event`
SET `audit_atom`.`created_by` = `audit_event`.`user_id`, `audit_atom`.`created_at` = `audit_event`.`time_stamp`
WHERE `audit_event`.`audit_event_id` = `audit_atom`.`audit_event_id`
EOL;
        $this->db->query($sql);

        // drop index aeid_ra_k
        $sql = "ALTER TABLE `audit_atom` DROP INDEX `aeid_ra_k`";
        $this->db->query($sql);

        // remove audit_event_id and root_atom columns from audit_atom table
        $sql =<<< EOL
ALTER TABLE `audit_atom`
    DROP COLUMN `audit_event_id`,
    DROP COLUMN `root_atom`,
    DROP FOREIGN KEY `audit_atom_ibfk_1`
EOL;
        $this->db->query($sql);

        // remove audit_event and audit_content table.
        $sql = "DROP TABLE `audit_event`";
        $this->db->query($sql);
        $sql = "DROP TABLE `audit_content`";
        $this->db->query($sql);

        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */

    public function down()
    {
        // remove audit_atom table.
        $this->db->trans_start();
        $sql = "DROP TABLE `audit_atom`";
        $this->db->query($sql);

        // restore the old auditing tables.
        $sql =<<< EOL
CREATE TABLE `audit_event` (
    `audit_event_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `time_stamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `user_id` INT(14) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY (`audit_event_id`),
    INDEX `user_id_k` (`user_id`) USING BTREE,
    INDEX `ae_u_ts_k` (`audit_event_id`, `user_id`, `time_stamp`) USING BTREE,
    CONSTRAINT `audit_event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        $sql =<<< EOL
CREATE TABLE `audit_atom` (
    `audit_atom_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_row_id` INT(14) UNSIGNED NOT NULL,
    `table_column` VARCHAR(50) COLLATE 'utf8_unicode_ci' NOT NULL,
    `table_name` VARCHAR(50) COLLATE 'utf8_unicode_ci' NOT NULL,
    `event_type` TINYINT(1) UNSIGNED NOT NULL,
    `root_atom` TINYINT(1) NOT NULL,
    `audit_event_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`audit_atom_id`),
    INDEX `audit_event_id_k` (`audit_event_id`) USING BTREE,
    INDEX `aeid_ra_k` (`audit_event_id`, `root_atom`) USING BTREE,
    CONSTRAINT `audit_atom_ibfk_1` FOREIGN KEY (`audit_event_id`) REFERENCES `audit_event` (`audit_event_id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        $sql =<<< EOL
CREATE TABLE `audit_content` (
    `audit_atom_id` INT(14) UNSIGNED NOT NULL,
    `serialized_state_event` MEDIUMBLOB NOT NULL,
    INDEX `audit_atom_id_k` (`audit_atom_id`) USING BTREE,
    CONSTRAINT `audit_content_ibfk_1` FOREIGN KEY (`audit_atom_id`) REFERENCES `audit_atom` (`audit_atom_id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        $this->db->trans_complete();
    }
}
