<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates a table for storing API keys to the db schema.
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
DROP TABLE IF EXISTS `api_key`;
CREATE TABLE `api_key` (
    `user_id` INT(10) UNSIGNED NOT NULL,
    `api_key` VARCHAR(64) NOT NULL COLLATE 'utf8_unicode_ci',
    PRIMARY KEY (`user_id`),
    UNIQUE INDEX `api_key` (`api_key`),
    CONSTRAINT `fk_api_key_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;
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
        $sql = "DROP TABLE `api_key`";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
