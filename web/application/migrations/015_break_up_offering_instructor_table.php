<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breaks up the "offering_instructor" table into two separate join tables.
 */
class Migration_Break_up_offering_instructor_table extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        // create new join tables
        $sql =<<<EOL
CREATE TABLE `offering_x_instructor` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `user_id`),
    CONSTRAINT `fkey_offering_x_instructor_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_instructor_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
) DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `offering_x_instructor_group` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `instructor_group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `instructor_group_id`),
    CONSTRAINT `fkey_offering_x_instructor_group_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_instructor_group_instructor_group_id`
        FOREIGN KEY (`instructor_group_id`)
        REFERENCES `instructor_group` (`instructor_group_id`)
) DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // copy data out of "offering_instructor" into the appropriate join table.
        $sql =<<<EOL
INSERT INTO `offering_x_instructor` (`offering_id`, `user_id`) (
    SELECT DISTINCT `offering_id`, `user_id` FROM `offering_instructor`
    WHERE `user_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `offering_x_instructor_group` (`offering_id`, `instructor_group_id`) (
    SELECT DISTINCT `offering_id`, `instructor_group_id` FROM `offering_instructor`
    WHERE `instructor_group_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);

        // create triggers on new join tables

        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_instructor_post_delete` AFTER DELETE ON `offering_x_instructor`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_instructor_post_insert` AFTER INSERT ON `offering_x_instructor`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_instructor_group_post_delete` AFTER DELETE ON `offering_x_instructor_group`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_instructor_group_post_insert` AFTER INSERT ON `offering_x_instructor_group`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);

        // drop triggers on "offering_instructor" table
        $this->db->query("DROP TRIGGER `trig_offering_instructor_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_instructor_post_insert`");

        // nuke "offering_instructor"
        $sql = "DROP TABLE `offering_instructor`";
        $this->db->query($sql);

        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();

        // re-create "offering_instructor" table
        $sql =<<< EOL
CREATE TABLE `offering_instructor` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NULL DEFAULT NULL,
    `instructor_group_id` INT(14) UNSIGNED NULL DEFAULT NULL,
    INDEX `user_id_k` (`user_id`) USING BTREE,
    INDEX `instructor_group_id_k` (`instructor_group_id`) USING BTREE,
    INDEX `offering_user_group_k` (`offering_id`, `user_id`, `instructor_group_id`) USING BTREE,
    CONSTRAINT `offering_instructor_ibfk_1` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`),
    CONSTRAINT `offering_instructor_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    CONSTRAINT `offering_instructor_ibfk_3` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // copy data from join tables into "offering_instructor"
        $sql =<<<EOL
INSERT INTO `offering_instructor` (`offering_id`, `user_id`) (
    SELECT `offering_id`, `user_id` FROM `offering_x_instructor`
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `offering_instructor` (`offering_id`, `instructor_group_id`) (
    SELECT `offering_id`, `instructor_group_id` FROM `offering_x_instructor_group`
)
EOL;
        $this->db->query($sql);

        // re-create triggers on "offering_instructor"
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_instructor_post_delete` AFTER DELETE ON `offering_instructor`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql=<<<EOL
CREATE TRIGGER `trig_offering_instructor_post_insert` AFTER INSERT ON `offering_instructor`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);

        // drop triggers on offering_x_instructor and offering_x_instructor_group
        $this->db->query("DROP TRIGGER `trig_offering_x_instructor_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_x_instructor_post_insert`");
        $this->db->query("DROP TRIGGER `trig_offering_x_instructor_group_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_x_instructor_group_post_insert`");

        // nuke offering_x_instructor and offering_x_instructor_group
        $sql = "DROP TABLE `offering_x_instructor`";
        $this->db->query($sql);
        $sql = "DROP TABLE `offering_x_instructor_group`";
        $this->db->query($sql);

        $this->db->trans_complete();
    }
}
