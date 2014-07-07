<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds created_at and updated_at timestamps to mesh data tables
 */
class Migration_Add_Mesh_Timestamps extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $queries = array();
        foreach($this->getTables() as $table){
            /*
                Becuase of a mysql issues not fixed until 5.6.5 http://dev.mysql.com/doc/relnotes/mysql/5.6/en/news-5-6-5.html
                we cannot have both a create_at and updated_at column which update automatically, however if it is set to NULL
                on insert CURRENT_TIMESTAMP will still be used, so we set created_at to NULL and updated_at updates itself
            */
            $queries[] = "ALTER TABLE {$table} ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'";
            $queries[] = "ALTER TABLE {$table} ADD COLUMN `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
            $queries[] = "UPDATE {$table} SET created_at = '2010-01-01 00:00:00', updated_at = '2010-01-01 00:00:00'";
        }
        $this->db->trans_start();
        foreach($queries as $sql){
            $this->db->query($sql);
        }
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $queries = array();
        foreach($this->getTables() as $table){
            $queries[] = "ALTER TABLE {$table} DROP COLUMN `created_at`";
            $queries[] = "ALTER TABLE {$table} DROP COLUMN `updated_at`";
        }
        $this->db->trans_start();
        foreach($queries as $sql){
            $this->db->query($sql);
        }
        $this->db->trans_complete();
    }

    /**
     * get the tables we are altering
     * @return array
     */
    private function getTables()
    {
        $tables = array(
            'mesh_concept',
            'mesh_descriptor',
            'mesh_qualifier',
            'mesh_semantic_type',
            'mesh_term'
        );
        return $tables;
    }
}
