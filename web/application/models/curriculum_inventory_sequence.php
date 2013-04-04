<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_sequence" table.
 */
class Curriculum_Inventory_Sequence extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_sequence', array('program_year_id'));
    }

    /**
     * Creates a new sequence.
     * @param int $programYearId
     * @param string $description
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function create ($programYearId, $description = '')
    {
        $data = array();
        $data['program_year_id'] = $programYearId;
        $data['description'] = $description;
        return $this->db->insert($this->databaseTableName, $data);
    }

    /**
     * Updates a sequence with the given data.
     * @param int $programYearId the program year id
     * @param string $description the sequence description
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function update ($programYearId, $description = '')
    {
        $data = array();
        $data['description'] = $description;
        $this->db->where('program_year_id', $programYearId);
        return $this->db->update($this->databaseTableName, $data);
    }
}