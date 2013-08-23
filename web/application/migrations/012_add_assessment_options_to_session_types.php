<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Creates options for assessment methods ("session types") and maps them to existing session type records as applicable.
 */
class Migration_add_assessment_options_to_session_types extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();

        // create the assessment_options table
        $sql =<<<EOL
CREATE TABLE `assessment_option` (
    `assessment_option_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(20) NOT NULL,
    PRIMARY KEY (`assessment_option_id`),
    UNIQUE INDEX `name` (`name`)
)
DEFAULT CHARSET='utf8'
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
EOL;
        $this->db->query($sql);

        // add column/fkey to session_type that references assessment_option
        $sql =<<<EOL
ALTER TABLE `session_type`
ADD COLUMN `assessment_option_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `assessment`,
ADD CONSTRAINT `assessment_option_fkey`
    FOREIGN KEY (`assessment_option_id`) REFERENCES `assessment_option` (`assessment_option_id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
EOL;
        $this->db->query($sql);

        // populate assessment_options table
        $this->db->query("INSERT INTO `assessment_option` (`assessment_option_id`, `name`) VALUES (1, 'summative')");
        $this->db->query("INSERT INTO `assessment_option` (`assessment_option_id`, `name`) VALUES (2, 'formative')");

        // default all assessment methods to be "summative"
        $this->db->query("UPDATE `session_type` SET `assessment_option_id` = 1 WHERE `assessment` = 1");

        $this->db->trans_complete();
    }


    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        $this->db->query('ALTER TABLE `session_type` DROP FOREIGN KEY `assessment_option_fkey`');
        $this->db->query('ALTER TABLE `session_type` DROP COLUMN `assessment_option_id`');
        $this->db->query('DROP TABLE `assessment_option`');
        $this->db->trans_complete();
    }
}
