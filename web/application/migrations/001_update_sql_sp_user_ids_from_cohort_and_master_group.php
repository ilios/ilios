<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 * Update the user_ids_from_cohort_and_master_group stored procedure
 * to return randomly-selected users for subgroup auto-generation instead
 * of users in selected in simple alphabetical order.
 *
 */


class Migration_Update_sql_sp_user_ids_from_cohort_and_master_group extends CI_Migration {
  
  public function up()
  {
    //drop the existing stored procedure...
    $queryString = 'DROP PROCEDURE IF EXISTS user_ids_from_cohort_and_master_group';
    $DB = $this->dbHandle;
    $queryResults = $DB->query($queryString);
    
    //create the new one...
    $queryString = <<<EOL
CREATE PROCEDURE `user_ids_from_cohort_and_master_group`(IN `in_user_count` INT, IN `in_cohort_id` INT, IN `in_group_id` INT)
    READS SQL DATA
BEGIN
        DECLARE out_of_rows INT DEFAULT 0;
        DECLARE tmp_uid INT DEFAULT 0;
        DECLARE flag INT DEFAULT 0;
        DECLARE inserted_count INT DEFAULT 0;
        DECLARE uid_cursor CURSOR FOR SELECT u.`user_id` FROM `user` u JOIN `user_x_cohort` uxc ON uxc.`user_id` = u.`user_id` WHERE uxc.`cohort_id` = in_cohort_id ORDER BY RAND();
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
    END    
EOL;

    $DB = $this->dbHandle;
    $queryResults = $DB->query($queryString);
    
  }// end up() method
  
  public function down()
  {
    //drop the existing stored procedure...
    $queryString = 'DROP PROCEDURE IF EXISTS user_ids_from_cohort_and_master_group';
    $DB = $this->dbHandle;
    $queryResults = $DB->query($queryString);
    
    //create the new one...
    $queryString = <<<EOL
CREATE PROCEDURE `user_ids_from_cohort_and_master_group`(IN `in_user_count` INT, IN `in_cohort_id` INT, IN `in_group_id` INT)
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
    END    
EOL;

    $DB = $this->dbHandle;
    $queryResults = $DB->query($queryString);  
  
  } // end down() method
}