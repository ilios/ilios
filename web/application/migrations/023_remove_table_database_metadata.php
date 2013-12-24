<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Removes the obsolete table "database_metadata".
 */
class Migration_Remove_table_database_metadata extends CI_Migration
{

    public function up()
    {
        $this->db->trans_start();
        $this->db->query('DROP TABLE `database_metadata`');
        $this->db->trans_complete();

    }

    public function down()
    {
        $this->db->trans_start();
        $sql =<<<EOL
CREATE TABLE `database_metadata` (
    `database_metadata_id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
    `time_stamp` TIMESTAMP NOT NULL,
    `mesh_release_version` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL,
    `last_som_feed` TIMESTAMP NOT NULL,
    `last_sis_feed` TIMESTAMP NOT NULL,
    `last_cp_feed` TIMESTAMP NOT NULL,
    PRIMARY KEY (`database_metadata_id`) USING BTREE
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
