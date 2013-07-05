<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds a foreign key constraint on the "competency_id" column to the "objective" table.
 */
class Migration_Add_Foreign_key_to_objective_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        // change the competency id from "0" to NULL
        $sql = "UPDATE `objective` SET `competency_id` = NULL WHERE `competency_id` = 0";
        $this->db->query($sql);
        // change the competency id to NULL for orphans.
        $sql = "UPDATE `objective` SET `competency_id` = NULL WHERE `competency_id` NOT IN (SELECT `competency_id` FROM `competency`)";
        $this->db->query($sql);

        // add the foreign key constraint
        $sql =<<<EOL
ALTER TABLE `objective`
    ADD CONSTRAINT `fkey_objective_competency`
    FOREIGN KEY (`competency_id`)
    REFERENCES `competency` (`competency_id`)
    ON UPDATE RESTRICT ON DELETE RESTRICT
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
        // remove the foreign key constraint
        $sql = "ALTER TABLE `objective` DROP FOREIGN KEY `fkey_objective_competency`";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
