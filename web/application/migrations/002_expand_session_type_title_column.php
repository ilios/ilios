<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Expand the max. size of the "title" column in the "session_type" table to 100 chars.
 */
class Migration_Expand_session_type_title_column extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql = "ALTER TABLE `session_type` CHANGE COLUMN `title` `title` VARCHAR(100) NOT NULL";
        $this->db->query($sql);
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        $sql = "ALTER TABLE `session_type` CHANGE COLUMN `title` `title` VARCHAR(60) NOT NULL";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
