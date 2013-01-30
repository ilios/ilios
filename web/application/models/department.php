<?php

include_once "abstract_ilios_model.php";

/**
 * Data Access Object (DAO) for the "department" table.
 */
class Department extends Abstract_Ilios_Model
{

    public function __construct ()
    {
        parent::__construct('department', array('department_id'));

        $this->createDBHandle();
    }

    /**
     * @return a non-associative array of department objects, each object being an associative array
     *              with keys 'department_id' and 'title'. departments which have their deleted bit
     *              set will not be returned.
     */
    public function getDepartmentsForSchoolId ($schoolId)
    {
        $rhett = array();

        $DB = $this->dbHandle;

        $DB->where('deleted', 0);
        $DB->where('school_id', $schoolId);
        $DB->order_by('title', 'desc');

        $queryResults = $DB->get($this->databaseTableName);
        foreach ($queryResults->result_array() as $row) {
            $model = array();

            $model['department_id'] = $row['department_id'];
            $model['title'] = $row['title'];

            array_push($rhett, $model);
        }

        return $rhett;
    }

}
