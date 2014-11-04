<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Restores the stored procedure "user_ids_from_cohort_and_master_group" which was
 * removed accidentally in migration 29.
 */
class Migration_Restore_Procedure_For_Users_From_Group extends CI_Migration
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
CREATE PROCEDURE user_ids_from_cohort_and_master_group (in_user_count INT, in_cohort_id INT, in_group_id INT)
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
END;
EOL;
        $this->db->query($queryString);
        $this->db->trans_complete();
    }
    //no walk back from this since the actual procedure is suposed to be added to
    //a new install
    public function down()
    {

    }

}
