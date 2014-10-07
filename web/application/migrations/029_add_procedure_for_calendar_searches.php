<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds the stored procedure "courses_with_title_restricted_by_school_for_user_calendar".
 */
class Migration_Add_procedure_for_calendar_searches extends CI_Migration
{

    public function up()
    {
        //set up for the transaction...
        $this->db->trans_start();
        //query to drop the existing stored procedure...
        $queryString = 'DROP PROCEDURE IF EXISTS user_ids_from_cohort_and_master_group';
        //drop the stored procedure...
        $queryResults = $this->db->query($queryString);
        $queryString = <<<EOL
CREATE PROCEDURE `courses_with_title_restricted_by_school_for_user_calendar`(IN `in_title_query` VARCHAR(30), IN `in_school_id` INT, IN `in_user_id` INT)
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
      WHERE deleted = 0 AND title LIKE in_title_query;
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
  END
EOL;
        $this->db->query($queryString);
        $this->db->trans_complete();
    }

    public function down()
    {
        $this->db->trans_start();
        $this->db->query('DROP PROCEDURE courses_with_title_restricted_by_school_for_user_calendar');
        $this->db->trans_complete();

    }

}
