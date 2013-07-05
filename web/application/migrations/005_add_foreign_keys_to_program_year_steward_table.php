<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds foreign key constraints to the "program_year_steward" join table to ensure referential integrity.
 * While at it, de-dupe the table's record set and add a uniqueness constraint spanning all three columns.
 */
class Migration_Add_Foreign_keys_to_program_year_steward_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        // remove any orphaned entries in the join table
        $sql = "DELETE FROM `program_year_steward` WHERE `program_year_id` NOT IN (SELECT `program_year_id` FROM `program_year`)";
        $this->db->query($sql);
        $sql = "DELETE FROM `program_year_steward` WHERE `school_id` NOT IN (SELECT `school_id` FROM `school`)";
        $this->db->query($sql);
        $sql = "DELETE FROM `program_year_steward` WHERE `department_id` IS NOT NULL AND `department_id` NOT IN (SELECT `department_id` FROM `department`)";
        $this->db->query($sql);
        // dedupe via temp table
        // see: http://www.mikeperham.com/2012/03/02/deleting-duplicate-rows-in-mysql/
        $sql = 'CREATE TABLE `program_year_steward_deduped` LIKE `program_year_steward`';
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `program_year_steward_deduped` (`program_year_id`, `school_id`, `department_id`)(
    SELECT DISTINCT `program_year_id`, `school_id`, `department_id` FROM `program_year_steward`
)
EOL;
        $this->db->query($sql);
        $sql = 'DELETE FROM `program_year_steward`';
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `program_year_steward` (`program_year_id`, `school_id`, `department_id`) (
    SELECT program_year_id, `school_id`, `department_id` FROM `program_year_steward_deduped`
)
EOL;
        $this->db->query($sql);
        $sql = 'DROP TABLE `program_year_steward_deduped`';
        $this->db->query($sql);

        // add uniqueness constraint
        $sql =<<<EOL
ALTER TABLE `program_year_steward`
ADD UNIQUE INDEX `program_year_id_school_id_department_id` (`program_year_id`, `school_id`, `department_id`)
EOL;
        $this->db->query($sql);

        // add the foreign key constraints
        $sql =<<<EOL
ALTER TABLE `program_year_steward`
    ADD CONSTRAINT `fkey_program_year_steward_program_year`
        FOREIGN KEY (`program_year_id`)
        REFERENCES `program_year` (`program_year_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    ADD CONSTRAINT `fkey_program_year_steward_school`
        FOREIGN KEY (`school_id`)
        REFERENCES `school` (`school_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    ADD CONSTRAINT `fkey_program_year_steward_department`
        FOREIGN KEY (`department_id`)
        REFERENCES `department` (`department_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE
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
        // remove the foreign key constraints
        $sql =<<<EOL
ALTER TABLE `program_year_steward`
    DROP FOREIGN KEY `fkey_program_year_steward_department`,
    DROP FOREIGN KEY `fkey_program_year_steward_program_year`,
    DROP FOREIGN KEY `fkey_program_year_steward_school`
EOL;
        $this->db->query($sql);
        // remove the uniqueness constraint
        $sql = "ALTER TABLE `program_year_steward` DROP INDEX `program_year_id_school_id_department_id`";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
