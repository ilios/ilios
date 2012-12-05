/*
 * Database upgrade script for Ilios version 2.1.1
 */

--
--  add template_prefix to the school table
--
ALTER TABLE `school`
    ADD `template_prefix` VARCHAR(8) NULL AFTER `school_id`,
    ADD UNIQUE (`template_prefix`);

--
-- update the session_type table to drop the old columns and add the new
-- ones for proper session_type css-handling
--

ALTER TABLE `session_type`
    DROP `fill_color`,
    DROP `icon_url`,
    ADD `owning_school_id` INT(10) unsigned NOT NULL,
    ADD `assessment` BOOL NOT NULL DEFAULT 0,
    ADD `session_type_css_class` VARCHAR(64) NULL AFTER `owning_school_id`;

-- update existing session-types, see supplementary update script for session type remapping.
UPDATE `session_type` SET `session_type_css_class` = 'clerkship' WHERE `title` = 'Clerkship';
UPDATE `session_type` SET `session_type_css_class` = 'exam' WHERE `title` = 'Exam';
UPDATE `session_type` SET `session_type_css_class` = 'holiday' WHERE `title` = 'Holiday';
UPDATE `session_type` SET `session_type_css_class` = 'hospice' WHERE `title` = 'Hospice';
UPDATE `session_type` SET `session_type_css_class` = 'laboratory' WHERE `title` = 'Laboratory';
UPDATE `session_type` SET `session_type_css_class` = 'large-group-presentation' WHERE `title` = 'Large Group Presentation';
UPDATE `session_type` SET `session_type_css_class` = 'lecture' WHERE `title` = 'Lecture';
UPDATE `session_type` SET `session_type_css_class` = 'opt-review-session' WHERE `title` = 'OPT./Review Session';
UPDATE `session_type` SET `session_type_css_class` = 'osce' WHERE `title` = 'OSCE';
UPDATE `session_type` SET `session_type_css_class` = 'physical-exam' WHERE `title` = 'Physical Exam';
UPDATE `session_type` SET `session_type_css_class` = 'problem-based-learning' WHERE `title` = 'Problem Based Learning';
UPDATE `session_type` SET `session_type_css_class` = 'small-group' WHERE `title` = 'Small Group';
UPDATE `session_type` SET `session_type_css_class` = 'preceptorship' WHERE `title` = 'Preceptorship';
UPDATE `session_type` SET `session_type_css_class` = 'reading-day' WHERE `title` = 'Reading Day';
UPDATE `session_type` SET `session_type_css_class` = 'rounds' WHERE `title` = 'Rounds';
UPDATE `session_type` SET `session_type_css_class` = 'outpatient-clinic' WHERE `title` = 'Outpatient Clinic';
UPDATE `session_type` SET `session_type_css_class` = 'call' WHERE `title` = 'Call';
UPDATE `session_type` SET `session_type_css_class` = 'team-based-learning' WHERE `title` = 'Team Based Learning';


-- clone the existing session types per school
INSERT INTO `session_type` (`title`, `owning_school_id`, `session_type_css_class`, `assessment`)
(
    SELECT st.title, s.school_id, st.session_type_css_class, 0
    FROM `session_type` st, `school` s
    ORDER BY s.school_id, st.session_type_id
);

-- temporarily drop the update trigger on the 'session' table
-- to prevent the last-updated timestamp to be affected by the following updates
DROP TRIGGER IF EXISTS `trig_session_pre_update`;

-- rewire existing session/session type associations and re-scope them by school
UPDATE `session` s SET s.session_type_id =
(
    SELECT DISTINCT st.session_type_id
    FROM `session_type` st
    JOIN `course` c ON c.owning_school_id = st.owning_school_id
    JOIN `session_type` st2 ON st2.title = st.title
    WHERE s.course_id = c.course_id
    AND st2.session_type_id = s.session_type_id
);

-- re-create update-trigger on the 'session' table

delimiter $$

CREATE TRIGGER `trig_session_pre_update` BEFORE UPDATE ON `session`
FOR EACH ROW BEGIN
    IF (NEW.`title` <> OLD.`title`
        || NEW.`publish_event_id` <> OLD.`publish_event_id`
        || NEW.`attire_required` <> OLD.`attire_required`
        || NEW.`equipment_required` <> OLD.`equipment_required`
        || NEW.`supplemental` <> OLD.`supplemental`
        || NEW.`session_type_id` <> OLD.`session_type_id`
        || NEW.`deleted` <> OLD.`deleted`
        || NEW.`ilm_session_facet_id` <> OLD.`ilm_session_facet_id`
        || NEW.`published_as_tbd` <> OLD.`published_as_tbd`) THEN
        SET NEW.`last_updated_on` = NOW();
    END IF;
END;

$$

delimiter ;

-- drop "global" session types
DELETE FROM `session_type` WHERE owning_school_id = 0;

-- add foreign key constraint on 'owning_school_id' to session_type table
ALTER TABLE `session_type`
ADD FOREIGN KEY (`owning_school_id`) REFERENCES school(school_id);

-- create and populate course clerkship type lookup table
-- and add clerkship type column to course table

CREATE TABLE `course_clerkship_type` (
    `course_clerkship_type_id` INT(10) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`course_clerkship_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `course_clerkship_type` (`course_clerkship_type_id`, `title`) VALUES (1, 'block');
INSERT INTO `course_clerkship_type` (`course_clerkship_type_id`, `title`) VALUES (2, 'longitudinal');
INSERT INTO `course_clerkship_type` (`course_clerkship_type_id`, `title`) VALUES (3, 'integrated');

ALTER TABLE `course`
    ADD COLUMN `clerkship_type_id` INT(10) NULL AFTER `published_as_tbd`,
    ADD CONSTRAINT `clerkship_type_id` FOREIGN KEY (`clerkship_type_id`)
    REFERENCES `course_clerkship_type` (`course_clerkship_type_id`);

-- re-create stored procedure to accomodate for changes to course table
DROP PROCEDURE IF EXISTS courses_with_title_restricted_by_school_for_user;
DELIMITER //
    CREATE PROCEDURE `courses_with_title_restricted_by_school_for_user`(IN `in_title_query` VARCHAR(30), IN `in_school_id` INT, IN `in_user_id` INT)
        LANGUAGE SQL
        NOT DETERMINISTIC
        READS SQL DATA
        SQL SECURITY DEFINER
        COMMENT ''
    BEGIN
        DECLARE out_of_rows INT DEFAULT 0;
        DECLARE cid INT DEFAULT 0;
        DECLARE course_owner_school_id INT DEFAULT 0;
        DECLARE flag INT DEFAULT 0;
        DECLARE count INT DEFAULT 0;
        DECLARE cid_cursor CURSOR FOR
           SELECT course_id, owning_school_id
           FROM course
           WHERE deleted = 0 AND archived = 0 AND title LIKE in_title_query;
        DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

        CREATE TEMPORARY TABLE IF NOT EXISTS tt_courses (
            `course_id` INT(14) UNSIGNED,
            `title` VARCHAR(100) COLLATE utf8_unicode_ci,
            `publish_event_id` INT(14) UNSIGNED,
            `course_level` SMALLINT(2) UNSIGNED,
            `year` SMALLINT(4) UNSIGNED,
            `start_date` DATE,
            `end_date` DATE,
            `deleted` TINYINT(1),
            `external_id` VARCHAR(18) COLLATE utf8_unicode_ci,
            `locked` TINYINT(1),
            `archived` TINYINT(1),
            `owning_school_id` INT(10) UNSIGNED,
            `published_as_tbd` TINYINT(1),
            `clerkship_type_id` INT(10) UNSIGNED
        );

        OPEN cid_cursor;

        REPEAT
            FETCH cid_cursor INTO cid, course_owner_school_id;

            IF NOT out_of_rows THEN
                IF course_owner_school_id = in_school_id THEN
                    INSERT INTO tt_courses SELECT * FROM course WHERE course_id = cid;
                ELSE
                    SET flag = 0;

                    SELECT count(can_write)
                    FROM permission
                    WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
                    INTO count;

                    IF (count > 0) THEN
                        SELECT can_write
                        FROM permission
                        WHERE user_id = in_user_id AND table_name = 'course' AND table_row_id = cid
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

        SELECT *
        FROM tt_courses
        ORDER BY `title`, `start_date`, `end_date`;

        DROP TABLE tt_courses;
    END;
//
DELIMITER ;
