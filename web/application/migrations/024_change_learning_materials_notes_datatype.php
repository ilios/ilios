<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Changes the learning materials' 'Notes' fields from VARCHAR(500) to TEXT
 * in order to release limit on characters entered.
 */
class Migration_Change_learning_materials_notes_datatype extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up()
    {
        //change `session_learning_material`.`notes` from type 'VARCHAR(500)' to type 'TEXT'
        $this->db->trans_start();
        $this->db->query('ALTER TABLE `session_learning_material` CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL');
        $this->db->trans_complete();

        //and the same for the `course_learning_material` table
        $this->db->trans_start();
        $this->db->query('ALTER TABLE `course_learning_material` CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL');
        $this->db->trans_complete();

    }

    /**
     * @see CI_Migration::down()
     */ 
    public function down()
    {
        //change `session_learning_material`.`notes` type from 'TEXT' back to its original 'VARCHAR(500)' type
        $this->db->trans_start();
        $this->db->query('ALTER TABLE `session_learning_material` CHANGE `notes` `notes` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL');
        $this->db->trans_complete();

        //and the same for the `course_learning_material` table
        $this->db->trans_start();
        $this->db->query('ALTER TABLE `course_learning_material` CHANGE `notes` `notes` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL');
        $this->db->trans_complete();
    }
}
