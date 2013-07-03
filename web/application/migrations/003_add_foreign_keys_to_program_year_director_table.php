<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds foreign key constraints to the "program_year_director" join table to ensure referencial integrity.
 */
class Migration_Add_Foreign_keys_to_program_year_director_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        // remove any orphaned entries in the join table
        $sql = "DELETE FROM `program_year_director` WHERE `program_year_id` NOT IN (SELECT `program_year_id` FROM `program_year`)";
        $this->db->query($sql);
        $sql = "DELETE FROM `program_year_director` WHERE `user_id` NOT IN (SELECT `user_id` FROM `user`)";
        $this->db->query($sql);

        // add the foreign key constraints
        $sql =<<<EOL
ALTER TABLE `program_year_director`
    ADD CONSTRAINT `fkey_program_year`
        FOREIGN KEY (`program_year_id`)
        REFERENCES `program_year` (`program_year_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    ADD CONSTRAINT `fkey_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON UPDATE RESTRICT ON DELETE CASCADE
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
        // remove the foreign key constraints
        $sql = "ALTER TABLE `program_year_director` DROP FOREIGN KEY `fkey_program_year`, DROP FOREIGN KEY `fkey_user`";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
