<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds foreign key constraint on user::primary_school_id.
 */
class Migration_Add_foreign_key_constraint_to_user_primary_school_id extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        // first off, we must change the data type of the "primary_school_id" column to match the type of the referenced column
        $sql = 'ALTER TABLE `user` CHANGE COLUMN `primary_school_id` `primary_school_id` INT(10) UNSIGNED NOT NULL';
        $this->db->query($sql);
        // add the foreign key
        $sql =<<<EOL
ALTER TABLE `user`
    ADD CONSTRAINT `fkey_user_primary_school`
        FOREIGN KEY (`primary_school_id`)
        REFERENCES `school` (`school_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
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
        $sql = 'ALTER TABLE `user` DROP FOREIGN KEY `fkey_user_primary_school`';
        $this->db->query($sql);
        // revert data type of the "primary_school_id" column
        $sql = 'ALTER TABLE `user` CHANGE COLUMN `primary_school_id` `primary_school_id` TINYINT(10) SIGNED NOT NULL';
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
