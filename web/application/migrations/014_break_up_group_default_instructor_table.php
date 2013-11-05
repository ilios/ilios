<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breaks up the "group_default_instructor" table into two separate join tables.
 */
class Migration_Break_up_group_default_instructor_table extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        // create new join tables
        $sql =<<<EOL
CREATE TABLE `group_x_instructor` (
    `group_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `user_id`),
    CONSTRAINT `fkey_group_x_instructor_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_group_x_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `group_x_instructor_group` (
    `group_id` INT(14) UNSIGNED NOT NULL,
    `instructor_group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY (`group_id`, `instructor_group_id`),
    CONSTRAINT `fkey_group_x_instructor_group_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
        ON DELETE CASCADE,
    CONSTRAINT `fkey_group_x_instructor_group_instructor_group_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
        ON DELETE CASCADE
) DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // copy data out of "group_default_instructor" into the appropriate join table.
        $sql =<<<EOL
INSERT INTO `group_x_instructor` (`group_id`, `user_id`) (
    SELECT DISTINCT `group_id`, `user_id` FROM `group_default_instructor`
    WHERE `user_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `group_x_instructor_group` (`group_id`, `instructor_group_id`) (
    SELECT DISTINCT `group_id`, `instructor_group_id` FROM `group_default_instructor`
    WHERE `instructor_group_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);

        // nuke "group_default_instructor"
        $sql = "DROP TABLE `group_default_instructor`";
        $this->db->query($sql);

        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();

        // re-create "group_default_instructor" table
        $sql =<<< EOL
CREATE TABLE `group_default_instructor` (
    `group_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED,
    `instructor_group_id` INT(14) UNSIGNED,
     CONSTRAINT `fkey_group_default_instructor_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
        ON DELETE CASCADE,
     CONSTRAINT `fkey_group_default_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON DELETE CASCADE,
     CONSTRAINT `fkey_group_default_instructor_instr_grp_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
        ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
EOL;
        $this->db->query($sql);

        // copy data from join tables into "group_default_instructor"
        $sql =<<<EOL
INSERT INTO `group_default_instructor` (`group_id`, `user_id`) (
    SELECT `group_id`, `user_id` FROM `group_x_instructor`
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `group_default_instructor` (`group_id`, `instructor_group_id`) (
    SELECT `group_id`, `instructor_group_id` FROM `group_x_instructor_group`
)
EOL;
        $this->db->query($sql);

        // nuke the other two join tables
        $sql = "DROP TABLE `group_x_instructor`";
        $this->db->query($sql);
        $sql = "DROP TABLE `group_x_instructor_group`";
        $this->db->query($sql);

        $this->db->trans_complete();
    }
}
