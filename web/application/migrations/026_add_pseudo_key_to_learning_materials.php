<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds a 'token' column to the 'learning_material' table and backfills it with unique, non-guessable value.
 */
class Migration_Add_pseudo_key_to_learning_materials extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up()
    {
        $this->db->trans_start();

        // add a new column "token"
        $sql ="ALTER TABLE `learning_material` ADD COLUMN `token` CHAR(32) COLLATE utf8_unicode_ci DEFAULT NULL";
        $this->db->query($sql);

        // create a unique index on "token"
        $sql = "CREATE UNIQUE INDEX `idx_learning_material_token_unique` ON `learning_material` (`token`)";
        $this->db->query($sql);

        //
        // generate a new token for each existing learning material
        //
        $sql = "SELECT `learning_material_id` FROM `learning_material`";
        $query = $this->db->query($sql);
        $ids = false;
        if ($query->num_rows()) {
            $ids = $query->result_array();
        }
        $query->free_result();
        if ($ids) {
            foreach ($ids as $id) {
                /* @todo
                $token = Ilios_LearningMaterialsUtils::generateToken($id);
                $this->query("UPDATE `learning_material` SET `token` = {$token} WHERE learning_material_id = {$id}");
                */
            }
        }
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */

    public function down()
    {
        // remove audit_atom table.
        $this->db->trans_start();

        // drop the index
        $sql = 'ALTER TABLE `learning_material` DROP INDEX `idx_learning_material_token_unique`';
        $this->db->query($sql);

        // drop the column
        $sql = 'ALTER TABLE `learning_material` DROP COLUMN `token`';
        $this->db->query($sql);

        $this->db->trans_complete();
    }
}
