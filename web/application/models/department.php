<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once "ilios_base_model.php";

/**
 * Data Access Object (DAO) for the "department" table.
 */
class Department extends Ilios_Base_Model
{
    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct('department', array('department_id'));
    }

    /**
     * Retrieves a list of all departments in a given school.
     * Departments flagged as "deleted" are excluded.
     *
     * @param int $schoolId The school id.
     * @return array An array of assoc. arrays. Each item is representing a department, with keys "department_id" and "title".
     */
    public function getDepartmentsForSchoolId ($schoolId)
    {
        $rhett = array();

        $this->db->where('deleted', 0);
        $this->db->where('school_id', $schoolId);
        $this->db->order_by('title', 'desc');

        $query = $this->db->get($this->databaseTableName);
        foreach ($query->result_array() as $row) {
            $model = array();

            $model['department_id'] = $row['department_id'];
            $model['title'] = $row['title'];

            $rhett[] = $model;
        }

        $query->free_result();
        return $rhett;
    }
}
