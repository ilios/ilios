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

        // delete and re-create relevant stored procedures
        $sql = "DROP FUNCTION `copy_offering_attributes_to_offering`";
        $this->db->query($sql);
        $sql=<<< EOL
CREATE FUNCTION copy_offering_attributes_to_offering (in_original_oid INT, in_new_oid INT)
    RETURNS INT
    READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE uid INT DEFAULT 0;
    DECLARE gid INT DEFAULT 0;
    DECLARE rows_added INT DEFAULT 0;
    DECLARE i_cursor CURSOR FOR SELECT user_id FROM offering_x_instructor WHERE offering_id = in_original_oid;
    DECLARE j_cursor CURSOR FOR SELECT instructor_group_id FROM offering_x_instructor_group WHERE offering_id = in_original_oid;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    OPEN i_cursor;

    REPEAT
        FETCH i_cursor INTO uid;

        IF NOT out_of_rows THEN
            INSERT INTO offering_x_instructor (offering_id, user_id) VALUES (in_new_oid, uid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE i_cursor;

    SET out_of_rows = 0;

    OPEN j_cursor;

    REPEAT
        FETCH j_cursor INTO gid;

        IF NOT out_of_rows THEN
            INSERT INTO offering_x_instructor_group (offering_id, instructor_group_id) VALUES (in_new_oid, gid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE j_cursor;

    RETURN rows_added;
END
EOL;
        $this->db->query($sql);
        $sql = "DROP PROCEDURE `nuke_offering`";
        $this->db->query($sql);
        $sql =<<< EOL
CREATE PROCEDURE nuke_offering (in_offering_id INT)
    READS SQL DATA
BEGIN
    DECLARE recurring_event_id INT DEFAULT 0;
    DECLARE select_found_match INT DEFAULT 1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET select_found_match = 0;


    DELETE FROM offering_learner WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_instructor WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_instructor_group WHERE offering_id = in_offering_id;

    SELECT recurring_event_id FROM offering_x_recurring_event WHERE offering_id = in_offering_id
        INTO recurring_event_id;

    IF select_found_match THEN
        CALL nuke_recurring_event_chain(recurring_event_id);
        DELETE FROM offering_x_recurring_event WHERE offering_id = in_offering_id;
    END IF;

    DELETE FROM offering WHERE offering_id = in_offering_id;
END
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

        // delete and re-create relevant stored procedures
        $sql = "DROP FUNCTION `copy_offering_attributes_to_offering`";
        $this->db->query($sql);
        $sql =<<<EOL
CREATE FUNCTION copy_offering_attributes_to_offering (in_original_oid INT, in_new_oid INT)
    RETURNS INT
    READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE uid INT DEFAULT 0;
    DECLARE gid INT DEFAULT 0;
    DECLARE rows_added INT DEFAULT 0;
    DECLARE i_cursor CURSOR FOR SELECT user_id, instructor_group_id FROM offering_instructor WHERE offering_id = in_original_oid;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    OPEN i_cursor;

    REPEAT
        FETCH i_cursor INTO uid, gid;

        IF NOT out_of_rows THEN
            INSERT INTO offering_instructor (offering_id, user_id, instructor_group_id) VALUES (in_new_oid, uid, gid);

            SET rows_added = rows_added + 1;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE i_cursor;

    RETURN rows_added;
END
EOL;
        $this->db->query($sql);
        $sql = "DROP PROCEDURE `nuke_offering`";
        $this->db->query($sql);
        $sql =<<<EOL
CREATE PROCEDURE nuke_offering (in_offering_id INT)
    READS SQL DATA
BEGIN
    DECLARE recurring_event_id INT DEFAULT 0;
    DECLARE select_found_match INT DEFAULT 1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET select_found_match = 0;


    DELETE FROM offering_learner WHERE offering_id = in_offering_id;
    DELETE FROM offering_instructor WHERE offering_id = in_offering_id;

    SELECT recurring_event_id FROM offering_x_recurring_event WHERE offering_id = in_offering_id
        INTO recurring_event_id;

    IF select_found_match THEN
        CALL nuke_recurring_event_chain(recurring_event_id);
        DELETE FROM offering_x_recurring_event WHERE offering_id = in_offering_id;
    END IF;

    DELETE FROM offering WHERE offering_id = in_offering_id;
END
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
