<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds a column for storing AAMC issued codes to the "competency" table.
 */
class Migration_add_aamc_code_column_to_competency_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<<EOL
ALTER TABLE `competency`
ADD COLUMN `aamc_code` VARCHAR(20) NULL DEFAULT NULL AFTER `owning_school_id`;
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
        $this->db->query("ALTER TABLE `competency` DROP COLUMN `aamc_code`;");
        $this->db->trans_complete();
    }
}