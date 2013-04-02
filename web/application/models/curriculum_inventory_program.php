<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_program" table.
 */
class Curriculum_Inventory_Program extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_program', array('program_year_id'));
    }

    /**
     * Creates a new curriculum inventory program record for a given program year id.
     * @param int $programYearId the ilios program year id.
     * @return int the program year id
     */
    public function create ($programYearId)
    {
        $data = array();
        $data['program_year_id'] = $programYearId;
        return $this->db->insert($this->databaseTableName, $data);
    }
}