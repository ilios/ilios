<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) for the "department" table.
 */
class Department extends Abstract_Ilios_Model
{

    public function __construct ()
    {
        parent::__construct('department', array('department_id'));
    }

    /**
     * @return a non-associative array of department objects, each object being an associative array
     *              with keys 'department_id' and 'title'. departments which have their deleted bit
     *              set will not be returned.
     */
    public function getDepartmentsForSchoolId ($schoolId)
    {
        $rhett = array();

        $this->db->where('deleted', 0);
        $this->db->where('school_id', $schoolId);
        $this->db->order_by('title', 'desc');

        $queryResults = $this->db->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            $model = array();

            $model['department_id'] = $row['department_id'];
            $model['title'] = $row['title'];

            array_push($rhett, $model);
        }

        return $rhett;
    }

}
