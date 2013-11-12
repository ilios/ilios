<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breaks up the "offering_learner" table into two separate join tables.
 */
class Migration_Break_up_offering_learner_table extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        // create new join tables
        $sql =<<<EOL
CREATE TABLE `offering_x_learner` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `user_id`),
    CONSTRAINT `fkey_offering_x_learner_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_learner_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
) DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TABLE `offering_x_group` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `group_id` INT(14) UNSIGNED NOT NULL,
    PRIMARY KEY(`offering_id`, `group_id`),
    CONSTRAINT `fkey_offering_x_group_offering_id`
        FOREIGN KEY (`offering_id`)
        REFERENCES `offering` (`offering_id`),
    CONSTRAINT `fkey_offering_x_group_group_id`
        FOREIGN KEY (`group_id`)
        REFERENCES `group` (`group_id`)
) DEFAULT CHARSET='utf8'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // copy data out of "offering_learner" into the appropriate join table.
        $sql =<<<EOL
INSERT INTO `offering_x_learner` (`offering_id`, `user_id`) (
    SELECT DISTINCT `offering_id`, `user_id` FROM `offering_learner`
    WHERE `user_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `offering_x_group` (`offering_id`, `group_id`) (
    SELECT DISTINCT `offering_id`, `group_id` FROM `offering_learner`
    WHERE `group_id` IS NOT NULL
)
EOL;
        $this->db->query($sql);

        // create triggers on new join tables

        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_learner_post_delete` AFTER DELETE ON `offering_x_learner`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_learner_post_insert` AFTER INSERT ON `offering_x_learner`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_group_post_delete` AFTER DELETE ON `offering_x_group`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_x_group_post_insert` AFTER INSERT ON `offering_x_group`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);

        // drop triggers on "offering_learner" table
        $this->db->query("DROP TRIGGER `trig_offering_learner_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_learner_post_insert`");

        // nuke "offering_learner"
        $sql = "DROP TABLE `offering_learner`";
        $this->db->query($sql);

        // delete and re-create relevant stored procedures
        $sql = "DROP PROCEDURE `nuke_offering`";
        $this->db->query($sql);
        $sql =<<< EOL
CREATE PROCEDURE nuke_offering (in_offering_id INT)
    READS SQL DATA
BEGIN
    DECLARE recurring_event_id INT DEFAULT 0;
    DECLARE select_found_match INT DEFAULT 1;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET select_found_match = 0;


    DELETE FROM offering_x_learner WHERE offering_id = in_offering_id;
    DELETE FROM offering_x_group WHERE offering_id = in_offering_id;
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

        // re-create "offering_learner" table
        $sql =<<< EOL
CREATE TABLE `offering_learner` (
    `offering_id` INT(14) UNSIGNED NOT NULL,
    `user_id` INT(14) UNSIGNED NULL DEFAULT NULL,
    `group_id` INT(14) UNSIGNED NULL DEFAULT NULL,
    INDEX `user_id_k` (`user_id`) USING BTREE,
    INDEX `group_id_k` (`group_id`) USING BTREE,
    INDEX `offering_user_group_k` (`offering_id`, `user_id`, `group_id`) USING BTREE,
    CONSTRAINT `offering_learner_ibfk_1` FOREIGN KEY (`offering_id`) REFERENCES `offering` (`offering_id`),
    CONSTRAINT `offering_learner_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
    CONSTRAINT `offering_learner_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // copy data from join tables into "offering_learner"
        $sql =<<<EOL
INSERT INTO `offering_learner` (`offering_id`, `user_id`) (
    SELECT `offering_id`, `user_id` FROM `offering_x_learner`
)
EOL;
        $this->db->query($sql);
        $sql =<<<EOL
INSERT INTO `offering_learner` (`offering_id`, `group_id`) (
    SELECT `offering_id`, `group_id` FROM `offering_x_group`
)
EOL;
        $this->db->query($sql);

        // re-create triggers on "offering_learner"
        $sql =<<<EOL
CREATE TRIGGER `trig_offering_learner_post_delete` AFTER DELETE ON `offering_learner`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = OLD.`offering_id`;
END
EOL;
        $this->db->query($sql);
        $sql=<<<EOL
CREATE TRIGGER `trig_offering_learner_post_insert` AFTER INSERT ON `offering_learner`
FOR EACH ROW BEGIN
    UPDATE `offering` SET `offering`.`last_updated_on` = NOW()
    WHERE `offering`.`offering_id` = NEW.`offering_id`;
END
EOL;
        $this->db->query($sql);

        // drop triggers on offering_x_learner and offering_x_group
        $this->db->query("DROP TRIGGER `trig_offering_x_learner_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_x_learner_post_insert`");
        $this->db->query("DROP TRIGGER `trig_offering_x_group_post_delete`");
        $this->db->query("DROP TRIGGER `trig_offering_x_group_post_insert`");

        // nuke offering_x_learner and offering_x_group
        $sql = "DROP TABLE `offering_x_learner`";
        $this->db->query($sql);
        $sql = "DROP TABLE `offering_x_group`";
        $this->db->query($sql);

        // delete and re-create relevant stored procedures
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
}
