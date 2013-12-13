<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Removes the stored procedure "disciplines_for_title_restricted_by_school".
 */
class Migration_Remove_disciplines_for_title_restricted_by_school extends CI_Migration
{

    public function up()
    {
        $this->db->trans_start();
        $this->db->query('DROP PROCEDURE disciplines_for_title_restricted_by_school');
        $this->db->trans_complete();

    }

    public function down()
    {
        $this->db->trans_start();
        $sql =<<<EOL
CREATE PROCEDURE disciplines_for_title_restricted_by_school (in_title_query VARCHAR(30), in_school_id INT)
READS SQL DATA
BEGIN
    DECLARE out_of_rows INT DEFAULT 0;
    DECLARE did INT DEFAULT 0;
    DECLARE discipline_owner_school_id INT DEFAULT 0;
    DECLARE flag INT DEFAULT 0;
    DECLARE did_cursor CURSOR FOR SELECT discipline_id, owning_school_id FROM discipline WHERE title LIKE in_title_query;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET out_of_rows = 1;

    CREATE TEMPORARY TABLE IF NOT EXISTS tt_disciplines (`discipline_id` INT(14) UNSIGNED, `title` VARCHAR(60) COLLATE utf8_unicode_ci, `owning_school_id` INT(10) UNSIGNED);

    OPEN did_cursor;

    REPEAT
        FETCH did_cursor INTO did, discipline_owner_school_id;

        IF NOT out_of_rows THEN
            IF discipline_owner_school_id = in_school_id THEN
            INSERT INTO tt_disciplines SELECT * FROM discipline WHERE discipline_id = did;
            END IF;
        END IF;

    UNTIL out_of_rows END REPEAT;

    CLOSE did_cursor;

    SELECT * FROM tt_disciplines ORDER BY `title`;
    DROP TABLE tt_disciplines;
END
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
