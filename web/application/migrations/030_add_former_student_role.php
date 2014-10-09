<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds the former student role to the user_role table
 */
class Migration_Add_former_student_role extends CI_Migration
{

    /**
     * @see CI_Migration::up()
     */
    public function up()
    {
        $this->db->trans_start();
        // Add the former student role
        $sql = 'INSERT INTO user_role (user_role_id, title) VALUES (9, "Former Student")';
        $this->db->query($sql);
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */

    public function down()
    {
            $this->db->trans_start();
            // Remove the former student role
            $sql = 'DELETE FROM user_role WHERE user_role_id=9';
            $this->db->query($sql);
            $this->db->trans_complete();
    }
}
