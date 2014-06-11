<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates a table for storing metadata for sequence block sessions.
 * Currently stores the boolean count_offerings_once
 */
class Migration_Create_curriculum_sequence_inventory_block_session_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<< EOL
CREATE TABLE `curriculum_inventory_sequence_block_session` (
  `sequence_block_session_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sequence_block_id` INT(10) UNSIGNED NOT NULL,
  `session_id` INT(14) UNSIGNED NOT NULL,
  `count_offerings_once` TINYINT default 1 NOT NULL,
  PRIMARY KEY (`sequence_block_session_id`),
  CONSTRAINT `fkey_ci_sequence_block_session_sequence_block_id`
      FOREIGN KEY (`sequence_block_id`) REFERENCES `curriculum_inventory_sequence_block` (`sequence_block_id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fkey_curriculum_inventory_sequence_block_session_session_id`
      FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`)
      ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE INDEX `report_session` (`sequence_block_id`, `session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
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
        $sql = "DROP TABLE `curriculum_inventory_sequence_block_session`";
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
