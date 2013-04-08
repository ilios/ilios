<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_sequence_block" table.
 */
class Curriculum_Inventory_Sequence_Block extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_sequence_block', array('curriculum_inventory_sequence_block_id'));
    }

    /**
     * Retrieves the sequence blocks associated with a given program year.
     * @param int $programYearId the program year id.
     * @return array an associative array of sequence blocks, keyed off by sequence block id
     */
    public function getBlocks ($programYearId)
    {
        $rhett = array();
        $query = $this->db->get_where($this->databaseTableName, array('program_year_id' => $programYearId));
        if (0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $rhett[$row['sequence_block_id']] = $row;
            }
        }
        $query->free_result();
        return $rhett;
    }
}