<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds a column for storing API keys to the authentication table.
 */
class Migration_Add_api_key_column_to_auth_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<< EOL
ALTER TABLE `authentication`
ADD COLUMN `api_key` VARCHAR(64) NULL DEFAULT NULL AFTER `person_id`,
ADD UNIQUE INDEX `api_key` (`api_key`);
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
        $sql =<<< EOL
ALTER TABLE `authentication`
DROP COLUMN `api_key`,
DROP INDEX `api_key`;
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
