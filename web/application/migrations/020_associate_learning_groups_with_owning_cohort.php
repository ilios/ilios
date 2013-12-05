<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breaks up the `cohort_master_group` JOIN table and moves the `cohort_id` directly into the `group`
 * table
 */
class Migration_Associate_learning_groups_with_owning_cohort extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        //add the `cohort_id` column to the `group` table
        $sql = "ALTER TABLE `group` ADD COLUMN `cohort_id` INT(14) UNSIGNED NULL AFTER `parent_group_id`";
        $this->db->query($sql);

        //then populate the `cohort_id` column with the cohort_id from the `cohort_master_group` table
        $sql = "UPDATE `group` `g` SET `g`.`cohort_id` = (SELECT `cmg`.`cohort_id` FROM `cohort_master_group` `cmg` WHERE `cmg`.`group_id` = `g`.`group_id`)";
        $this->db->query($sql);

        //then drop the `cohort_master_group` table completely
        $sql = "DROP TABLE `cohort_master_group`";
        $this->db->query($sql);

        //then drop the `root_group_of_group` function
        $sql = "DROP FUNCTION root_group_of_group";
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();

        //create the `cohort_master_group` table
        $sql = <<<EOL
CREATE TABLE `cohort_master_group` (
	  `cohort_id` INT(14) UNSIGNED NOT NULL,
	  `group_id` INT(14) UNSIGNED NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOL;
        $this->db->query($sql);

        //then insert the rows from the group table
        $sql = "INSERT INTO `cohort_master_group` (`cohort_id`,`group_id`) SELECT `cohort_id`, `group_id` FROM `group` ORDER BY `group_id` WHERE `cohort_id` IS NOT NULL";
        $this->db->query($sql);

        //then drop the `cohort_id` column from group table
        $sql = "ALTER TABLE `group` DROP COLUMN `cohort_id`";
        $this->db->query($sql);

        //then create the root_group_of_group function
        $sql =  <<<EOL
DELIMITER //
	CREATE FUNCTION root_group_of_group (in_gid INT)
		RETURNS INT
		READS SQL DATA
	BEGIN
		DECLARE gid INT DEFAULT in_gid;
		DECLARE pgid INT DEFAULT 0;

		WHILE gid IS NOT NULL DO
			SELECT parent_group_id
				INTO pgid
				FROM `group`
				WHERE group_id = gid;

			IF pgid IS NULL THEN
				RETURN gid;
			ELSE
				SET gid = pgid;
			END IF;
		END WHILE;

		RETURN 0;
	END;
	//
DELIMITER ;
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
