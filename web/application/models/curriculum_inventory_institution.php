<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "curriculum_inventory_institution" table.
 */
class Curriculum_Inventory_Institution extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('curriculum_inventory_institution', array('curriculum_inventory_institution_id'));
    }

    /**
     * Retrieves institutional data for a given school.
     * @param $schoolId the school id.
     * @return array|boolean the institutional data as assoc. array or FALSE if none was found
     */
    public function getBySchoolId ($schoolId)
    {
        $rhett = false;
        $query = $this->db->get_where($this->databaseTableName, array('school_id' => $schoolId), 1);
        if (0 < $query->num_rows())
        {
            $rhett = $query->result_array();
        }
        return $rhett;
    }
}