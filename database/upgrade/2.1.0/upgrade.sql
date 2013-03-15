/*
 * Database upgrade script for Ilios version 2.1.0
 */

--
-- Adding foreign key constraints
--
-- course_x_cohort
ALTER TABLE `course_x_cohort`
ADD CONSTRAINT `fkey_course_x_cohort_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_course_x_cohort_cohort_id` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON DELETE CASCADE;

-- cohort
ALTER TABLE `cohort`
ADD CONSTRAINT `fkey_cohort_program_year_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`);

-- program year
ALTER TABLE `program_year`
ADD CONSTRAINT `fkey_program_year_program_id` FOREIGN KEY (`program_id`) REFERENCES `program` (`program_id`);

-- course_x_objective
ALTER TABLE `course_x_objective`
ADD KEY `course_objective_id_k` USING BTREE (`course_id`,`objective_id`),
ADD CONSTRAINT `fkey_course_x_objective_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_course_x_objective_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE;

-- program_year_x_competency
ALTER TABLE `program_year_x_competency`
ADD KEY `program_year_competency_id_k` USING BTREE (`program_year_id`,`competency_id`),
ADD CONSTRAINT `fkey_program_year_x_competency_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_program_year_x_competency_competency_id` FOREIGN KEY (`competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE;

-- objective_x_objective, *not able to add constraint for parent_objective_id because of value 0
ALTER TABLE `objective_x_objective`
ADD KEY `objective_objective_id_k` USING BTREE (`parent_objective_id`,`objective_id`);

ALTER TABLE `objective_x_objective`
ADD CONSTRAINT `fkey_objective_x_objective_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE;

-- course_x_discipline
ALTER TABLE `course_x_discipline`
ADD  KEY `course_discipline_id_k` USING BTREE (`course_id`,`discipline_id`),
ADD  CONSTRAINT `fkey_course_x_discipline_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_course_x_discipline_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE;

-- objective_x_mesh
ALTER TABLE `objective_x_mesh`
ADD CONSTRAINT `fkey_objective_x_mesh_objective_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE;

-- program_year_x_discipline
ALTER TABLE `program_year_x_discipline`
ADD KEY `program_year_discipline_id_k` USING BTREE (`program_year_id`,`discipline_id`),
ADD CONSTRAINT `fkey_program_year_x_discipline_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_program_year_x_discipline_discipline_id` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`discipline_id`) ON DELETE CASCADE;

-- ilm_session_facet_learner
DELETE FROM `ilm_session_facet_learner` WHERE `ilm_session_facet_id` NOT IN (SELECT `ilm_session_facet_id` FROM `ilm_session_facet`); -- remove orphans
ALTER TABLE `ilm_session_facet_learner`
ADD CONSTRAINT `fkey_ilm_learner_ilm_session_facet_id` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_ilm_learner_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_ilm_learner_group_id` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE;

-- ilm_session_facet_instructor
DELETE FROM `ilm_session_facet_instructor` WHERE `ilm_session_facet_id` NOT IN (SELECT `ilm_session_facet_id` FROM `ilm_session_facet`); -- remove orphans
ALTER TABLE `ilm_session_facet_instructor`
ADD CONSTRAINT `fkey_ilm_instructor_ilm_session_facet_id` FOREIGN KEY (`ilm_session_facet_id`) REFERENCES `ilm_session_facet` (`ilm_session_facet_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_ilm_instructor_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_ilm_instructor_group_id` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`) ON DELETE CASCADE;

-- group_default_instructor
DELETE FROM `group_default_instructor` WHERE `group_id` NOT IN (SELECT `group_id` FROM `group`); -- remove orphans
ALTER TABLE `group_default_instructor`
ADD CONSTRAINT `fkey_group_default_instructor_group_id` FOREIGN KEY (`group_id`) REFERENCES `group` (`group_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_group_default_instructor_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_group_default_instructor_instr_grp_id` FOREIGN KEY (`instructor_group_id`) REFERENCES `instructor_group` (`instructor_group_id`) ON DELETE CASCADE;

-- course_x_mesh
ALTER TABLE `course_x_mesh`
ADD CONSTRAINT `fkey_course_x_mesh_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE;

-- program_year_x_objective
ALTER TABLE `program_year_x_objective`
ADD KEY `program_year_objective_id_k` USING BTREE (`program_year_id`,`objective_id`),
ADD CONSTRAINT `fkey_program_year_x_objective_prg_yr_id` FOREIGN KEY (`program_year_id`) REFERENCES `program_year` (`program_year_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fkey_program_year_x_objective_obj_id` FOREIGN KEY (`objective_id`) REFERENCES `objective` (`objective_id`) ON DELETE CASCADE;

-- report_po_value
ALTER TABLE `report_po_value`
ADD CONSTRAINT `fkey_report_po_value_report_id` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE;


--
-- dropping unused procedures
--

DROP PROCEDURE IF EXISTS discipline_ids_for_instructor;
DROP PROCEDURE IF EXISTS discipline_ids_for_instructor_group;
DROP PROCEDURE IF EXISTS discipline_ids_for_learning_material;
DROP PROCEDURE IF EXISTS learning_material_ids_for_instructor;
DROP PROCEDURE IF EXISTS learning_material_ids_for_instructor_group;
DROP PROCEDURE IF EXISTS mesh_descriptor_ids_for_course;
DROP PROCEDURE IF EXISTS mesh_descriptor_ids_for_session;
DROP PROCEDURE IF EXISTS mesh_descriptor_ids_for_instructor;
DROP PROCEDURE IF EXISTS mesh_descriptor_ids_for_instructor_group;
DROP PROCEDURE IF EXISTS prot_mesh_desc_for_session;
DROP PROCEDURE IF EXISTS mesh_descriptor_ids_for_learning_material;


--
-- Adding title to report
--
ALTER TABLE `report`
ADD COLUMN `title` VARCHAR(240) COLLATE utf8_unicode_ci;

--
-- alter title size
--

-- alter competency title size
ALTER TABLE `competency` MODIFY `title` varchar(200);

-- alter course title size
ALTER TABLE `course` MODIFY `title` varchar(200);

-- alter seession title size
ALTER TABLE `session` MODIFY `title` varchar(200);

-- alter discipline title size
ALTER TABLE `discipline` MODIFY `title` varchar(200);

-- alter program title size
ALTER TABLE `program` MODIFY `title` varchar(200);

-- alter learning material filename size
ALTER TABLE `learning_material` MODIFY `filename` varchar(255);

--
-- update procedure courses_with_title_restricted_by_school to sort by title, start_date, end_date
--
DROP PROCEDURE IF EXISTS courses_with_title_restricted_by_school_for_user;

DELIMITER //
    CREATE PROCEDURE courses_with_title_restricted_by_school_for_user (in_title_query VARCHAR(30), in_school_id INT, in_user_id INT)
              READS SQL DATA
    BEGIN
        DECLARE out_of_rows INT DEFAULT 0;
        DECLARE cid INT DEFAULT 0;
        DECLARE course_owner_school_id INT DEFAULT 0;
        DECLARE flag INT DEFAULT 0;
        DECLARE count INT DEFAULT 0;
        DECLARE cid_cursor CURSOR FOR SELECT course_id, owning_school_id FROM course WHERE deleted = 0 AND archived = 0 AND title LIKE in_title_query;
        DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

        CREATE TEMPORARY TABLE IF NOT EXISTS tt_courses (`course_id` INT(14) UNSIGNED, `title` VARCHAR(100) COLLATE utf8_unicode_ci, `publish_event_id` INT(14) UNSIGNED, `course_level` SMALLINT(2) UNSIGNED, `year` SMALLINT(4) UNSIGNED, `start_date` DATE, `end_date` DATE, `deleted` TINYINT(1), `external_id` VARCHAR(18) COLLATE utf8_unicode_ci, `locked` TINYINT(1), `archived` TINYINT(1), `owning_school_id` INT(10) UNSIGNED, `published_as_tbd` TINYINT(1));


        OPEN cid_cursor;

        REPEAT
            FETCH cid_cursor INTO cid, course_owner_school_id;

            IF NOT out_of_rows THEN
                IF course_owner_school_id = in_school_id THEN
                    INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                ELSE
                    SET flag = 0;

                    SELECT count(can_write) FROM permission WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
                        INTO count;

                    IF (count > 0) THEN
                        SELECT can_write FROM permission WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
                            INTO flag;
                    END IF;

                    IF flag THEN
                        INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                    ELSE
                        SELECT course_has_cohort_stewarded_or_owned_by_school(cid, in_school_id)
                            INTO flag;

                        SET out_of_rows = 0;

                        IF flag THEN
                            INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                        END IF;
                    END IF;
                END IF;
            END IF;

        UNTIL out_of_rows END REPEAT;

        CLOSE cid_cursor;


        SELECT * FROM tt_courses ORDER BY `title`, `start_date`, `end_date`;
        DROP TABLE tt_courses;
    END;
    //
DELIMITER ;

-- rename report-category from "discipline" to "topic"
UPDATE `report` SET `subject` = 'topic' WHERE `subject` = 'discipline';
UPDATE `report` SET `prepositional_object` = 'topic' WHERE `prepositional_object` = 'discipline';

-- clean up garbage in learning materials
UPDATE `learning_material` SET filename = REPLACE(`filename`, 'C:\\fakepath\\', '') WHERE `filename` LIKE 'C:\\\\fakepath\\\\%';


-- add primary combined key to group/user JOIN table
-- this guarantees uniqueness of this association
ALTER TABLE `group_x_user` ADD PRIMARY KEY (`group_id`, `user_id`);

--
-- backfill the group/user JOIN table
--

SET @recursion_depth = @@max_sp_recursion_depth; -- get the current session recursion depth

-- The assumption is that no more than five levels of nested groups exist.
-- If MySQL starts throwing errors about exceeding the recursive limit then bump up this number and try again.

SET max_sp_recursion_depth = 5;

DELIMITER //
CREATE PROCEDURE `backfill_groups` (IN `gid` INT)
    LANGUAGE SQL
    NOT DETERMINISTIC
    MODIFIES SQL DATA
BEGIN
    DECLARE done BOOL DEFAULT FALSE;
    DECLARE sgid INT;
    DECLARE subgroup_cursor
        CURSOR FOR
            SELECT group_id
            FROM `group`
            WHERE COALESCE(`parent_group_id`, -1) = gid;
    DECLARE
        CONTINUE HANDLER FOR
        SQLSTATE '02000'
            SET done = TRUE;

    OPEN subgroup_cursor;

    -- iterate over subgroups and backfill them
    REPEAT

        FETCH subgroup_cursor INTO sgid;

        IF NOT sgid IS NULL THEN
            CALL backfill_groups(sgid);  -- recursive call to this function
        END IF;

    UNTIL done END REPEAT;

    CLOSE subgroup_cursor;

    -- backfill with users associated with subgroups
    REPLACE INTO `group_x_user` (`user_id`, `group_id`) (
        SELECT gu.`user_id`, g.`parent_group_id`
        FROM `group_x_user` gu
        JOIN `group` g ON g.`group_id` = gu.`group_id`
        WHERE g.`parent_group_id` = gid
    );

END;
//
DELIMITER ;

CALL backfill_groups(-1); -- run this on all root-level groups

DROP PROCEDURE `backfill_groups`; -- cleanup
SET max_sp_recursion_depth = @recursion_depth; -- reset the session's recursion depth

--
-- get rid of "administrator" entities
--

-- remove the admin id column and associated fkey/indeces
ALTER TABLE `audit_event` DROP INDEX `ae_a_ts_k`;
ALTER TABLE `audit_event` DROP COLUMN `administrator_id`, DROP FOREIGN KEY `audit_event_ibfk_2`;

-- drop the administrator table
DROP TABLE `administrator`;

-- remove "is administrator flag" from authentication table
ALTER TABLE `authentication` DROP COLUMN `person_is_administrator`;

-- alter index on permission table to enforce uniqueness on user permissions
ALTER TABLE `permission`
    DROP INDEX `user_table_k`,
    ADD UNIQUE INDEX `user_table_k` (`user_id`, `table_name`, `table_row_id`) USING BTREE;

--
-- implement multiple user/cohort associations model.
--

-- set up user/cohort join table
CREATE TABLE `user_x_cohort` (
    `user_id` INT(14) UNSIGNED NOT NULL,
    `cohort_id` INT(14) UNSIGNED NOT NULL,
    `is_primary` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`, `cohort_id`),
    INDEX `fkey_user_x_cohort_cohort` (`cohort_id`),
    CONSTRAINT `fkey_user_x_cohort_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fkey_user_x_cohort_cohort` FOREIGN KEY (`cohort_id`) REFERENCES `cohort` (`cohort_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- migrate existing "primary" user/cohort associations
INSERT INTO user_x_cohort (user_id, cohort_id, is_primary)
(SELECT user_id, cohort_id, 1 FROM user WHERE COALESCE(cohort_id, 0) != 0);

-- remove obsolete "cohort_id" column from users_table
ALTER TABLE `user` DROP COLUMN `cohort_id`;

-- fix up stored procedures
DROP PROCEDURE IF EXISTS user_ids_from_cohort_and_master_group;
DELIMITER //
    CREATE PROCEDURE user_ids_from_cohort_and_master_group (in_user_count INT, in_cohort_id INT, in_group_id INT)
        READS SQL DATA
    BEGIN
        DECLARE out_of_rows INT DEFAULT 0;
        DECLARE tmp_uid INT DEFAULT 0;
        DECLARE flag INT DEFAULT 0;
        DECLARE inserted_count INT DEFAULT 0;
        DECLARE uid_cursor CURSOR FOR SELECT u.`user_id` FROM `user` u JOIN `user_x_cohort` uxc ON uxc.`user_id` = u.`user_id` WHERE uxc.`cohort_id` = in_cohort_id;
        DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

        CREATE TEMPORARY TABLE IF NOT EXISTS tt_subgroup (uid INT);


        OPEN uid_cursor;

        REPEAT
            FETCH uid_cursor INTO tmp_uid;

            IF NOT out_of_rows THEN
                SELECT user_can_be_assigned(tmp_uid, in_group_id)
                    INTO flag;

                IF NOT flag THEN
                    INSERT INTO tt_subgroup (uid) VALUES (tmp_uid);

                    SET inserted_count = inserted_count + 1;
                END IF;
            END IF;

        UNTIL out_of_rows OR inserted_count = in_user_count END REPEAT;

        CLOSE uid_cursor;


        SELECT * FROM tt_subgroup;
        DROP TABLE tt_subgroup;
    END;
    //
DELIMITER ;


--
-- reconfigure authentication table
--

-- cleanup
DELETE FROM authentication WHERE person_id NOT IN (
  SELECT user_id FROM user
);

-- add the fkey to user::user_id
ALTER TABLE `authentication`
    ADD CONSTRAINT `fkey_authentication_user`
    FOREIGN KEY (`person_id`)
    REFERENCES `user` (`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE;

-- set person_id as new primary key and enforce uniqueness on username
ALTER TABLE `authentication`
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`person_id`),
    ADD UNIQUE INDEX `username` (`username`);
