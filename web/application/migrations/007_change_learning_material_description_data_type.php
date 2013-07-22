<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Expands the "description" column by changing its data type from VARCHAR(512) to TEXT.
 */
class Migration_Change_learning_material_description_data_type extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        $sql =<<<EOL
ALTER TABLE `learning_material`
    CHANGE COLUMN `description` `description` TEXT NOT NULL COLLATE 'utf8_unicode_ci'
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
        $sql =<<<EOL
ALTER TABLE `learning_material`
    CHANGE COLUMN `description` `description` VARCHAR(512) NOT NULL COLLATE 'utf8_unicode_ci'
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
