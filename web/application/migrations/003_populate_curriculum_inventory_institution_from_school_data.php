<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * As the class-name says...
 * Fill the curriculum_inventory_institution table with records for each entry in the school table.
 */
class Migration_populate_curriculum_inventory_institution_from_school_data extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        $sql =<<<EOL
INSERT INTO `curriculum_inventory_institution` (
  `school_id`, `name`, `aamc_id`, `address_street`, `address_city`,
  `address_state_or_province`, `address_zipcode`, `address_country_code`)
( SELECT `school_id`, `title`, '00000', '', '', '', '', '' FROM `school`)
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
        $this->db->query("DELETE FROM `curriculum_inventory_institution`");
        $this->db->trans_complete();
    }
}